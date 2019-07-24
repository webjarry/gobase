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

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

    protected function _initialize(){
		//header('Access-Control-Allow-Origin:*');

		/* 读取站点配置 */
        $config = api('Config/lists');
        C($config);

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
		//var_dump(session('user'));
		
		$userinfo = session('user');
		
		if(!empty($userinfo)){
			
			if($userinfo['type'] == 1){
				
				$user = M('usermember')->where('id='.$userinfo['id'])->find();

				$user['type'] = 1;
				
			}else{
				$user=M('staff')->where("id=".$userinfo[id])->find();

				$user['type'] = 2;
				
			}
			if(strpos($user[icon],'http://') !== false){

			}else{
				$user[icon]=picture($user[icon]);
			}

			$this->assign('user',$user);
			$this->assign('islogin',1);
		}else{
			$this->assign('user',123);
			$this->assign('islogin',0);
		}
		
		$key = "418831400";
		$redirect_uri = "http://www.test2.test/Home/Public/weblogin?";
		//授权后将页面重定向到本地项目
		$redirect_uri = urlencode($redirect_uri);
		$wb_url = "https://api.weibo.com/oauth2/authorize?client_id={$key}&response_type=code&redirect_uri={$redirect_uri}";
		
		$this -> assign('web_url',$wb_url);

	}

	//头部
	public function head(){
		
       //查询顶部网站LOGO
	   $lg['typeid'] = array('eq',13);
	   $lg['status'] = array('eq',1);
	   $logo = M('ad')->where($lg)->order('sort asc')->find();
	  
	   $this -> assign('logo',$logo);
      
	   
	   //查询导航
	   $nav['pid'] = array('eq',0);
	   $nav['isnav'] = array('eq',1);
	   $nav['status'] = array('eq',1);
	   $navList = M('category')->where($nav)->field('url,title,id,pid')->order('sort asc')->limit(6)->select();
	   $this -> assign('navList',$navList);

	   foreach($navList as $kr=>$vr){
		  $navarrs[pid] = $vr[id]; 
		  $navarrs[status] = 1; 
		  $navarrs[isnav]  = 1; 
		  $navbn[$vr[id]] = M('category')->where($navarrs)->field('url,title,id,pid')->order('sort asc')->select();
	   }
	   
	   $this -> assign('navbn',$navbn);
	   //底部导航
	   foreach($navList as $kr=>$vr){
		  $navarr[pid] = $vr[id]; 
		  $navarr[status] = 1; 
		  $ernav[$vr[id]] = M('category')->where($navarr)->field('url,title,id,pid')->order('sort asc')->select();
	   }
	   $this -> assign('ernav',$ernav);
	   
	   
	   //查询网页左边导航
	   $arrte[typeid] = 16;
	   $arrte[status] = 1;
       $leftads = M('ad')->where($arrte)->order('id desc')->find();
	   $this -> assign('leftads',$leftads);	
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
	public function checklogin(){
		
		/*$uid = $_GET['uid'];
		
		file_put_contents("text.txt","Hello World. Testing!");
		session('[start]');
		session('dr-uid',$uid);
		echo $uid;*/
		
		$memList = str_replace("_","",$_GET['key']);
		
		$memInfo = base64_decode($memList);
		

			
		  $uid = $memInfo;
			  
		  $key = md5('sourceCode=0005wllzztzdr@v_142779052200003');
		  $post_data = array(
			'sourceCode' => 5,
			'key' => $key,
			'uid' => $uid,
		  );
		  
		  $result = $this -> send_post('http://c.tzdr.com:999/tzdr-web/getUserInfo',$post_data);
		  
		  $memResult = json_decode($result,true);
		  
		  if($memResult['success'] == true){
			  
			  
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
				  $data['mobile'] = $$memList['mobile'];
				  $data['uid'] = $memList['uid'];
				  $data['success'] = true;
				  $data['message'] = '登陆成功！';		
			  }
			  
		  }else{
			  session('dr-uid',$memList['uid']);
			  $data['mobile'] = $$memList['mobile'];
			  $data['uid'] = $memList['uid'];
			  $data['success'] = true;
			  $data['message'] = '登陆成功！';	 
			}
		}else{
			$data['success'] = false;
		     $data['message'] = '用户不存在，非法操作！';	
		}
		 //var_dump($data);
	  echo json_encode($data);
	}
	public function data_post($url, $post_data) {
  
		  $postdata = http_build_query($post_data);
		  $options = array(
			  'http' => array(
			  'method' => 'GET',//or GET
			  'header' => 'Content-type:application/x-www-form-urlencoded',
			  'content' => $postdata,
			  'timeout' => 15 * 60 // 超时时间（单位:s）
		  )
		  );
		  $context = stream_context_create($options);
		  $result = file_get_contents($url, false, $context);
		  return $result;
	}
	
	//微信信息
    public function getopenid(){
        $appid='wx46fbc7e73f15a373';
        $appsecret='44aa58acf67e8c595d2ba21c0190cc08';
        $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $return_url =urlencode($return_url);

        if(!isset($_GET['code'])){
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$return_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            header("Location:".$url);exit;
        }else {
            $code = $_GET['code'];
            /*根据code获取用户openid*/
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
            $abs = file_get_contents($url);
            $obj = json_decode($abs, true);
            $access_token = $obj['access_token'];
            $openid = $obj['openid'];
            //获取信息
            $abs_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
            $abs_url_data = file_get_contents($abs_url);
            $infoarr = json_decode($abs_url_data, true);
           // session('WX_USERINFO', $infoarr);
		   
		   return $infoarr;
        }
    }
	
	

	protected function appReturn($value) {
        header("Content-Type:application/json; charset=utf-8");
        $array = array(
            'code' => 200,
            'status' => true,
            'data' => array(),
            'msg' => '获取数据成功',
        );

        //控制开关
        $app_debug = C('APP_DEBUG');
        if ($app_debug) {
            $debug = array(
                'debug' => array(
                    'param' => array(
                        'post' => I('post.'),
                        'get' => I('get.'),
                        'files' => $_FILES,
                    ),
                    'ip' => get_client_ip(),
                ),
            );
            $array = array_merge($array, $debug);
        }

        $value = array_merge($array, $value);
        exit(json_encode($value));
    }

}
