<?php

namespace App\Controller;

use Think\Controller;

class HomeController extends Controller {

    public $token = '';
    public $uid = 0;
    public $type;
    public $param;
    public $me;
    public $infock = 1;

    /* 空操作，用于输出404页面 */
    public function _empty() {
        $this->redirect('Index/index');
    }

    protected function _initialize() {
		
		header('Access-Control-Allow-Origin:*');
		
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        if (!C('WEB_SITE_CLOSE')) {
            $this->error('站点已经关闭，请稍后访问~');
        }

        $this->param = $_POST;
        
        $this->token=$_POST['token'];
        
        if ($this->token) {
            if (!empty($_POST['infock'])) {
                
                $this->infock = 2;
            }
            $map['token'] = array('eq', $this->token);
            $user = M('usermember')->where($map)->find();
            if ($user) {
                $this->me = $user;
                $this->uid = $user['id'];
                $this->type = 1 ;
            }else{
                $staff = M('staff')->where($map)->find();
                $this->me = $staff;
                $this->uid = $staff['id'];
                $this->type = 2;
            }
        }
    }
    public function find(){
        $post = $this->param;
        if(!$post['model'] || !$post['id']  ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $m=M($post['model']);
        $m->where("id=".$post['id'])->setInc('view');

        $result=$m->find($post['id']);

        $result['time']=date('Y-m-d',$result['create_time']);
        if(!empty($result[icon])){
            if(strpos($result[icon],',')>0){

                $result[icons]=expic($result[icon]);
                $result[icon]=expic($result[icon])[0];
            }else{
                $result[iconid]=$result[icon];
                $result[icon]=picture($result[icon]);
            }

        }
        if(!empty($result[file])){
            $result[fileid]=$result[file];
            $result[file]=fileurl($result[file]);
        }
        if(!empty($result[uid])){
            $user=M('usermember')->find($result[uid]);
            $result[uicon]=picture($user[icon]);
            $result[phone]=yc_phone($user[phone]);
        }
        if(!empty($result[content])){
            if($post[path] == 1){
                //$result[content]=editorPath($result[content]);
            }
        }
        if($post['model']=='zc'){
            $result[content]=$result[detail];
        }
        if($post['model']=='dongtai'){
            $result=$m->find($post['id']);
            $result[icons]=$result[icon];
            $result[icon]=expic($result[icon]);
        
            if($result[utype]==1){
                $staff=uinfo($result['uid']);
                $result['name']=$staff['nickname'];
            }else{
                $staff=sinfo($result['uid']);
                $result['name']=$staff['nickname'].'律师';
            }

            $result['tx']=$staff['icon'];

            $result['time']=date('Y-m-d H:i',$result['create_time']);
            $dz=M('dz')->where("tid=".$post[id])->count();
            $zf=M('dongtai')->where("pid=".$post[id])->count();
            $result['dz']=$dz;
            $result['zf']=$zf;
            
            /*$d='';
            foreach($dz as $k=>$v){
                if($v[utype]==1){
                    $ms=M('usermember');
                }else{
                    $ms=M('staff');
                }
                $d.= ' '.$ms->find($v[uid])[nickname];
            }
            $result['dzname']=$d;
            $result['dznum']=M('dz')->where("tid=".$post[id])->count();

            $z='';
            foreach($zf as $k=>$v){
                if($v[utype]==1){
                    $ms=M('usermember');
                }else{
                    $ms=M('staff');
                }
                $z.= ' '.$ms->find($v[uid])[nickname];
            }
            $result['zfname']=$z;
            $result['zfnum']=M('zf')->where("tid=".$post[id])->count();*/
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result,'user'=>$staff));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function lists(){
        $post = $this->param;
        if( !$post['model'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $pagesize=$post[pagesize]>0?$post[pagesize] : 3;
        $start=$post[page]>0?($post[page]-1)*$pagesize : 0;

        $m=M($post['model']);
        //$where['status']=array('eq',1);
       /* if(!empty($post['uid'])){
            
        }*/
		
		if($post['model'] != 'hetong'){
			
			$where['uid']=array('eq',empty($post['uid'])?$this->uid:$post['uid']);
			
		}else{
			$where['status']=array('eq',1);
		}
		
		
        if(!empty($post['type'])){
            $where['type']=array('eq',$post['type']);
        }
        if(!empty($post['name'])){
            $where['name']=array('like','%'.$post['name'].'%');
        }
        if(!empty($post['key'])){
            $where['name']=array('like','%'.$post['name'].'%');
        }
		
		if($post['sort'] == 1){
			
			$sort = 'id asc';
			
		}else{
			
			$sort = 'id desc';
			
		}
		
        if($post[page]>0){
            $result=$m->where($where)->order($sort)->limit($start,$pagesize)->select();
        }else{
            $result=$m->where($where)->order($sort)->select();
        }
        foreach($result as $k=>$v){
			$result[$k]['icon']=picture($v['icon']);
            if($post['model']=='zhuanti'){
                $result[$k]['catename']=M('ajcate')->find($v['typeid'])['name'];
                $result[$k]['icon']=picture($v['icon']);
            }
            if($post['model']=='hetong' || $post['model']=='hetonglawyer'){

                $result[$k]['icon']=expic($v['icon'])[0];


            }
            if($post['model']=='family'){
                $result[$k]['xiao']=date('Y-m-d H:i',$v[create_time]+180*24*3600);
            }
            $result[$k]['time']=date('Y-m-d H:i',$v[create_time]);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$m->getLastSql(),'data'=>$result,'count'=>count($result),'sort'=>$sort));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result,'msg'=>$m->getLastSql()));
        }
    }
	
	public function back_login_check($username = null, $password = null, $verify = null){
        /* 调用UC登录接口登录 */
		$uid = 1;
		if(0 < $uid){ //UC登录成功
			/* 登录用户 */
			$Member = D('Admin/Member');
			if($Member->login($uid)){ //登录用户
				//TODO:跳转到登录前页面
				

					$this->success('登录成功！', U('/Admin/Category/index'));
					

			} else {
				$this->error($Member->getError());
			}

		} else { //登录失败
			switch($uid) {
				case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
				case -2: $error = '密码错误！'; break;
				default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
			}
			$this->error($error);
		}
    }
    public function count(){
        $post = $this->param;
        if( !$post['model'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
        }
        $m=M($post['model']);
        $where['status']=array('eq',1);
        $result=$m->where($where)->count();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }

    public function checkIndividual() {
        if ($this->uid == 0) {
            $this->appReturn(array('status' => false, 'msg' => '您的账户已在其他设备登录，请重新登录', 'code' => 501));
        }elseif($this->type==2){
            if($this->infock==1){
                $pass=M('staff')->field('pass')->where('id='.$this->uid)->find()['pass'];
                if($pass==0){
                    $this->appReturn(array('status' => false, 'msg' => '您的个人信息尚未认证，无法进行该操作', 'code' => 502));
                } 
            }
            
        }
    }
    public function showhide(){
         $this->appReturn(array('status' => false, 'msg' => 'hide', 'code' => 200));

    }
    public function checkUid($post){
        if($post['uid']!=''){
            $user = M('usermember')->where(array('id'=>$post['uid']))->find();
            //$user = M('usermember')->where(array('id'=>$post['uid'],'token'=>  $this->token))->find();
            if(!$user){
                $this->appReturn(array('status' => false, 'msg' => '用户ID不存在', 'code' => 501));
            }
        }else{
            $this->appReturn(array('status' => false, 'msg' => '请登录', 'code' => 501));
        }
    }
    public function checkStaff($post){
        if($post['uid']!=''){
            $user = M('staff')->where(array('id'=>$post['uid'],'token'=>  $this->token))->find();
            if(!$user){
                $this->appReturn(array('status' => false, 'msg' => '用户ID不存在', 'code' => 501));
            }
        }else{
            $this->appReturn(array('status' => false, 'msg' => '请登录', 'code' => 501));
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

    public function fors($array){
        foreach($array as $k=>$v){
            $array[$k]['icon'] = picture($v['icon']);
            $array[$k]['time'] = format_date($v['create_time']);
            if(!empty($v['file'])){
                $array[$k]['file'] = fileurl($v['file']);
            }
        }
        return $array;
    }
    public function fortime($array){
        foreach($array as $k=>$v){
            $array[$k]['create_time'] = format_date($v['create_time']);
        }
        return $array;
    }
    public function uploads(){
        $config = C('PICTURE_UPLOAD');
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if($info){

            $filedata['pic']='/Uploads/Picture/'.$info['icon']['savepath'].$info['icon']['savename'];

            $pic['md5'] = $info['icon']['md5'];
            $pic['sha1'] = $info['icon']['sha1'];
            $picture = M('picture')->where($pic)->find($pic);
            if($picture){
                @unlink('.'.$filedata['pic']);
                $result['code'] = 200;
                $result['pic'] = $picture['path'];
                $result['id'] = $picture['id'];
                exit(json_encode($result));
            }else{

                //裁剪图片
                $data['path'] = $filedata['pic'];
                //$data['cutpath'] = $cutpath;
                $data['url'] = '';
                $data['md5'] = $info['icon']['md5'];
                $data['sha1'] = $info['icon']['sha1'];
                $data['status'] = 1;
                $data['create_time'] = time();
                $pic_id = M('picture')->add($data);
                if(!empty($pic_id)){
                    $result['code'] = 200;
                    $result['pic'] = $data['path'];
                    $result['id'] = $pic_id;
                    exit(json_encode($result));
                }else{
                    $result['code'] = 201;
                    $result['message']='上传失败';
                    $result['sql'] = $sql;
                    exit(json_encode($result));
                }
            }
        }else{
            $result['code']=202;
            $result['message']='上传失败';
            $result['error']=$upload->getError();
            exit(json_encode($result));
        }
    }
    
}
