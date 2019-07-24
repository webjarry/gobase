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
class StaffController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		
		$type['status'] = array('eq',1);
		$this->assign("staff",M('staff')->where($type)->order('sort asc')->select());
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
		*/
		if(!empty($_GET['date1'])){
			$map['create_time']=array('gt',strtotime($_GET['date1']));

		}
		if(!empty($_GET['date2'])){
			$map['create_time']=array('lt',strtotime($_GET['date2']));

		}
		if(!empty($_GET['date1']) && !empty($_GET['date2'])){
			$map['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));

		}
		if(!empty($_GET['name'])){
			$map['nickname']=array('like','%'.$_GET['name'].'%');
		}
		
		if(isset($_GET['status'])){
			
			$map['pass'] = array('eq',$_GET['status']);
			
		}
		
		//dump($map);
		$list   = $this->lists('staff',$map);
		foreach ($list as $k=>$v) {
			$list[$k]['num']=M('ask')->where("sid=".$v[id])->count();
			if(strpos($v[icon],'http') !== false){

			}else{
				$list[$k][icon] = picture($v[icon]);
			}
			$list[$k]['wdz'] = D('Home/Staff')->wdz_price($v[id]);
			//$list[$k]['balance']=M('order')->where("uid=".$v[id].' and type=2')->sum("money");

		}
		int_to_string($list);
		$this->assign('_list', $list);


		$this->display();
	}
	public function changestate(){
		$id=$_GET['id'];
		$state = M('staff')->where("id=".$id)->find()['state'];
		$state=$state==0? 1:0;
		$re = M('staff')->where("id=".$id)->save(array('state'=>$state));
		if($re){
			$this->success('修改成功','/Admin/Staff/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function deluser(){
		$id=$_GET['id'];
		$state = M('staff')->where("id=".$id)->delete();
		if($state){
			$this->success('修改成功','/Admin/Staff/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function _before_edit(){
		
		$id=$_GET['id'];
		$user = M('staff')->where("id=".$id)->find();

		$this->assign('info',$user);
		
	}
	public function pass(){
		$id=$_GET['id'];
		$recommend = M('staff')->where("id=".$id)->find()['pass'];
		$tj=$recommend==1?0:1;
		$re=M('staff')->where("id=".$id)->save(array('pass'=>$tj));
		if($re){

			$this->success('修改成功','/Admin/staff/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	
	public function savesort(){
		
		if(IS_POST){
			
			M('staff')->save($_POST);
			
		}
		
	}
}
