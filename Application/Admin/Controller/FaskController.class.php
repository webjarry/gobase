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
class FaskController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		$type['pid'] = array('eq',7);
		$type['status'] = array('eq',1);
		$this->assign("typelist",M('category')->where($type)->order('sort asc')->select());
		parent::_initialize();
	}
	public function _filter(&$sqlWhere){
		//var_dump($sqlWhere);
		if(!empty($_GET['type'])){
			if($_GET['type'] == 1){
				$sqlWhere['_string'] = '((private=0 and reward_price=0) or ((private=1 or reward_price=0) and is_pay=1))';
			}elseif($_GET['type'] == 2){
				$sqlWhere['_string'] = 'reward_price > 0 and is_pay=1';
				
			}elseif($_GET['type'] == 3){
				$sqlWhere['_string'] = 'private =1 and is_pay=1';
			}elseif($_GET['type'] == 4){
				$sqlWhere['_string'] = '((private=0 and reward_price=0) or ((private=0 or reward_price>0) and is_pay=1))';
			}else{
				$sqlWhere['_string'] = '((private=0 and reward_price=0) or (reward_price>0 and is_pay=1))';
			}
		}else{
			$sqlWhere['_string'] = '((private=0 and reward_price=0) or ((private=1 or reward_price>0) and is_pay=1))';
		}
		
		if(!empty($_GET['name'])){
			
			$sqlWhere['content'] = array('like','%'.$_GET['name'].'%');
		}
		if(!empty($_GET['date1']) && empty($_GET['date2'])){
			
			$sqlWhere['create_time'] = array('egt',strtotime($_GET['date1']." 00:00:00"));
		}
		if(empty($_GET['date1']) && !empty($_GET['date2'])){
			
			$sqlWhere['create_time'] = array('elt',strtotime($_GET['date2']." 23:59:59"));
		}
		if(!empty($_GET['date1']) && !empty($_GET['date2'])){
			
			$sqlWhere['create_time'] = array(array('egt',strtotime($_GET['date1']." 00:00:00")),array('elt',strtotime($_GET['date2']." 23:59:59")));
		}
		
		return $sqlWhere;
		
	}
	public function recommend(){
		$id=$_GET['id'];
		$recommend = M('travelcpy')->where("id=".$id)->find()['recommend'];
		$tj=$recommend==1?0:1;
		$re=M('travelcpy')->where("id=".$id)->save(array('recommend'=>$tj));
		if($re){

			$this->success('修改成功','/Admin/travelcpy/index.html');
		}else{
			$this->error('修改失败');
		}
	}
	public function savesort(){
		
		if(IS_POST){
			
			M('fask')->save($_POST);
			
		}
		
	}
}
