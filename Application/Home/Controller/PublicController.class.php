<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
use OT\DataDictionary;
use Think\AjaxUpload;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class PublicController extends \Think\Controller {

	public function _initialize() {
		$config = api('Config/lists');
		C($config);

		if(!C('WEB_SITE_CLOSE')){
			$this->error('站点已经关闭，请稍后访问~');
		}

		$key = "418831400";
		$redirect_uri = "http://www.test2.test/Home/Public/weblogin?";
		//授权后将页面重定向到本地项目
		$redirect_uri = urlencode($redirect_uri);
		$wb_url = "https://api.weibo.com/oauth2/authorize?client_id={$key}&response_type=code&redirect_uri={$redirect_uri}";
		$this -> assign('web_url',$wb_url);

	}
	public function task(){
		$ord['status'] = array('gt',0);
		$ord['sid'] = array('gt',0);
		$ord['is_transfer'] = array('eq',0);
		$ord['is_confirm'] = array('eq',1);
	
		$day=C('WEB_TRANSFER_DAY');
		$day = $day>0? $day : 15;
		
		$ord['update_time'] = array('lt', time() - $day*24*3600 );

		$orders = M('order')->where($ord)->select();
		foreach($orders as $k=>$v) {
			M("order")->where("id=".$v[id])->save(array('is_transfer'=>1));

			$bili=bili($v[type]);
			$money= $v[pay_price]*(100-$bili)/100;
			
			$user=M('staff')->find($v['sid']);
			M("staff")->where("id=".$user[id])->save(array('balance'=>$user[balance]+$money));
		}
	}
	
	public function apply_Crowd_funding(){
		$info=M('zc')->find($_GET['id']);
		$info[ajid]=M('ajcate')->find($info[ajid])[name];
		$info[iczm]=picture($info[iczm]);
		$info[icfm]=picture($info[iczm]);
		$info[icon]=expic($info[icon]);
		$info[file]=expic($info[file]);
		$info[lasttime]=date('Y-m-d　H:i',$info[lasttime]);
		$this->assign('vo',$info);
		
		$this->display();}
	
	public function apply_mutualAid(){
		$info=M('hz')->find($_GET['id']);
		$info[ajid]=M('ajcate')->find($info[ajid])[name];
		$info[iczm]=picture($info[iczm]);
		$info[icfm]=picture($info[iczm]);
		$info[icon]=expic($info[icon]);
		$info[file]=expic($info[file]);
		$this->assign('vo',$info);
		
		$this->display();}
	
	public function yhxy(){$this->display();}
	public function login(){
		session('usertype','user');
		S('usertype','user');
		if(!empty(session('password')) || !empty(session('password')) ){
			$this->assign('phone',session('phone'));
			$this->assign('password',session('password'));
		}
		$this->display('User/login');
	}
	public function login_account(){
		session('usertype','user');
		S('usertype','user');
		$this->assign('usertype',session('usertype'));
		if(!empty(session('password')) || !empty(session('password')) ){
			$this->assign('phone',session('phone'));
			$this->assign('password',session('password'));
		}
		
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			
			$this -> assign('is_weixin',1);
			
		}
		
		
		$this->display('User/login_account');
	}
	public function register(){
		session('usertype','user');
		S('usertype','user');
		$this->display('User/register');
	}
	
	public function test_refun(){
		
		$map['orderno'] = array('eq','190507955741');
		$order = M('order')->where($map)->find();
		var_dump($order);
		$res = D('App/Pay')->refund($order);
		
		var_dump($res);
		
	}
	public function waplogin()
	{
		$post=$_POST;

		$arr['phone'] = array('eq', $post['phone']);
		
		if(!empty($post['password'])){
			$arr['password'] = array('eq',$post['password']);
		}else{
			$arr['three'] = array('eq',0);
		}
		if($post['yzm']){
			if(S($post['phone'])!=$post['yzm']){
				exit(json_encode( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data)));
			}
		}

		if ($post['type'] == 1) {
			
			$result = M('usermember')->where($arr)->find();
			$sql = M('usermember')->getLastSql();
			if ($result) {
				$token = md5($result['id'] . noncestr());
				$result['type'] = 1;
				$result['token'] = $token;
				$result['icon'] = picture($result['icon']);

				$result['fee'] = $this->fee();

				session('user',$result);
				session('user_type',1);
				session('phone',$post['phone']);
				if($post['remember']==1){
					session('password',$post['password']);
				}
				$save = M('usermember')->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
				exit(json_encode( array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result)));
			}else{
				
				if(!empty($post['password'])){
					
					exit(json_encode( array('status' => true, 'msg' => '账号密码错误', 'code' => 201, 'data' => $result,'sql'=>$sql)));
					
				}else{
					
					exit(json_encode( array('status' => true, 'msg' => '当前手机号未注册，注册后即可用短信验证码登录!', 'code' => 201, 'data' => $result,'sql'=>$sql)));
					
				}

			}

		}elseif ($post['type'] == 2) {
			if(!M('staff')->where("phone=".$post['phone'])->find()){
				exit(json_encode(  array('status' => true, 'msg' => '您尚未注册,去注册 !', 'code' => 201, 'data' => $result)));
			}

			if ($result = M('staff')->where($arr)->find()) {
				$token = md5($result['id'] . noncestr());
				$result['type'] = 2;
				$result['token'] = $token;
				$result['icon'] = picture($result['icon']);
				$result['fee'] = $this->fee();
				session('user',$result);
				session('user_type',2);
				session('phone',$post['phone']);
				if($post['remember']==1){
					session('password',$post['password']);
				}
				$save = M('staff')->where(array('id' => $result['id']))->save(array('token' => $token));
				exit(json_encode( array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result)));
			} else {
				exit(json_encode(  array('status' => true, 'msg' => '密码错误 !', 'code' => 201, 'data' => $result)));
				
			}

		}


	}
	public function fee(){
		$result=M('Feept')->select();
		return $result;
	}
	public function wapreg()
	{
		$post=$_POST;

		$arr['phone'] = array('eq', $post['phone']);

		if($post['yzm']){
			if(S($post['phone'])!=$post['yzm']){
				exit(json_encode( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data)));
			}
		}

		if ($post['type'] == 1) {
			if ($result = M('usermember')->where($arr)->find()) {
				exit(json_encode( array('status' => true, 'msg' => '该手机号已注册', 'code' => 201, 'data' => $result)));
			}else{
				
				
				if($post['idno']){
					
					$pid = M('usermember')->where("idno=".$post['idno'])->find();
					
                    if(!$pid){
                        exit(json_encode( array('status' => true, 'msg' => '推荐码不存在', 'code' => 201, 'data' => $result)));
                    }
					$add['pid'] = $pid['id'];
					
					M('usermember')->where("id=".$pid['id'])->save(array('reward'=>$pid['hz']+C('WEB_YAOQIN')));
				}
				$add['nickname'] = $post['phone'];
				$add['phone'] = $post['phone'];
				$add['password'] = $post['password'];
				$add['idno'] = str_shuffle(time());
				$add['create_time'] = time();
				$add['date_time'] = date('Y-m');

				$re = M('usermember')->add($add);
				if ($re) {
					$result = M('usermember')->find($re);
					$token = md5($re . noncestr());
					$result['type'] = 1;
					$result['token'] = $token;
					$result['icon'] = picture($result['icon']);

					$ewm=$this->erweim(1,$re);

					session('user',$result);
					session('user_type',1);
					session('phone',$post['phone']);
					session('password',$post['password']);



					$save = M('usermember')->where(array('id' => $re))->save(array('token' => $token,'ewm' => $ewm));
					exit(json_encode(  array('status' => true, 'msg' => '注册成功', 'code' => 200, 'data' => $result)));
				} else {
					exit(json_encode(  array('status' => true, 'msg' => '注册失败', 'code' => 201, 'data' => $result)));
				}

			}

		}elseif ($post['type'] == 2) {
			$user = M('usermember')->where($arr)->find();
			if($user){
				//exit(json_encode( array('status' => true, 'msg' => '该手机号已在用户端注册', 'code' => 201, 'data' => $result)));
			}
			
			if (M('staff')->where($arr)->find()) {
				exit(json_encode( array('status' => true, 'msg' => '该手机号已被注册', 'code' => 201, 'data' => $result)));
			} else {
				if($post['idno']){
					
					$pid = M('usermember')->where("idno=".$post['idno'])->find();
					
                    if(!$pid){
                        exit(json_encode( array('status' => true, 'msg' => '推荐码不存在', 'code' => 201, 'data' => $result)));
                    }
					$add['pid'] = $pid['id'];
					
					M('staff')->where("id=".$pid[id])->save(array('reward'=>$pid['hz']+C('WEB_YAOQIN')));
				}
				$add['nickname'] = $post['phone'];
				$add['phone'] = $post['phone'];
				$add['password'] = $post['password'];
				$add['idno'] = str_shuffle(time());
				$add['create_time'] = time();
				$add['date_time'] = date('Y-m');
				$re = M('staff')->add($add);
				if ($re) {
					$result = M('staff')->find($re);
					$token = md5($re . noncestr());
					$result['type'] = 2;
					$result['token'] = $token;
					$result['icon'] = picture($result['icon']);

					$ewm=$this->erweim(2,$re);

					session('user',$result);
					session('phone',$post['phone']);
					session('user_type',2);
					session('password',$post['password']);
					
					$save = M('staff')->where(array('id' => $re))->save(array('token' => $token,'ewm' => $ewm));
					exit(json_encode(  array('status' => true, 'msg' => '注册成功', 'code' => 200, 'data' => $result)));
				} else {
					exit(json_encode(  array('status' => true, 'msg' => '注册失败', 'code' => 201, 'data' => $result)));
				}
			}

		}


	}
	public function aaa(){
		$result=M('staff')->select();
		foreach($result as $k=>$v){
			$ewm=$this->erweim(2,$v[id]);
			M('staff')->where('id='.$v[id])->save(array('ewm'=>$ewm));

		}
	}
	public function erweim($type,$id){
		header("Content-type: text/html; charset=utf-8");
		if($type==1){
			$user = M('usermember')->find($id);
		}else{
			$user = M('staff')->find($id);
		}


		$path    = 'Uploads/qrcode/'.date('Ymd').'/';
		$imgname = $user[idno];
		if($type==1){
			$url     = C('WEB_SITE_URL').'/home/user/register?uniqueid='.$user[idno];
		}else{
			$url     = C('WEB_SITE_URL').'/home/lawyer/register?uniqueid='.$user[idno];
		}


		$this->qrcode($url,$path,$imgname);
		//存入二维码路径
		$imgpath = '/'.$path.$imgname.'.png';

		return $imgpath;
	}
	public function qrcode($data,$path,$imgname,$level=3,$size=4){

		Vendor('Phpqrcode.phpqrcode');

		$level = 'L';
		// 点的大小：1到10,用于手机端4就可以了
		$size = 15;

		if(!file_exists($path))
		{
			mkdir($path, 0777);
		}
		//  生成的文件名
		$fileName = $path.$imgname.'.png';
		ob_end_clean();//清空缓冲区
		$object = new \QRcode();
		$object->png($data,$fileName,$level, $size);

	}
	/*h5注册*/
	public function reg(){
        if(IS_POST) {
            $post=I('post.');
            $username = $post['username'];
            $result = M('usermember')->where(array('username' => $username))->find();
            if ($result) {
                exit(json_encode(array('code' => 402, 'msg' => '该账户已经被注册!')));
            } else {
                //$yzm = S($phone); 
                if ($yzm != $post['yzm']) {
                    return array('status' => false, 'msg' => '验证码错误', 'code' => 201, 'data' => $data);
                } else {
                    /*if (!empty($post[idno])) {
                        $re = M('usermember')->where(array('idno' => $post[idno]))->find();
                        if ($re) {
                            $post['pid'] = $re['id'];
                        } else {
                            exit(json_encode(array('code' => 400, 'msg' => '推荐人ID号错误')));
                        }

                    }*/
                    if (!empty($post[id])) {
                        $re = M('fxcontent')->where(array('id' => $post[id]))->find();
                        if ($re) {
                            $post['pid'] = $re['uid'];
                        } else {
                            exit(json_encode(array('code' => 400, 'msg' => 'ID参数错误')));
                        }

                    }

                    $post['password'] = $post['password'];
                    //$post['password'] = md5(md5($post['password']));
                    $post['idno'] = str_shuffle(time());
                    $post['create_time'] = time();

                    $add = M("usermember")->add($post);
                    if ($add) {
                        //S($phone,null);
                        $data = M('usermember')->field('id')->find($add);
                        exit(json_encode(array('code' => 200, 'msg' => '注册成功,您可下载意达订货APP试用24小时')));
                    } else {
                        exit(json_encode(array('code' => 400, 'msg' => '注册失败')));
                    }
                }

            }
        }

	}
	
    public function checklogin(){
        if(IS_POST){ 

            $list=I('post.');
			$whe['username']=$list['username'];
            $whe['password']=$list['password'];
            $re=M('usermember')->where($whe)->find();
            if($re){

				if($re['state']==0){
					exit(json_encode(array('code'=>400,'msg'=>'您已被禁用，请联系系统管理员')));
				}
                session('user',$re['id']);
                session('user_info',$re);
                exit(json_encode(array('code'=>200,'msg'=>'登陆成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'用户名或密码错误')));
            }
        }
    }
	/*退出*/
	public function logout(){
		session('user',null);
		session('user_info',null);
		exit(json_encode(array('code'=>200,'msg'=>'退出成功')));
		//$this->redirect('/Home/Public/login_account');
	}
	public function logout1(){
		session('user',null);
		session('user_info',null);
		$this->redirect('/Home/Lawyer/login');
	}
	public function verify(){
		$verify = new \Think\Verify();
		$verify->length   = 4;
		$verify->entry(1);
	}
   
    //获取注册短信验证码  注册模板号 SMS_137895102  修改密码 SMS_137895101 
    public function sms(){
        if(empty($_POST['phone'])){
            exit(json_encode(array('code'=>400,'msg'=>'手机号码不能为空')));
        }
        $pt=$_POST;
        $phone=$_POST['phone'];
         //type发送类型  1:注册  2短信登录 3修改密码  4修改支付密码  5修改手机号
        if($pt['type'] == 1){
            //判断该手机号是否已经注册过
            if(M('usermember')->where("phone=".$phone)->find()){
                exit(json_encode(array('code'=>400,'msg'=>'该手机号已被注册！')));
            }
            $sms = "SMS_137895102 ";
            $verify_type = 'verify';
        }elseif ($pt['type'] == 2 ){
            $sms = "";
        }elseif ($pt['type'] == 3 ){
            
            $sms = "SMS_137895101";
           
        }elseif ($pt['type'] == 4 ){
            $sms = "";
        }elseif ($pt['type'] == 5 ){
            if(M('usermember')->where("phone=".$phone)->find()){
                exit(json_encode(array('code'=>400,'msg'=>'该手机号已被注册！')));
            }
            $sms = "SMS_137895102";
        }

        $codes = rand(1000,9999);
        $run = AliyunSendMsg($phone,$sms,$codes);
        if($run[Code]=='OK'){
            session($phone,$codes);
            exit(json_encode(array('code'=>200,'msg'=>'短信已发送至'.substr($_POST['phone'],-4))));
        }else{
            exit(json_encode(array('code'=>400,'msg'=>$run['Message'])));
        }
    }
	public function forget(){
		if (IS_POST) {
			$list = I('post.');


			$re = M('usermember')->where("phone=" . $list['phone'])->find();
			if (!$re) {
				exit(json_encode(array('code' => 400, 'msg' => '用户不存在')));
			}
			if (session($re['phone']) != $list['code']) {
				exit(json_encode(array('code' => 402, 'msg' => '验证码错误')));
			}
			$save=M('usermember')->where("phone=".$re['phone'])->save(array('password'=>$list['newpass']));
            

			if ($save) {
				exit(json_encode(array('code' => 200,'msg'=>'修改成功' )));
			} else {
				exit(json_encode(array('code' => 400, 'msg'=>'修改失败')));
			}
		}else{
		    $this -> assign('get',$_GET);
            $this->display();
        }
	}

	//微信登录
	public function wx_login(){
		//$AppID     = C('WX_LOGIN_APPID');
		//$AppSecret = C('WX_LOGIN_APPSECRET');
        $AppID     = 'wxad41fc80c23c0208';
        $AppSecret = '9d8f71069e5fcabe8f4cf7b34f3cc68d';

		$callback  =  'http://www.test2.test/Home/public/callback'; //回调地址

		//-------生成唯一随机串防CSRF攻击
		$state  = md5(uniqid(rand(), TRUE));
		session('wx_state',$state);//存到SESSION
		$callback = urlencode($callback);

		$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri=".$callback."&response_type=code&scope=snsapi_login&state=".$state."#wechat_redirect";
		header("Location: $wxurl");


	}

	//微信回调地址
	public function callback(){
        $AppID     = 'wxad41fc80c23c0208';
        $AppSecret = '9d8f71069e5fcabe8f4cf7b34f3cc68d';

		$info = D('Login')->wx_login($AppID,$AppSecret,$_GET['state'],$_GET['code']); //获取用户信息，参数必传


		$where['openid'] = array('eq',$info['openid']);
		//$where['status'] = array('eq',1);
		$res = M('usermember')->where($where)->find();
		if(!empty($res)){
            session('user',$res['id']);
			session('sdw_users_id',$res['id']);
			if(ismobile()){
				$this->redirect('/Wap/Index/index');
			}else{
				$this->redirect('/Home/Index/index');
			}
		}else{
            $data['openid']=$info['openid'];
            $data['name']=$info['nickname'];
			$data['reg_time']=time();
            $add = M('usermember')->add($data);
            if($add){
                session('user',$add);
                session('three_login_type','weixin');//向前台传递三方登录类型
                $this->redirect('Index/index');
            }
			// //绑定账号*/
		}

	}

	//QQ登录入口 APP ID：101550279 APP Key：c52a9e77fbc94690bd10d41ee0c45421
	public function qq_login(){
		$app_id = C('QQ_LOGIN_APPID');
		$app_id =101550279;
		$scope = 'get_user_info,get_repost_list,add_idol,add_t,del_t,add_pic_t,del_idol';
		$callback = 'http://www.test2.test/home/public/qqlogin';

		$sns = new \Common\Api\QQConnect;
		$sns->login($app_id, $callback, $scope);
	}
	//qq登录回调地址
	public function qqlogin(){
		$callback = 'http://www.test2.test/home/public/qqlogin';
		$appid=101550279;
		$appkey='c52a9e77fbc94690bd10d41ee0c45421';

		$info = D('Login')->qq_userinfo($_REQUEST['code'],$_REQUEST['state'],$appid,$appkey,$callback);
		$info['icon']=$info['figureurl'];
		session('three_type','qq');
		session('three_info',$info);

		$this->threelogin($info);

	}
	public function weblogin(){

		$key = "418831400";
		//接收code值
		$code = I('get.code');
		//换取Access Token： post方式请求  替换参数: client_id, client_secret,redirect_uri， code
		$secret = "d22ddc37a68ffae9e8b1376a998a264c";
		$redirect_uri = "http://www.test2.test/Home/Public/weblogin";
		$url = "https://api.weibo.com/oauth2/access_token?client_id={$key}&client_secret={$secret}&grant_type=authorization_code&redirect_uri={$redirect_uri}&code={$code}";
		$token = post($url, array());
		$token = json_decode($token, true);
		//获取用户信息 : get方法，替换参数： access_token， uid
		$url = "https://api.weibo.com/2/users/show.json?access_token={$token['access_token']}&uid={$token['uid']}";
		$info = get($url);
		$info = json_decode($info,true);

		if($info){
			$info['openid']=$info['id'];
			$info['nickname']=$info['name'];
			$info['icon']=$info['profile_image_url'];
			session('three_type','web');
			session('three_info',$info);

			$this->threelogin($info);


		}
	}

	public function loginbang(){
		if(IS_POST){
			$post = $_POST;

			if($post['yzm']){
				if(S($post['phone'])!=$post['yzm']){
					exit(json_encode( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data)));
				}
			}
			if(S('usertype')=='user'){
				$m=M('usermember');
				$type=1;
			}else{
				$m=M('staff');
				$type=2;
			}

			$three_info=session('three_info');
			
			$map = array();
			$map['phone'] = array('eq',$post['phone']);
			
			$res = $m->where($map)->find();
			
			if($res){
				exit(json_encode( array('status'=>false,'msg'=>'当前手机号已存在','code'=>201)));
				
			}

			$me = $m->where("openid='".$three_info['openid']."'")->find();
			$m->where("id=".$me['id'])->save(array('phone'=>$post[phone]));
			
			if( $have = $m->where("phone='".$post[phone]."' and three=0")->find() ){

				$m->where("id=".$me['id'])->save(array('three'=>1));
				if(empty($have[icon])){
					$m->where("id=".$have['id'])->save(array('icon'=>$three_info[icon]));
				}
				if(preg_match("/^1[345678]{1}\d{9}$/",$have[nickname])){
					$m->where("id=".$have['id'])->save(array('nickname'=>$three_info[nickname]));
				}
				$result = $m->where("id=".$have['id'])->find();
				$token = md5($have['id'] . noncestr());

				$result['token'] = $token;
				$result['type'] = $type;

				session('user',$result);
				$save = $m->where(array('id' => $have['id']))->save(array('token' => $token));

			}else{
				$result=$me;

				$token = md5($me['id'] . noncestr());
				if(S('usertype') == 'user'){
					$ewm=$this->erweim(1,$me['id']);
				}else{
					$ewm=$this->erweim(2,$me['id']);
				}
				$result['token'] = $token;
				$result['type'] = $type;

				session('user',$result);
				$save = $m->where(array('id' => $me['id']))->save(array('token' => $token,'ewm' => $ewm));

			}

			if($result){
				exit(json_encode( array('status' => true, 'msg' => '绑定成功', 'code' => 200, 'data' => $result)));
			}else{
				exit(json_encode( array('status' => false, 'msg' => '绑定失败', 'code' => 201, 'data' => $result)));
			}
			/*if(empty($vo) || $vo['status']!=1){
				$this->ajaxReturn(array('code'=>201,'msg'=>'账号不存在或被禁用！'));exit;
			}else{
				if($vo['password']!=mymd5($post['password'])){
					$this->ajaxReturn(array('code'=>201,'msg'=>'密码错误！'));exit;
				}
			}
			$thrinfo = session('wxdj_three_info');
			if(empty($thrinfo)){
				$this->ajaxReturn(array('code'=>201,'msg'=>'获取第三方信息失败！'));exit;
			}
			$data['id'] = $vo['id'];
			if($post['type']=='weixin'){
				//微信
				if($vo['openid']!=''){
					$this->ajaxReturn(array('code'=>201,'msg'=>'该账号已被绑定！'));exit;
				}

				$data['openid']     = $thrinfo['openid'];
				$data['wx_headimg'] = $thrinfo['headimgurl'];
			}else if($post['type']=='qq'){
				//qq
				$data['qq_openid']  = $thrinfo['openid'];
				$data['qq_headimg'] = $thrinfo['figureurl'];
			}

			$res = M('usermember')->save($data);
			if($res){
				session('sdw_users_id',$vo['id']); //登录成功
				$this->ajaxReturn(array('code'=>200,'msg'=>'登录成功！'));
			}else{
				$this->ajaxReturn(array('code'=>201,'msg'=>'登录失败！'));
			}*/


		}else{

			$this->assign('usertype',S('usertype'));

			$this->display();
		}

	}
	public function threelogin($info){
		$where['openid'] = array('eq',$info['openid']);
		//$where['status'] = array('eq',1);
		if(S('usertype') == 'user'){
			$m=M('usermember');
			$type=1;
		}else{
			$m=M('staff');
			$type=2;
		}
		$result = $m->where($where)->find();
		if(!empty($result)){

			if(empty($result[phone])){
				$this->redirect('/home/public/loginbang');exit();
			}
			if( $have = $m->where("phone='".$result[phone]."' and three=0")->find() ){

				$result = $m->where("id=".$have['id'])->find();
				$token = md5($have['id'] . noncestr());
				$result['token'] = $token;
				$result['type'] = $type;

				session('user',$result);
				$save = $m->where(array('id' => $have['id']))->save(array('token' => $token));

			}else{
				$token = md5($result['id'] . noncestr());
				$result['type'] = $type;
				$result['token'] = $token;

				session('user',$result);
				$save = $m->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
			}
			if(S('usertype')=='user'){
				$this->redirect('/home/user/index');
			}else{
				$this->redirect('/home/lawyer/workbench');
			}

		}else{
			$data['openid']=$info['openid'];
			$data['nickname']=$info['nickname'];
			$data['icon']=$info['icon'];
			$data['idno']=str_shuffle(time());

			$data['create_time']=time();
			$data['date_time']=date('Y-m');
			$re = $m->add($data);
			if($re){
				//var_dump($info);die();
				$this->redirect('/home/public/loginbang');exit();
			}
		}
	}

	//获取注册短信验证码  注册模板号 SMS_105215235  修改密码 SMS_105215234 登录确认 SMS_105215237  签名号 115901164
    public function mobileVerify(){

        if(IS_POST){
            $randnum =  session('dx_rand_num');
            $pt = $_POST;
            //验证是否正规操作
            if($pt['rand_num']!=$randnum){
                $msg[status] = 2;
                $msg[msg]    =  '非法操作！';
                $this->ajaxReturn($msg);exit;
            }
            $mobile = $pt[mobile];
            $map['mobile'] = array('eq',$mobile);
            $telResu = M('usermember')->where($map)->find();
            //type发送类型  1:注册  2短信登录 3修改密码  4修改支付密码  5修改手机号
            if($pt['type'] == 1){
                //判断该手机号是否已经注册过
                if(!empty($telResu)){
                    $msg[status] = 2;
                    $msg[msg]    =  '该手机号已被注册！';
                    $this->ajaxReturn($msg);exit;
                }
                $sms = "SMS_105215235";
                $verify_type = 'verify';
            }elseif ($pt['type'] == 2 ){
                //判断用户是否存在
                if(empty($telResu)){
                    $msg[status] = 2;
                    $msg[msg]    =  '该手机号不存在！';
                    $this->ajaxReturn($msg);exit;
                }
                $sms = "SMS_105215237";
                $verify_type = 'verify';
            }elseif ($pt['type'] == 3 ){
                //判断用户是否存在
                if(empty($telResu)){
                    $msg[status] = 2;
                    $msg[msg]    =  '该手机号不存在！';
                    $this->ajaxReturn($msg);exit;
                }
                $sms = "SMS_105215234";
                $verify_type = 'verify';
            }elseif ($pt['type'] == 4 ){
                //判断用户是否存在
                if(empty($telResu)){
                    $msg[status] = 2;
                    $msg[msg]    =  '该手机号不存在！';
                    $this->ajaxReturn($msg);exit;
                }
                $sms = "SMS_105215234";
                $verify_type = 'paypass';
            }elseif ($pt['type'] == 5 ){
                //判断用户是否存在
                if(!empty($telResu)){
                    $msg[status] = 2;
                    $msg[msg]    =  '该手机号已存在！';
                    $this->ajaxReturn($msg);exit;
                }
                $sms = "SMS_105215234";
                $verify_type = 'updateMobile';
            }

            $yzm = rand(100000,999999);

            //阿里云短信接口
            $rt = AliyunSendMsg($pt['mobile'],$sms,$yzm);

            if($rt[Code]=='OK'){
                session($pt[mobile].$verify_type,$yzm);//将短信验证码存入session中
                session('dx_rand_num',null);//清除session
                $msg[status] = 1;
                $msg[msg]    =  '短信发送成功，请注意查收！';
            }else{
                $msg[status] = 2;
                $msg[msg]    =  '短信发送失败！';
            }
            $this->ajaxReturn($msg);exit;
        }else{
            $this->error('非法操作');
        }

    }
	/*手机验证码重置密码*/
	public function phoneresetpassword(){
		if(IS_POST){
			$list=I('post.');
			if(session($list['forgetuname'])!=$list['forgecode']){
				exit(json_encode(array('code'=>402,'msg'=>'验证码错误!')));
			}
			$fidn['uname']=array('eq',$list['forgetuname']);
			$user = M('system_users','jieqi_','DB_CONFIG1')->where($fidn)->find();
			if(empty($user)){
				exit(json_encode(array('code'=>401,'msg'=>'账号不存在!')));
			}else{
				exit(json_encode(array('code'=>200,'msg'=>'验证成功!')));
			}

		}
	}
	/*重置密码*/
	public function resetpassword(){
		if(IS_POST){
			$list=I('post.');

			$fidn['uname']=array('eq',$list['forgetuname']);
			$user = M('system_users','jieqi_','DB_CONFIG1')->where($fidn)->find();
			if($user){
				exit(json_encode(array('code'=>400,'msg'=>'非法操作!')));
			}
			if($user['pass']==md5(md5($list['forgepass']))){
				exit(json_encode(array('code'=>200,'msg'=>'修改成功!')));
			}
			$save['pass']=md5(md5($list['forgepass']));
			$saveuser = M('system_users','jieqi_','DB_CONFIG1')->where('uid='.$user['uid'])->save($save);
			if($saveuser){
				exit(json_encode(array('code'=>200,'msg'=>'修改成功!')));
			}else{
				exit(json_encode(array('code'=>400,'msg'=>'修改失败!')));
			}
		}
	}

	//上传图片
	public function upload333(){

		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		$upload->savePath  =     'Picture/'; // 设置附件上传（子）目录
		// 上传文件
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$arr = array('code'=>201,'msg'=>$upload->getError());
			$this->ajaxReturn($arr);
		}else{// 上传成功
			$path = "/Uploads/".$info['file']['savepath'].$info['file']['savename'];
			$file = file_get_contents('.'.$path);
			$where['sha1'] = array('eq',sha1($file));
			$where['md5'] = array('eq',md5($file));
			$vo = M('picture')->where($where)->find();
			if(!empty($vo)){
				@unlink('.'.$path); //删除图片路径
				$arr = array('code'=>200,'path'=>$vo['path'],'icon'=>$vo['id']);
			}else{
				$data['sha1'] = $info['file']['sha1'];
				$data['md5']  = $info['file']['md5'];
				$data['create_time'] = time();
				$data['path'] = $path;
				$res = M('picture')->add($data);
				if($res){
					$arr = array('code'=>200,'path'=>$path,'icon'=>$res);
				}
			}
			$this->ajaxReturn($arr);
		}
	}
	/*用户头像*/
	public function uploadicon(){
		$id=session('user');

		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   = 3145728 ;// 设置附件上传大小
		$upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  = 'https://move20170828.oss-cn-shanghai.aliyuncs.com/files/system/avatar/';// 设置附件上传根目录
		if(strlen($id)>3){
			$upload->savePath  = substr($id,0,3);
		}else{
			$upload->savePath  = $id;
		}
		$upload->saveName = $id;/*上传文件的名称*/
		// 上传文件
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$arr = array('code'=>201,'msg'=>$upload->getError());
			$this->ajaxReturn($arr);
		}else{// 上传成功

			$arr = array('code'=>200,'path'=>"");
			$this->ajaxReturn($arr);
		}
	}

	public function test(){
		if (!is_dir("./Uploads/Picture/".date('Y-m-d'))){
			mkdir("./Uploads/Picture/".date('Y-m-d'), 0777);
		}

		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		$upload->savePath  =     "Picture/"; // 设置附件上传（子）目录
		// 上传文件
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$arr = array('code'=>201,'msg'=>$upload->getError());
			$this->ajaxReturn($arr);
		}else{// 上传成功
			$icon='';
			foreach ($info as $k=>$v) {
				$path = "/Uploads/".$v['savepath'].$v['savename'];
				//$file = file_get_contents('.'.$path);
				$where['sha1'] = array('eq',$v['sha1']);
				$where['md5'] = array('eq',$v['sha1']);
				$vo = M('picture')->where($where)->find();
				if(!empty($vo)){
					@unlink('.'.$path); 
					//$arr = array('code'=>200,'path'=>$vo['path'],'icon'=>$vo['id']);
				}else{
					$data['sha1'] = $v['sha1'];
					$data['md5']  = $v['md5'];
					$data['create_time'] = time();
					$data['path'] = $path;
					$res = M('picture')->add($data);
					if($res){
						//$arr = array('code'=>200,'path'=>$path,'icon'=>$res);
						$icon .= ','.$res;
					}

				}
			}
			
			//$arr = array('code'=>200,'path'=>$path,'icon'=>trim($icon,','));
			$icon=trim($icon,',');
			$re=M('usermember')->where('id='.session('user'))->save(array('icon'=>$icon));
			if($re){
				$this->redirect('mer/shangjia',array('icon'=>$icon));
			}
			//$this->ajaxReturn($arr);
		}
	}
	//上传图片
	public function upload(){
		if ($_POST) {
			$form_name = $_GET['form_name'];
			$file_size = intval($_GET['file_size']);
			$upload = new AjaxUpload($form_name, $file_size);
			$result = $upload->upload();
			if($result['ok'] == 'ok'){
				$data['path'] = "/Uploads/Picture/".date('Y-m-d').'/'.$result['filename'];
				$data['url'] = '';
				$file = file_get_contents('.'.$data['path']);
				$data['md5'] = md5($file);
				$data['sha1'] = sha1($file);
				$data['status'] = 1;
				$data['create_time'] = time();
				$pic['md5'] = array('eq',$data['md5']);
				$pic['sha1'] = array('eq',$data['sha1']);
				$picture = M('picture')->where($pic)->find();
				if($picture){
					@unlink('.'.$data['path']);
					$arr = array('ok'=>"ok",'filename'=>$picture['path'],'id'=>$picture['id']);
					exit(json_encode($arr));
				}else{
					$result = M('picture')->add($data);
					if($result){
						$arr = array('ok'=>"ok",'filename'=>$data['path'],'id'=>$result);
						exit(json_encode($arr));
					}
				}
			}
		}
	}
	

	
	//上传文件
	public function upload_files(){
		if ($_POST) {
			$form_name = $_GET['form_name'];
			$file_size = intval($_GET['file_size']);
			$upload = new AjaxUpload($form_name, $file_size);
			$result = $upload->upload();
		
			if($result['ok'] == 'ok'){
				
				$data['name'] = $_FILES[$_GET['form_name']]['name'];
				$data['size'] = sprintf("%.2f", ($_FILES[$_GET['form_name']]['size'])/1024/1024).'M';
				$data['path'] = "/Uploads/Picture/".date('Y-m-d').'/'.$result['filename'];
				$data['url'] = '';
				$file = file_get_contents('.'.$data['path']);
				$data['md5'] = md5($file);
				$data['sha1'] = sha1($file);
				$data['status'] = 1;
				$data['create_time'] = time();
				$pic['md5'] = array('eq',$data['md5']);
				$pic['sha1'] = array('eq',$data['sha1']);
				$picture = M('picture')->where($pic)->find();
				
				if($picture){
					@unlink('.'.$data['path']);
					$arr = array('ok'=>"ok",'filename'=>$picture['path'],'id'=>$picture['id']);
					exit(json_encode($arr));
				}else{
					$result = M('picture')->add($data);
					if($result){
						$arr = array('ok'=>"ok",'filename'=>$data['path'],'id'=>$result,'data'=>$data,'file'=>$_FILES);
						exit(json_encode($arr));
					}
				}
			}
		}
	}
	
	/*用户更改头像*/
	public function uploads(){
		if ($_POST) {
			$form_name = $_GET['form_name'];
			$file_size = intval($_GET['file_size']);
			$upload = new AjaxUpload($form_name, $file_size);
			$result = $upload->upload();
			if($result['ok'] == 'ok'){
				$data['path'] = "/Uploads/Picture/".date('Y-m-d').'/'.$result['filename'];
				$data['url'] = '';
				$file = file_get_contents('.'.$data['path']);
				$data['md5'] = md5($file);
				$data['sha1'] = sha1($file);
				$data['status'] = 1;
				$data['create_time'] = time();
				$pic['md5'] = array('eq',$data['md5']);
				$pic['sha1'] = array('eq',$data['sha1']);
				$picture = M('picture')->where($pic)->find();
				if($picture){
					@unlink('.'.$data['path']);
					$id['id']=array('eq',session('user'));
					$save['icon']=$picture['id'];
					M('user')->where($id)->save($save);
					$arr = array('ok'=>"ok",'filename'=>$picture['path'],'id'=>$picture['id']);
					exit(json_encode($arr));
				}else{
					$result = M('picture')->add($data);
					if($result){
						$id['id']=array('eq',session('user'));
						$save['icon']=$result;
						M('user')->where($id)->save($save);
						$arr = array('ok'=>"ok",'filename'=>$data['path'],'id'=>$result);
						exit(json_encode($arr));
					}
				}
			}
		}
	}


	/*微信登陆已经判断*/
	public function calljs(){
		if(IS_POST){
			$list=I('post.');
			if($list['type']==1){
				$wher['weiappid']=array('eq',$list['msg']['openid']);
				$user=M('user')->where($wher)->find();
				if($user){
					session('user',$user['id']);
					exit('200');
				}else{
					session('weix',$list['msg']);
					exit('400');
				}
			}else{
				/*绑定微信*/
				$wher['weiappid']=array('eq',$list['msg']['openid']);
				$user=M('user')->where($wher)->find();
				if($user){
					exit(json_encode(array('code'=>'400','msg'=>'当前微信已绑定过了,请更换微信!')));
				}
				$id['id']=array('eq',session('user'));
				$save['weititle']=$list['msg']['name'];
				$save['weiappid']=$list['msg']['openid'];
				$user=M('user')->where($id)->save($save);
				if($user){
					exit(json_encode(array('code'=>'200','msg'=>'绑定成功')));
				}else{
					exit(json_encode(array('code'=>'400','msg'=>'绑定失败')));
				}
			}
		}
	}
	
	//登陆接口
	
	public function sessionLogin(){
		
		$href = $_POST['href'];
		$data['sourceCode'] = 5;
		$data['backData'] = base64_encode($href);
		$data['key'] = md5('sourceCode=000'.$data['sourceCode'].'&backData='.$href.'wllzztzdr@v_142779052200001');
		
		$arr = json_encode($data);
		
		echo $arr;
		
	}
	
	//注册接口
	
	public function sessionRegister(){
		
		$href = $_POST['href'];
		$data['sourceCode'] = 5;
		$data['backData'] = base64_encode($href);
		$data['key'] = md5('sourceCode=000'.$data['sourceCode'].'&backData='.$href.'wllzztzdr@v_142779052200002');
		
		$arr = json_encode($data);
		
		echo $arr;
		
	}
	public function send_post($url, $post_data) {
  
	  $postdata = http_build_query($post_data);
	  $options = array(
	  'http' => array(
	  'method' => 'POST',//or GET
	  'header' => 'Content-type:application/x-www-form-urlencoded',
	  'content' => $postdata,
	  'timeout' => 15 * 60 // 超时时间（单位:s）
	  )
	  );
	  $context = stream_context_create($options);
	  $result = file_get_contents($url, false, $context);
	  return $result;
  }
  
  //弹窗是否登陆
  
  public function loginCheck(){
	  
	$this -> display();  
	  
  }
  
  //退出会员中心
  
  public function signout(){
	  	
		session('dr-uid',null);  
		
		$href = $_POST['url'];
		$data['sourceCode'] = 5;
		$data['backData'] = base64_encode($href);
		$data['key'] = md5('sourceCode=000'.$data['sourceCode'].'&backData='.$href.'wllzztzdr@v_142779052200001');
		
		
		echo '200-'.$data['sourceCode'].'-'.$data['backData'].'-'.$data['key'];
		
		//exit('200');
		/*$url = $_POST['url'];
		
		$this -> redirect('http://c.tzdr.com:999/tzdr-web/logout?sourceCode=5&backData='.base64_encode($url).'&key='.md5('sourceCode=0005&backData='.$url.'wllzztzdr@v_142779052200001'));
		
		$uid = session('dr-uid');
	  	$key = md5('sourceCode=0005wllzztzdr@v_142779052200005');
		$post_data = array(
		  'sourceCode' => 5,
		  'key' => $key,
		  'uid' => $uid,
		);
	  	$result = $this -> send_post('http://c.tzdr.com:999/tzdr-web/singleLogout',$post_data);
		
		$memResult = json_decode($result,true);
		
		//exit(var_dump($memResult));
		
		if($memResult['success'] == true){
			
				
			
		}else{
			
			exit('500');
			
		}*/
	  
		
	 
  }
  
  //查询@好友列表
  public function memlist(){
	  
	  	$useration = $this -> getMemberation();
		
		//查询自己的所有好友
		$data['_string'] = ' uid = '.$useration.' OR friendid = '.$useration;
		$fir['status'] = array('eq',1);
		
		//查询最新的所有问题
	   
		$data = M("friend");
		$count = $data->where($fir)->count();

		$Page = new \Think\Pagelist($count,12); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$friList = $data->where($fir)->limit($Page->firstRow.",".$Page->listRows)->select();
		$this->assign('page',$show);// 赋值分页输出
		
		$this -> assign('friList',$friList);
		$this -> assign('useration',$useration);
		$this -> display();  
		
  }
  
  //存在登陆session
  public function loginSession(){
	  
		session('loginSession','loginSession');
		
		exit('200');  
	  
  }
  
  //存储注册session
  public function registerSession(){
	  
		session('loginSession','registerSession');
		
		exit('200');  
	  
  }
  
  //获取登陆注册session
  public function layerSession(){
	  
	  $loginSession = session('loginSession');
	  
	  if($loginSession == 'loginSession'){
		  
		  session('loginSession',null);
		  exit('200');
		  
	  }elseif($loginSession == 'registerSession'){
		  session('loginSession',null);
		  exit('201');
		  
	  }else{
		  session('loginSession',null);
		  exit('500'); 
		  
	  }
	  
	  
  }

    public function loginout(){
        if(IS_POST){
            if(session('user',null) && session('user_info',null)){
                exit(json_encode(array('code'=>200,'msg'=>'')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'')));
            }
        }
    }
  public function checklogin1(){
		
		$memList = str_replace("_","",$_GET['key']);
		
		$memInfo = json_decode($memList,true);
		
		$backData = base64_decode($memInfo['backData']);
		
		$kevVal = md5('sourceCode=000'.$memInfo['sourceCode'].'&backData='.$backData.'7kcb@19910503');
		
		if($memInfo['key'] == $kevVal && $memInfo['sourceCode'] == 5){
			
			
			$uid = $memInfo['uid'];
				
			$key = md5('sourceCode=0005wllzztzdr@v_142779052200003');
			$post_data = array(
			  'sourceCode' => 5,
			  'key' => $key,
			  'uid' => $uid,
			);
			
			$result = $this -> send_post('http://c.tzdr.com:999/tzdr-web/getUserInfo',$post_data);
			
			$memResult = json_decode($result,true);
			
			
			//查询当前会员是否在这边注册
			$memList = $memResult['data'];
			//var_dump($memList);
			$user['uid'] = array('eq',$memList['uid']);
			
			$userList = M('usermember')->where($user)->find();
			if(!$userList){
				
				
				//添加会员
				
				$data['uid'] = $memList['uid'];
				$data['mobile'] = $memList['mobile'];
				$data['username'] = $memList['uname'];
				$data['email'] = $memList['email'];
				$data['position'] = getCateId(31,$memList['position']);
				$data['industry'] = getCateId(16,$memList['industry']);
				$data['education'] = getCateId(9,$memList['education']);
				$data['marriage'] = getCateId(27,$memList['marriage']);
				if(!empty($memList['province'])){
					
					$data['nowsprovince'] = $memList['province'];
					
				}else{
					
					$data['nowsprovince'] = 0;	
					
				}
				
				if(!empty($memList['city'])){
					
					$data['nowscity'] = $memList['city'];
					
				}else{
					
					$data['nowscity'] = 0;	
					
				}
				$data['nowsaddress'] = $memList['address'];
				$data['birthday'] = '';
				$data['leastlogin'] = time();
				$data['addtime'] = $memList['ctime'];
				$data['icon'] = 0;
				$data['point'] = 0;
				$data['birthaddress'] = 0;
				
				if(M('usermember')->add($data)){
					
					session('dr-uid',$memList['uid']);
					
					Header("Location: $backData"); 
				}
				
			}else{
				
				
				
				session('dr-uid',$memList['uid']);
				
				Header("Location: $backData"); 
				
				
			}
			
			
		}else{
			
			$data['success'] = false;
			$data['message'] = '密钥或渠道来源不匹配！';		
			
		}
		
		var_dump($data);
	}


  
	
}
