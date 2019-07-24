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
class AdController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		$type['pid'] = array('eq',0);
		$type['id'] = array('lt',6);
		$this->assign("typelist",M('category')->where($type)->order('sort asc')->select());

		$aaa['pid'] = array('eq',42);
		$this->assign("guidelist",M('category')->where($aaa)->order('sort asc')->select());
		$this->assign("goodslist",M('goods')->field('id,name')->where("status=1")->order('id desc')->select());
		$this->assign("artlist",M('yida')->field('id,name')->where("status=1")->order('id desc')->select());
		parent::_initialize();
	}
	public function add(){
		if(IS_POST){

			$dde = D('Ad')->create();
			if($dde){
				D('Ad')->add($_POST);
				$this->success('添加成功',U('ad/index'));
			}else{
				$this->error(D('Ad')->getError());
			}
		}else{
			$this->display();
		}

	}



	public function _filter(&$sqlWhere){
		
		if(!empty($_GET['name'])){
			
			$sqlWhere['name'] = array('like','%' . $_GET['name'] . '%');
		}	
		
		return $sqlWhere;
		
	}
	/* public function _before_add(){
		
	} */
}
