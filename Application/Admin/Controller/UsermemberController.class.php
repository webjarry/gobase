<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class UsermemberController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		
		$type['status'] = array('eq',1);
		$s=M('staff')->where($type)->order('id desc')->select();
		$this->assign("staff",$s);
		parent::_initialize();
	}
	public function _filter(&$sqlWhere){
		
		/*if(!empty($_GET['name'])){
			
			$sqlWhere['nickname'] = array('like','%' . $_GET['nickname'] . '%');
		}	
		
		return $sqlWhere;*/
		
	}
	public function index(){
		/*	if(!empty($_GET['state'])){
			$this->assign('state',$_GET['state']);
		}
		if(!empty($_GET['state'])&&$_GET['state']==1){
			$map['state']=array('eq',$_GET['state']);
		}elseif(!empty($_GET['state'])&&$_GET['state']==2){
			$map['state']=array('not in','0,1,3');
		}elseif(!empty($_GET['state'])&&$_GET['state']==3){
			$map['state']=array('not in','0,1,2');
		}else{
			$map['state']=array('eq',1);
		}*/
		/*if(!empty($_GET['date1'])){
			$map['create_time']=array('gt',strtotime($_GET['date1']));
			if(!empty($_GET['state'])){
				$map['state']=array('eq',$_GET['state']);
			}
		}
		if(!empty($_GET['date2'])){
			$map['create_time']=array('lt',strtotime($_GET['date2']));
			if(!empty($_GET['state'])){
				$map['state']=array('eq',$_GET['state']);
			}
		}
		if(!empty($_GET['date1']) && !empty($_GET['date2'])){
			$map['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
			if(!empty($_GET['state'])){
				$map['state']=array('eq',$_GET['state']);
			}
		}*/
		if(!empty($_GET['nickname'])){
			$map['nickname']=array('like','%'.$_GET['nickname'].'%');
		}
		if(!empty($_GET['pid'])){
			$map['pid']=array('eq',$_GET['pid']);
		}
		//dump($map);
		$list   = $this->lists('usermember',$map);
		foreach ($list as $k=>$v) {
			if(strpos($v[icon],'http') !== false){

			}else{
				$list[$k][icon] = picture($v[icon]);
			}
			$list[$k]['num']=M('order')->where("uid=".$v[id].' and type=1')->count();
			

		}
		int_to_string($list);
		$this->assign('_list', $list);


		$this->display();
	}
	public function changestate(){
		$id=$_GET['id'];
		$state = M('usermember')->where("id=".$id)->find()['state'];
		$state=$state==0? 1:0;
		$re = M('usermember')->where("id=".$id)->save(array('state'=>$state));
		if($re){
			$this->success('修改成功','/Admin/Usermember/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function deluser(){
		$id=$_GET['id'];
		$state = M('usermember')->where("id=".$id)->delete();
		if($state){
			$this->success('修改成功','/Admin/Usermember/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function vip(){
		$id=$_GET['id'];
		$recommend = M('usermember')->where("id=".$id)->find()['vip'];
		$tj=$recommend==1?0:1;
		$re=M('usermember')->where("id=".$id)->save(array('vip'=>$tj));
		if($re){

			$this->success('修改成功','/Admin/usermember/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function add(){
		if(IS_POST){
			$post=I();
			$_POST[create_time]=time();

			if($_POST['pid']>0){ 
				$_POST['vip']=1;
			}else{
				$_POST['vip']=0;
			}

			if($id=D('usermember')->add($_POST)){
				if($_POST['pid']>0){
					$add[uid]=$id;
					$add[sid]=$_POST['pid'];
					$add[name]=$_POST['nickname'];
					$add[phone]=$_POST['phone'];
					M('myuser')->add($add);
				}

				$this->success('用户添加成功！',U('index'));
			} else {
				$this->error('用户添加失败！',U('index'));
			}


		}else{

			$this->display();
		}

	}

	public function recommend(){
		$id=$_GET['id'];
		$recommend = M('usermember')->where("id=".$id)->find()['recommend'];
		$tj=$recommend==1?0:1;
		$re=M('usermember')->where("id=".$id)->save(array('recommend'=>$tj));
		if($re){

			$this->success('修改成功','/Admin/usermember/index.html');
		}else{
			$this->error('修改失败');
		}
	}
}
