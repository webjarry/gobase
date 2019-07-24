<?php

namespace App\Controller;

class PublicController extends HomeController {

    //public $param;

    public function _initialize() {

        parent::_initialize();

        //$list = file_get_contents('php://input');
        //$param = json_decode($list, true);
        //$this->param = $param;
        //$this->param = $_POST;
    }
    

    //登录
    public function login() {

        $post = $this->param;

        if (!$post['phone'] || !$post['type']) {

            $this->appReturn(array('status' => false,'code'=>201, 'msg' => '未完善信息'));
        }

        $result = D('Common/Userlogin')->login($post);

        $this->appReturn($result);
    }
    public function waplogin() {

        $post = $this->param;

        if (!$post['phone'] || !$post['type']) {

            $this->appReturn(array('status' => false,'code'=>201, 'msg' => '未完善信息'));
        }

        $result = D('Common/Userlogin')->waplogin($post);

        $this->appReturn($result);
    }
    //注册
   
    public function reg()
    {
        $post = $this->param;

        $arr['phone'] = array('eq', $post['phone']);

        /*if($post['yzm']){
            if(S($post['phone'])!=$post['yzm']){
                $this->appReturn( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data));
            }
        }*/

        if ($post['type'] == 1) {
            if ($result = M('usermember')->where($arr)->find()) {
                $this->appReturn(array('status' => true, 'msg' => '该手机号已被注册', 'code' => 201, 'data' => $result));
            }else{
                if($post['idno']){
					
					$pid = M('usermember')->where("idno=".$post['idno'])->find();
					
                    if(!$pid){
                        $this->appReturn(array('status' => true, 'msg' => '推荐码不存在！', 'code' => 201, 'data' => $result));
                    }
					$add['pid'] = $pid['id'];
                }
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
                    $result['webphone'] = C('WEB_SITE_MOBILE');

                    $ewm=$this->erweim(1,$re);

                    
                    $save = M('usermember')->where(array('id' => $re))->save(array('token' => $token,'ewm' => $ewm));
                    $this->appReturn(array('status' => true, 'msg' => '注册成功', 'code' => 200, 'data' => $result));
                } else {
                    $this->appReturn(array('status' => true, 'msg' => '注册失败', 'code' => 201, 'data' => $result));
                }

            }

        }elseif ($post['type'] == 2) {
            $user = M('usermember')->where($arr)->find();
            if($user){
                //$this->appReturn( array('status' => true, 'msg' => '该手机号已在用户端注册', 'code' => 201, 'data' => $result)));
            }

            if (M('staff')->where($arr)->find()) {
                $this->appReturn( array('status' => true, 'msg' => '该手机号已被注册', 'code' => 201, 'data' => $result));
            } else {
                if($post['idno']){
					
					$pid = M('staff')->where("idno=".$post['idno'])->find();
                    if(!$pid){
                        $this->appReturn(array('status' => true, 'msg' => '推荐码不存在！', 'code' => 201, 'data' => $result));
                    }
					$add['pid'] = $pid;
                }
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
                    $result['webphone'] = C('WEB_SITE_MOBILE');
                    $ewm=$this->erweim(2,$re);

                    
                    $save = M('staff')->where(array('id' => $re))->save(array('token' => $token,'ewm' => $ewm));
                    $this->appReturn(array('status' => true, 'msg' => '注册成功', 'code' => 200, 'data' => $result));
                } else {
                    $this->appReturn(array('status' => true, 'msg' => '注册失败', 'code' => 201, 'data' => $result));
                }
            }

        }


    }
	
	public function checkOpenid(){
		
		$post = $this->param;
		if(!$post['openid'] || !$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
		
		if($post['type'] == 1){
            $m=M('usermember');

        }else{
            $m=M('staff');

        }
		$where['openid'] = array('eq',$post['openid']);
        
        $result = $m->where($where)->find();
		
		if($result){
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'用户存在'));
		}else{
			$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'用户不存在'));
		}

		
		
	}

    public function three(){
        $post = $this->param;
        if(!$post['openid'] || !$post['username'] || !$post['icon'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if($post['type'] == 1){
            $m=M('usermember');
            $type=1;
        }else{
            $m=M('staff');
            $type=2;
        }
        $where['openid'] = array('eq',$post['openid']);
        //$where['nickname'] = array('eq',$post['username']);
        $result = $m->where($where)->find();
		
		
		if($post['phone'] && $result){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'当前手机号已存在'));
			
		}

        if(!empty($result)){
            $token = md5($result['id'] . noncestr());
            $result['token'] = $token;
            $result['type'] = $type;

            $result['webphone'] = C('WEB_SITE_MOBILE');
            $result['workday'] = C('WEB_WORKDAY');
            $result['wxkefu'] = C('WEB_WX_KEFU');


            $save = $m->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'登录成功','data'=>$result));
        }else{
			
			$yzm = S($post['phone']);
			
			if(empty($post['yzm']) || $post['yzm'] != $yzm){
				
				$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'短信验证码有误','yzm1'=>$post['yzm'],'yzm2'=>$yzm));
				
			}
			$data['phone'] = $post['phone'];
			$data['password'] = $post['password'];
            $data['openid']=$post['openid'];
            $data['nickname']=$post['username'];
            $data['icon']=$post['icon'];
            $data['idno']=str_shuffle(time());

            $data['create_time']=time();
            $data['date_time']=date('Y-m');
            $re = $m->add($data);
            if($re){
                //var_dump($info);die();

                $result=$m->find($re);

                $token = md5($re . noncestr());
                $result['token'] = $token;
                $result['type'] = $type;

                $result['webphone'] = C('WEB_SITE_MOBILE');
                $result['workday'] = C('WEB_WORKDAY');
                $result['wxkefu'] = C('WEB_WX_KEFU');
                if($post['type'] == 1){
                    $ewm=$this->erweim(1,$re);
                }else{
                    $ewm=$this->erweim(2,$re);
                }

                $save = $m->where(array('id' => $re))->save(array('token' => $token,'ewm' => $ewm));
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'登录成功','data'=>$result));
            }

        }

    }
    public function loginbang(){
            $post = $this->param;

            if($post['yzm']){
                if(S($post['phone'])!=$post['yzm']){
                    exit(json_encode( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data)));
                }
            }
            if($post[usertype]=='user'){
                $m=M('usermember');
                $type=1;
            }else{
                $m=M('staff');
                $type=2;
            }

            $three_info=$post;

            $me = $m->where("openid='".$three_info['openid']."'")->find();
            $m->where("id=".$me['id'])->save(array('phone'=>$post[phone]));

            if( $have = $m->where("phone='".$post[phone]."' and three=0")->find() ){

                $m->where("id=".$me['id'])->save(array('three'=>1));
                if(empty($have[icon])){
                    $m->where("id=".$have['id'])->save(array('icon'=>$three_info[icon]));
                }
                $result = $m->where("id=".$have['id'])->find();
                $token = md5($have['id'] . noncestr());

                $result['token'] = $token;
                $result['type'] = $type;

                //session('user',$result);
                $save = $m->where(array('id' => $have['id']))->save(array('token' => $token));

            }else{
                $result=$me;

                $token = md5($me['id'] . noncestr());
                if($post[usertype] == 'user'){
                    $ewm=$this->erweim(1,$me['id']);
                }else{
                    $ewm=$this->erweim(2,$me['id']);
                }
                $result['token'] = $token;
                $result['type'] = $type;

                //session('user',$result);
                $save = $m->where(array('id' => $me['id']))->save(array('token' => $token,'ewm' => $ewm));

            }

            if($result){
                $this->appReturn(array('status' => true, 'msg' => '绑定成功', 'code' => 200, 'data' => $result));
            }else{
                $this->appReturn( array('status' => false, 'msg' => '绑定失败', 'code' => 201, 'data' => $result));
            }

    }
    public function threelogin($info){
        $post = $this->param;
        $where['openid'] = array('eq',$info['openid']);
        //$where['status'] = array('eq',1);
        if($post[usertype] == 'user'){
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

                //session('user',$result);
                $save = $m->where(array('id' => $have['id']))->save(array('token' => $token));

            }else{
                $token = md5($result['id'] . noncestr());
                $result['type'] = $type;
                $result['token'] = $token;

                //session('user',$result);
                $save = $m->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
            }
            if($post[usertype]=='user'){
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
                
                $this->redirect('/home/public/loginbang');exit();
            }
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

    public function search(){
        $post = $this->param;
        if(!$post['key'] || !$post['actiontype'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        

        if($post['actiontype']==1){
            $start=$post[page]?($post[page]-1)*10 : 0;
            $ww["name"]=array('like','%'.$post['key'].'%');

            $result = M('hetong')->where($ww)->limit($start,10)->select();
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $start=$post[page]?($post[page]-1)*4 : 0;
            $ww["name"]=array('like','%'.$post['key'].'%');
            $id = M('ajcate')->where($ww)->find()[id];
            if($id){
                //$where['_string'] =  'FIND_IN_SET('.$id.',label)';
                //$result = M('staff')->where($where)->limit($start,4)->select();
                $result = M('staff')->where("FIND_IN_SET($id,label) and pass=1")->select();
                foreach($result as $k=>$v){
                    $staff=sinfo($v['id']);
                    $result[$k][name]= $staff[nickname];
                    $result[$k][icon]= $staff[icon];
                    $result[$k][label]= $staff[label];

                }
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M('staff')->getLastSql(),'data'=>$result));
            }else{
                $wh["nickname"]=array('like','%'.$post['key'].'%');
                $wh["pass"]=array('eq',1);
                $result = M('staff')->where($wh)->select();
                if($result){
                    foreach($result as $k=>$v){
                        $staff=sinfo($v['id']);
                        $result[$k][name]= $staff[nickname];
                        $result[$k][icon]= $staff[icon];
                        $result[$k][label]= $staff[label];

                    }
                    $this->appReturn(array('status'=>false,'code'=>200,'msg'=>M('staff')->getLastSql(),'data'=>$result));
                }else{
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('staff')->getLastSql(),'data'=>$result));
                }


            }

        }

    }
    //验证码
    public function yzm(){
        $post = $this->param;
        if(!$post['phone']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = D('Common/Userlogin')->yzm($post);
        
        $this->appReturn($result);
    }
    //忘记密码
    public function forget(){
        $post = $this->param;
        
        if(!$post['phone'] || !$post['password'] || !$post['yzm'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        
        $result = D('Common/Userlogin')->forget($post);
        
        $this->appReturn($result);
    }
	
	
	//找回密码
    public function forgeter(){
        $post = $this->param;
        
        if(!$post['phone'] || !$post['password'] || !$post['yzm'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        
        $result = D('Common/Userlogin')->forgeter($post);
        
        $this->appReturn($result);
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

    public function peizhi(){
        $post = $this->param;

        $result[url]=C('WEB_SITE_URL');
        $result[email]=C('WEB_SITE_EMAIL');
        $result[phone]=C('WEB_SITE_MOBILE');
        $result[bq]=C('WEB_BANQUAN_AUTH');
        $result[copy]=C('WEB_SITE_ICP');

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }

    public function fanzui(){
        $post = $this->param;
        if(!empty($post[key])){
            $where['content']=array('like','%'.$post[key].'%');
        }
        $result=M('question')->where($where)->find();

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function lawyer_info(){
        $post = $this->param;
        $result = M('staff')->find($post[id]);
        $me=sinfo($post[id]);
        $result[icon]=$me[icon];
        $result[label]=$me[label];
        $result[score]=score($post[id]);
        $result[servernum]=servernum($post['id']);
         if($result){
            $this->appReturn(array('status'=>true,'msg'=>'成功','code'=>200,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'失败','code'=>201,'data'=>$result));
        }
    }
    public function news(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]>0?($post[page]-1)*4: 0;

        if($post['newstype']==1){
            $where['file']=array('gt',0);
        }else{
            $where['file']=array('eq',0);
        }
        $where['typeid']=array('eq',$post['type']);  
        $where['status']=array('eq',1);

        $result=M('news')->where($where)->order('id desc')->limit($start,4)->select();
        $result=$this->fors($result);
        foreach ($result as $k=>$v){
            $result[$k][plnum]=M('pl')->where("tid=".$v['id']." and type=1")->count();
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function dt(){
        $post = $this->param;
        /*if(!$post['uid'] || !$post['utype']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }*/
        $start=$post[page]>0?($post[page]-1)*5 : 0;
        if($post['uid']>0 && $post['utype']>0){
            $where['uid']=array('eq',$post['uid']);
            $where['utype']=array('eq',2);
        }
        $where['status']=array('eq',1);
        $result=M('dongtai')->where($where)->order('id desc')->limit($start,5)->select();
        foreach($result as $k=>$v){
            if($v[utype]==1){
                $user=uinfo($v[uid]);
                $result[$k][nickname]= yc_phone($user[phone]);
                $result[$k][icon]= $user[icon];
            }else{
                $staff=sinfo($v[uid]);
                $result[$k][nickname]= $staff[nickname].'律师';
                $result[$k][icon]= $staff[icon];
            }

            $result[$k][time]= date('Y-m-d H:i',$v[create_time]);
            $result[$k][icons]= expic($v[icon]);
            /*if(M('dz')->where("tid=".$v['id']." and type=2 and uid=".$post['uid']." and utype=".$post['utype'])->find()){
                $result[$k][is_dz]==1;
            }else{
                $result[$k][is_dz]==0;
            }*/
            $result[$k][is_dz]==0;
            $result[$k][zf]= M('dongtai')->where("pid=".$v[id])->count();
            $result[$k][pl]= M('pl')->where("tid=".$v[id]." and type=2")->count();
            $result[$k][dz]= M('dz')->where("tid=".$v[id]." and type=2")->count();
        }
        $result[data]=$result;
        $result[total]=M('dongtai')->where($where)->count();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function newnotice(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $result=M('fanswer')->where("tid=".$post[id])->select();

        if($result){
            M('fask')->where("id=".$post[id])->save(array('notice'=>1));
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function fask(){
        $post = $this->param;
        $start=$post[page]>0?($post[page]-1)*10 : 0;

        if($post[uid]>0){
            $where['uid']=array('eq',$post[uid]);
        }else if($post['type']>0){
            $where['private']=array('eq',0);
        }
        if($post[typeid]>0){
            $where['ajid']=array('eq',$post[typeid]);
        }
        if(!empty($post[key])){
            $where['content']=array('like','%'.$post[key].'%');
        }
        $where['status']=array('eq',1);
        $result=M('fask')->where($where)->order('id desc')->limit($start,50)->select();
		
		$newList = array();
		
        foreach($result as $k=>$v){
			
			
			
            $user=uinfo($v[uid]);
            $result[$k][phone]=empty($user['nickname'])?$user[phone]:$user['nickname'];
            $result[$k][icon]=$user[icon];
            $result[$k][addr]=$user[addr];

            $result[$k][ajtype]=M('ajcate')->find($v[ajid])[name];
            $result[$k][time]=format_date($v[create_time]);
            $result[$k][num]=M('fanswer')->where("tid=".$v[id])->count();
			$result[$k][choose]=M('fanswer')->where("tid=".$v[id].' and choose=1')->count();
			
			if($v['private'] == 1){
				
				$map['orderno'] = array('eq',$v['orderno']);
				$map['status'] = array('eq',1);
				$order = M('order')->where($map)->find();
				
				if(!$order || $order['status'] != 1){
					
					unset($result[$k]);
					
				}else{
					
					$newList[] = $result[$k];
					
				}
				
			}else{
				
				$newList[] = $result[$k];
				
			}
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$newList));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function askinfo(){
        $post = $this->param;

        $result=M("fask")->find($post[id]);

        $staff=uinfo($result['uid']);
        $result[name]= $staff[phone];
        $result[icon]= $staff[icon];
        $result[ordertype]= ordertype($result[type]);
		$result[time] = format_date($result[create_time]);            
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }



    }
    public function askinfo1(){
        $post = $this->param;

        $result=M("ask")->find($post[id]);

        $staff=sinfo($result['sid']);
        $result[name]= $staff[phone];
        $result[icon]= $staff[icon];
        $result[ordertype]= ordertype($result[type]);

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }



    }
    public function fanswer(){
        $post = $this->param;
        if(!$post['uid']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]>0?($post[page]-1)*5 : 0;


        $where['sid']=array('eq',$post['uid']);
        $result=M('fanswer')->where($where)->order('id desc')->limit($start,5)->select();
        foreach($result as $k=>$v){
            $ask=M('fask')->find($v[tid]);
            $result[$k][content]=$ask[content];
            $result[$k][reply]=$v[content];
            $result[$k][ajtype]=M('ajcate')->find($ask[ajid])[name];
            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);

            $uid=$ask[uid];
            $user=uinfo($uid);
            $result[$k][phone]=$user[phone];

            $staff=sinfo($v['sid']);
            $result[$k][nickname]=$staff[nickname];
            $result[$k][icon]=$staff[icon];

        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function faskdetail(){
        $post = $this->param;
        if( !$post['id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }

        $result=M('fanswer')->where("tid=".$post['id'])->order('id desc')->select();
        foreach($result as $k=>$v){
            $staff=sinfo($v['sid']);
            $result[$k][nickname]=$staff[nickname];
            $result[$k][icon]=$staff[icon];
            $result[$k][time]=format_date($v[create_time]);
        }
        $result['list']=$result;
        $result['info']=M('fask')->find($post['id']);
		$result['info']['time'] = format_date($result['info'][create_time]);
        $result['uinfo']=uinfo($result['info']['uid']);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function wt(){
        $post = $this->param;
        $start=$post[page]>0?($post[page]-1)*4 : 0;

        $sort=$post['sort'];
        if($sort==1){
            $order="w.create_time desc";
        }elseif($sort==2){
            $order="w.margin desc";
        }elseif($sort==3){
            $order="w.maxprice desc";
        }elseif($sort==4){
            $order="w.margin desc";
        }elseif($sort==5){
            $ww['w.over']=array('eq',0);
        }elseif($sort==6){
            $ww['w.over']=array('eq',1);
        }elseif($sort==7){
            $ww['w.type']=array('eq',2);
        }elseif($sort==8){
            $ww['w.type']=array('eq',1);
        }else{
            $order="w.create_time desc";
        }
        if($post['addrid']>0){
            $ww['w.addrid']=array('eq',$post['addrid']);
        }
        if($post['ajid']>0){
            $ww['w.ajid']=array('eq',$post['ajid']);
        }
        if($post['uid']>0){
            $ww['w.uid']=array('eq',$post['uid']);
        }else{
            $ww['w.payed']=array('eq',1);
        }
        if($post['type']==1){
            $ww['w.status']=array('eq',1);
            //$ww['payed']=array('eq',0);
        }else if($post['type']==2){
            $ww['w.status']=array('in','2,4');
            $ww['w.payed']=array('eq',1);
			$ww['o.is_confirm']=array('eq',0);
        }else if($post['type']==3){
            $ww['w.status']=array('eq',3);
            $ww['w.payed']=array('eq',1);
        }else if($post['type']==4){
            $ww['w.status']=array('eq',4);
            $ww['w.payed']=array('eq',1);
			$ww['o.is_confirm']=array('eq',1);
        }

        $result = M('wt as w')->join('bhy_order as o on o.orderno=w.orderno')->field('w.*')->where($ww)->order($order)->limit($start,4)->select();
        foreach($result as $k=>$v){
            if($v[status]==3 || $v[status]==4){
                $result[$k][over]=1;
            }
            $user=M('usermember')->find($v[uid]);
            $result[$k][icon]=picture($user[icon]);
            $result[$k][phone]=yc_phone($user[phone]);
            $result[$k][addr]=$user[addr];

            $result[$k][ajtype]=M('ajcate')->find($v[ajid])[name];
            if($v[type]>0){
                $result[$k][price]='1'.'-'.$v[maxprice];
            }else{
                $result[$k][price]=$v[minprice].'-'.$v[maxprice];
            }

            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
            $result[$k][num]=M('wtjoin')->where("tid=".$v[id])->count();
           $re= M('wtjoin')->where('tid='.$v[id].' and choose=1')->find();
            if($re){
                $result[$k][choose]=1;
            }else{
                $result[$k][choose]=0;
            }
            $sql=M('wtjoin')->getLastSql();

            if(time()>$v[lasttime]){
                $result[$k][guoqi]=1;
            }else{
                $result[$k][guoqi]=0;
            }
            $result[$k][lasttime]= date('Y-m-d H:i',$v[lasttime]);
            
            $baojia=M('wtjoin')->where('tid='.$v[id].' and choose=1')->find()[money];
            $result[$k][rest]= $baojia-$v[margin];
			
			if($v['status'] >= 2){
				
				$result[$k]['progress'] = wt_progress($v['progress']);
				
			}
            
            
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$a,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('wt')->getLastSql(),'data'=>$result));
        }

    }
    public function wtdetail(){
        $post = $this->param;
        if( !$post['id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }

        M('wt')->where("id=".$post[id])->setInc('view');
        $result=M('wtjoin')->where("tid=".$post['id'])->order('id desc')->select();
        foreach($result as $k=>$v){
            $staff=sinfo($v['uid']);
            $result[$k][phone]=$staff[phone];
            $result[$k][nickname]=$staff[nickname];
            $result[$k][icon]=$staff[icon];
            $result[$k][label]=$staff[label];
            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
            
            $result[$k][num]=servernum($v['uid']);
            $result[$k][score]=score($v['uid']);
        }
        $result['list']=$result;

        $wt=M('wt')->find($post['id']);
        $result['info']=$wt;
        $result['uinfo']=uinfo($result['info']['uid']);
        $result['info']['lasttime']=date('Y-m-d H:i',$result['info']['last_time']);
        if($wt[type]>0){
            $result['info'][price]='1'.'-'.$wt[maxprice];
        }else{
            $result['info'][price]=$wt[minprice].'-'.$wt[maxprice];
        }
        $result['info'][time]=date('Y-m-d H:i',$wt[create_time]);

        if($post['uid']>0){
            $result['me']=sinfo($post['uid']);
            $a=M('wtjoin')->where("tid=".$post['id']." and uid=".$post['uid'])->find();
            $result['me']['price']=$a['money'];
            $result['me']['time']=date('Y-m-d H:i',$a['create_time']);
            $result['me']['choose']=$a['choose'];
            
            
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function zc(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*4: 0;

        if($post[uid]>0){
            $where[uid]=array('eq',$post[uid]);
        }else{
            $where[status]=array('eq',2);
        }
        $result = M('zc')->where($where)->order('id desc')->limit($start,4)->select();
        foreach ($result as $k=>$v){
            /*if(M('zcjoin')->where("tid=".$v[id])->count()){
                $have=M('zcjoin')->where("tid=".$v[id])->sum('money');
                $rest=$v[money]-$have;
                $result[$k][rest]=$rest;
                $result[$k][bili]=ceil($have/$v[money]);
            }else{
                $result[$k][rest]=$v[money];
                $result[$k][bili]=0;
            }*/
            $zong = D('App/Zongcou')->count_price($v['id'],$v['money']);
            $cha_price = $v['money']-$zong['total_price'];
            $result[$k]['money'] = $cha_price; //差的金额
            $result[$k]['bili'] = $zong['scale'];


            $icon=$v[icon];
            if(strpos($icon,',')){
                $f=explode(',',$icon)[0];
            }else{
                $f=$icon;
            }
            $result[$k][icon]=picture($f);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function hz(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*4: 0;
        if($post[type]>0){
            $where[status]=array('eq',$post[type]);
        }
        if(!empty($post['date1'])){
            $where['create_time']=array('gt',strtotime($_GET['date1']));
        }
        if(!empty($post['date2'])){
            $where['create_time']=array('lt',strtotime($_GET['date2']));
        }
        if(!empty($post['date1']) && !empty($post['date2'])){
            $where['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
        }
        if($post[uid]>0){
            $where[uid]=array('eq',$post[uid]);
        }
        $result = M('hz')->where($where)->order('id desc')->limit($start,4)->select();
        foreach ($result as $k=>$v){
            $user=uinfo($v[uid]);
            $result[$k][nickname]=$user['nickname'];
            $icon=$v[icon];
            if(strpos($icon,',')){
                $f=explode(',',$icon)[0];
            }else{
                $f=$icon;
            }
            $result[$k][icon]=picture($f);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function zcdetail(){
        $post = $this->param;
        if( !$post['id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $result=M('zc')->where("id=".$post[id])->find();

        if(M('zcjoin')->where("tid=".$post[id])->count()){
            $have=M('zcjoin')->where("tid=".$post[id])->sum('money');
            $result[have]=$have;
            $result[bili]=ceil($have/$result[money]);
        }else{
            $result[have]=0;
            $result[bili]=0;
        }
        if(time()>$result[lasttime]){
            $result[isover]=1;
        }else{
            $result[isover]=0;
        }
        $result[cover]=expic($result[icon])[0];
        $result[rest]=$result[money]-$result[have];
        $result[resttime]=$result[lasttime]+7*24*3600-time();

        $result[stime]=date('Y-m-d',$result[create_time]);
        $result[etime]=date('Y-m-d',$result[lasttime]);
        $result[icon]=expic($result[icon]);
        $result[file]=expic($result[file]);

        $result[wtid]=M('wt')->where("tid=".$post[id])->find()[id];
        
        $aj=M('ajcate')->where("id=".$result[ajid])->find();
        $result[ajtype]=M('ajcate')->where("id=".$aj[paid])->find()[name].'-'.$aj[name];

        $zm=M('zcsure')->where("tid=".$post[id])->order('id desc')->select();
        foreach ($zm as $k=>$v){
            $user=uinfo($v[uid]);
            $zm[$k][phone]=$user[phone];
            $zm[$k][icon]=$user[icon];
            $zm[$k][time]=date('Y-m-d',$v[create_time]);
        }
        $result[zm]=$zm;
        $result[num]=count($zm);

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function paihang(){
        $post = $this->param;
        if( !$post['type'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]>0?($post[page]-1)*4 : 0;
        if($post['type']==1){
            $result=M('ask a')->join("bhy_staff s on a.sid=s.id")->field("s.*,count(a.sid) as snum,a.sid")->group('a.sid')->order('s.score desc')->limit($start,4)->select();
        }else if($post['type']==2){
            $result=M('ask a')->join('bhy_staff s on a.sid=s.id')->field("s.*,count(*) as snum,a.sid")->group('a.sid')->order('snum desc')->limit($start,4)->select();
        }else if($post['type']==3){
            $result=M('mind a')->join('bhy_staff s on a.sid=s.id')->field("s.*,sum(a.money) as snum,a.sid")->group('a.sid')->order('snum desc')->limit($start,4)->select();
        }
        if($result){
            foreach($result as $k=>$v){
                $staff=sinfo($v['id']);
                $result[$k][name]=$staff[nickname];
                $result[$k][icon]=$staff[icon];
                $result[$k][label]=$staff[label];
                $result[$k][time]=date('Y-m-d H:i',$v[create_time]);

                $result[$k][servernum]=servernum($v['id']);
                //$result[$k][score]=score($v['id']);
				
				//M('staff')->where('id='.$v['id'])->save(array('score'=>score($v['id'])));
            }
			
			
			
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M('fanswer a')->getLastSql(),'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>M('ask a')->getLastSql(),'data'=>$result));
        }

    }

    public function banner(){
        $post = $this->param;
        $result = M('ad')->where("typeid=1 and status=1")->find();
        $result[icon]=picture($result[icon]);

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function catead(){
        $post = $this->param;
        $result = M('ad')->where("typeid=2 and status=1")->find();
        $result[icon]=picture($result[icon]);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }

    public function cate(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $where['pid'] = array('eq', $post['type']);
        if($post['type']==1){
            $result = M('category')->field("id,title")->where($where)->order('id asc')->select();
            foreach($result as $k=>$v){
                $son = M('category')->field("id,title")->where("pid=".$v[id])->order('id asc')->select();
                $result[$k][son]=$son;
            }
        }else{
            $result = M('category')->field("id,title")->where($where)->order('id asc')->select();
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function ajcate(){
        $post = $this->param;

        $w[status]=array('eq',1);
        $w[pid]=array('eq',0);
        $result = M('ajcate')->field('id,name')->where($w)->order('sort asc')->select();
        foreach ($result as $k=>$v){
            $ww[status]=array('eq',1);
            $ww[pid]=array('eq',$v[id]);
            $result[$k][son]=M('ajcate')->field('id,name')->where($ww)->order('sort asc')->select();
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function soncate(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }

        $result = M('cate')->field('id,name,icon')->where("pid=".$post[id])->order('sort asc')->select();
        $result = $this->fors($result);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function region(){
        $post = $this->param;
        $w['pid']=array('eq',0);
        $result = M('region')->field('id,name')->where($w)->order('id asc')->select();

        foreach ($result as $k=>$v){
            $middle=M('region')->field('id,name')->where("pid=".$v[id])->order('id asc')->select();
            $arr=array();
            foreach ($middle as $mk=>$mv){
                $son=M('region')->field('id,name')->where("pid=".$mv[id])->order('id asc')->select();
                foreach ($son as $sk=>$sv){
                    array_push($arr,$sv);
                }
            }
            $result[$k][son]=$arr;
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function countfee(){
        $post = $this->param;
        if(!$post['ajid'] || !$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if($post['addrid']>0){
            $bili=M('region')->find($post['addrid'])[bili];
        }else{
            $bili=100;
        }
        if($post['caichan']>0){
            //$bili=M('region')->find($post['addrid'])[bili];
        }
        if($post['type']==1){
            $fee=M('ajcate')->find($post['ajid'])[lawyerfee];
        }else{
            $fee=M('ajcate')->find($post['ajid'])[legalfee];
        }
        $fee=$fee*$bili/100;
        if($fee){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$fee));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$fee));
        }

    }
    public function htdetail(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        
        $fee=M('hetong')->find($post['id']);
        $file=$fee[icon];
        if($file != ''){
            if(strpos($file,',')){
                $c=explode(',',$file);
                $fee[num]=count($c);
            }else{
                $fee[num]=1;
            }
        }else{
            $fee[num]=0;
        }
		
		$filesInfo = M('file')->where('id='.$fee['file'])->find();
		
		$fee[file] = empty($filesInfo)?'':'/Uploads/Download/'.$filesInfo['savepath'].$filesInfo['savename'];
        $fee[icons]=expic($fee[icon]);
        if($fee){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$fee));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$fee));
        }

    }
    
    public function searchlawyer(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*8 : 0;
        $where['pass']=array('eq',1);
        if($post['addrid']>0){
            $where['addrid']=array('eq',$post['addrid']);
        }
        if($post['ajid']>0){
            $where['_string'] = 'FIND_IN_SET('.$post['ajid'].',label)';
        }

        if($post[sort]==1){
            $order="year desc";
        }else if($post[sort]==2){
            $order="score desc";
        }else{
            $order="sort desc";
        }
        $result = M('staff')->where($where)->order($order)->limit($start,8)->select();
        foreach($result as $k=>$v){
            $staff=sinfo($v['id']);
            $result[$k][name]= $staff[nickname];
            $result[$k][icon]= $staff[icon];
            $result[$k][label]= $staff[label];
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M('staff')->getLastSql(),'data'=>$result,'sort'=>$order));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'','data'=>$result));
        }

    }

    public function attribute(){
        $post = $this->param;
        $result=array();

        $material= M('material')->where("status=1")->select();
        $brand= M('brand')->where("status=1")->select();
        $cate= M('cate')->where("pid>0 and status=1")->select();

        $result[material]=$material;
        $result[brand]=$brand;
        $result[cate]=$cate;

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function index(){
        $post = $this->param;
        $result=array();

        $tj = M('goods')->where("status=1 and recommend=1")->limit(3)->select();
        $newest = M('goods')->where("status=1")->order('id desc')->limit(4)->select();
        $sery= M('sery')->where("status=1")->order('sort asc')->limit(3)->select();
        $cheap = M('goods')->where("status=1 and cheap=1")->limit(4)->select();

        $result[tj]=$this->fors($tj);
        $result[newest]=$this->fors($newest);
        $result[sery]=$this->fors($sery);
        $result[cheap]=$this->fors($cheap);

        $banner=M('ad')->where("typeid=1 and status=1")->select();
        foreach($banner as $k=>$v){
            $banner[$k][icon]=picture($v[icon]);
        }
        $result[banner]=$banner;

        $result['cpy']['name']=C('WEB_CPYNAME');
        $result['cpy']['addr']=C('WEB_COM_ADDRESS');
        $result['cpy']['tel']=C('WEB_SITE_MOBILE');

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }

    public function more(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if(isset($post['updown'])){
            $sort=$post['sort'];
            if($sort==1){
                $order="price asc";
            }elseif($sort==2){
                $order="salenum asc";
            }else{
                $order="sort desc";
            }
        }elseif(isset($post['sort'])){
            $sort=$post['sort'];
            if($sort==1){
                $order="price desc";
            }elseif($sort==2){
                $order="salenum desc";
            }else{
                $order="sort asc";
            }
        }
        if($post['type']==1){
            $where[recommend]=array('eq',1);
        }elseif($post['type']==2){
            $order="id desc";

        }elseif($post['type']==3){
            //$where[recommend]=array('eq',1);
        }elseif($post['type']==4){
            $where[cheap]=array('eq',1);
        }

        $where[status]=array('eq',1);


        $result =$this->fors( M('goods')->where($where)->order($order)->select() );


        $brand = M('brand')->where("status=1")->select();
        foreach($brand as $k=>$v){
            $brand[$k][icon]=picture($v[icon]);
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }


    public function getarticle(){
        $post = $this->param;
        if(!$post['id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $yida= M('yida')->where("id=".$post[id])->find();

        $yida[detail]=str_replace('img src="','img src="'.C('WEB_SITE_URL'), $yida[detail]);
        if($yida){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$yida));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$yida));
        }

    }
    public function goods(){
        $post = $this->param;

        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>$post));
        }

        $result =M('goods')->where("id=".$post['id'])->find();

        if(strpos($result[color],',')){
            $c=explode(',',$result[color]);
        }else{
            $c=array($result[color]);
        }
        foreach($c as $k=>$v){
            $color[$k][name]= M('color')->where("id=".$v)->find()[name];
            $color[$k][id]= $v;
        }
        $result[color]=$color;
        if($result[imgarr] != ''){
            if(strpos($result[imgarr],',')){
                $s=explode(',',$result[imgarr]);
            }else{
                $s=array($result[imgarr]);
            }
            foreach($s as $k=>$v){
                $imgarr[$k]= picture($v);
            }
            $result[imgarr]=$imgarr;
        }else{
            $result[imgarr]=array();
        }


        $result[icon]=picture($result[icon]);


        if($com=M('pl')->where("goodsid=".$post['id'])->select()){
            foreach ($com as $k=>$v){
                if($v[type]==1){
                    $user=M('usermember')->where("id=".$v[uid])->find();
                }else{
                    $user=M('staff')->where("id=".$v[uid])->find();
                }
                $com[$k][name]=$user[nickname];
                $com[$k][icon]=picture($user[icon]);
                $com[$k][time]=date('Y-m-d',$v[create_time]);

                $colorid=M('order')->where("id=".$v[oid])->find()[color];
                $com[$k][color]=M('color')->where("id=".$colorid)->find()[name];
            }
            $result[com]=$com;
            $result[plnum]=count($com);
        }else{
            $result[com]=array();
            $result[plnum]=0;
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function gonggao(){
        $post = $this->param;
        $start=$post[page]>0?($post[page]-1)*7 : 0;

        $result=M('mind')->order('id desc')->limit($start,7)->select();
        foreach($result as $k=>$v){
            $user=uinfo($v[uid]);
            $result[$k][phone]=$user[phone];

            $staff=sinfo($v['sid']);
            $result[$k][name]=$staff[nickname];
            $result[$k][icon]=$staff[icon];
            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);

        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function mindgonggao(){
        $post = $this->param;
        $start=$post[page]>0?($post[page]-1)*7 : 0;

        $result=M('mind')->order('id desc')->limit($start,7)->select();
        foreach($result as $k=>$v){
            $user=uinfo($v[uid]);
            $result[$k][phone]=$user[phone];


            $staff=sinfo($v['sid']);
            $result[$k][name]=$staff[nickname];
            $result[$k][icon]=$staff[icon];
            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);

        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function ad(){
        $post = $this->param;
        if($post[id]>0){
            $result=M('ad')->where("id=".$post[id])->find();
            $result['file'] = fileurl($result['file']);
        }else{
            if($post[type]==1){
                $result=M('ad')->where("typeid=".$post[type]." and status=1")->order('sort asc')->select();
                $result=$this->fors($result);
            }else{
                $result=M('ad')->where("typeid=".$post[type]." and status=1")->order('sort asc')->find();
                $result[icon]=picture($result[icon]);
            }
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }

    public function pl(){
        $post = $this->param;
        if( !$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }

        $where['type']=array('eq',$post['type']);

        if($post['uid'] >0 && $post['type']==3){
            $ask=M('ask')->field('id')->where("sid=".$post['uid'])->select();
            $ids=implode(',',array_column($ask,'id'));
            $where['tid']=array('in',$ids);
        }else{
            $where['tid']=array('eq',$post['id']);
        }
        if($post[page] >0){
            $start=$post[page]>0?($post[page]-1)*5: 0;
            $com=M('pl')->where($where)->order('id desc')->limit($start,5)->select();$a=M('pl')->getLastSql();
        }else{
            $com=M('pl')->where($where)->order('id desc')->select();
        }
        if($com){
            foreach ($com as $k=>$v){
                if($v[utype]==1){
                    $user=M('usermember')->find($v[uid]);
                    $com[$k][phone]=yc_phone($user[phone]);
                }else{
                    $user=M('staff')->find($v[uid]);
                    $com[$k][phone]=$user[nickname].'律师';
                }
                $com[$k][icon]=picture($user[icon]);
                $com[$k][time]=date('Y-m-d H:i',$v[create_time]);

                //$colorid=M('order')->where("id=".$v[oid])->find()[color];
                //$com[$k][color]=M('color')->where("id=".$colorid)->find()[name];
            }
            $result[com]=$com;
            $result[plnum]=count($com);
        }else{
            $result[com]=array();
            $result[plnum]=0;
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$a,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$a,'data'=>$result));
        }

    }
    
    public function province(){
        $post = $this->param;
        $result = M('region')->where(array('level'=>1))->select();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function city(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if($result=M('region')->where(array('level'=>2,'parent_id'=>$post[id]))->select()){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未找到子级市','data'=>$result));
        }
     }
    public function xian(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if($result=M('region')->where(array('level'=>3,'parent_id'=>$post[id]))->select()){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未找到子级县','data'=>$result));
        }
    }
    public function area(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        if($result=M('region')->where(array('level'=>4,'parent_id'=>$post[id]))->select()){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未找到子级区','data'=>$result));
        }
    }


    //上传图片
    public function uploads(){
        $config = C('PICTURE_UPLOAD');
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if($info){

            $filedata['pic']='/Uploads/Picture/'.$info['file']['savepath'].$info['file']['savename'];

            $pic['md5'] = $info['file']['md5'];
            $pic['sha1'] = $info['file']['sha1'];
            $picture = M('picture')->where($pic)->find($pic);
            if($picture){
                @unlink('.'.$filedata['pic']);
                $result['code'] = 200;
                $result['pic'] = $picture['path'];
                $result['id'] = $picture['id'];
                $this->appReturn($result);
            }else{

                //裁剪图片
                $data['path'] = $filedata['pic'];
                //$data['cutpath'] = $cutpath;
                $data['url'] = '';
                $data['md5'] = $info['file']['md5'];
                $data['sha1'] = $info['file']['sha1'];
                $data['status'] = 1;
                $data['create_time'] = time();
                $pic_id = M('picture')->add($data);
                if(!empty($pic_id)){
                    $result['code'] = 200;
                    $result['pic'] = $data['path'];
                    $result['id'] = $pic_id;
                    $this->appReturn($result);
                }else{
                    $result['code'] = 201;
                    $result['msg']='上传失败';
                    $result['sql'] = $sql;
                    $result['error']=$upload->getError();
                    $this->appReturn($result);
                }
            }
        }else{
            $result['code']=202;
            $result['msg']='上传失败';
            $result['error']=$upload->getError();
            $this->appReturn($result);
        }
    }
	
	//上传文件
    public function upload_file(){
        //p($_FILES);exit;
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     0 ;// 设置附件上传大小
        $upload->exts      =     array('doc', 'docx', 'ppt','pptx','xls','xlxs','pdf');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     'Download/'; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            $result['code'] = 201;
            $result['message']='上传失败';
            exit(json_encode($result));
        }else{// 上传成功
            $path = "/Uploads/".$info['file']['savepath'].$info['file']['savename'];

            $where['sha1'] = array('eq',$info['file']['sha1']);
            $where['md5'] = array('eq',$info['file']['md5']);
			
			
            $vo = M('file')->where($where)->find();

            if(!empty($vo)){
                @unlink('.'.$path); //删除图片路径
                $pather = '/Uploads/'.$vo['savepath'].$vo['savename'];
                $arr = array('code'=>200,'path'=>$pather,'video_id'=>$vo['id']);
				exit(json_encode($arr));
            }else{
                /*$data['name'] = $info['file']['name'];
                $data['ext'] = $info['file']['ext'];
                $data['size'] = $info['file']['size'];
                $data['savename'] = $info['file']['savename'];
                $data['savepath'] = $info['file']['savepath'];
                $data['sha1'] = $info['file']['sha1'];
                $data['md5']  = $info['file']['md5'];
                $data['create_time'] = time();

                $res = M('file')->add($data);*/
				
				
				$data['name'] = $info['file']['name'];
				$data['size'] = sprintf("%.2f", $info['file']['size']/1024).'M';
				$data['path'] = "/Uploads/Picture/".date('Y-m-d').'/'.$info['file']['savename'];
				$data['url'] = '';
				$file = file_get_contents('.'.$data['path']);
				$data['md5'] = md5($file);
				$data['sha1'] = sha1($file);
				$data['status'] = 1;
				$data['create_time'] = time();
				$pic['md5'] = array('eq',$data['md5']);
				$pic['sha1'] = array('eq',$data['sha1']);
				$res = M('picture')->where($pic)->find();

                if(!empty($res)){
                    $result['code'] = 200;
                    $result['path'] = $path;
                    $result['video_id'] = $res;
                    exit(json_encode($result));
                }else{
                    $result['code'] = 201;
                    $result['message']='上传失败';
                    exit(json_encode($result));
                }
            }
        }
    }

}
