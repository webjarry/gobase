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
class AjcateController extends AdminController {

	public function _initialize(){

		//查询所有广告分类
		$type['pid'] = array('eq',0);
		$type['status'] = array('eq',1);
		$this->assign("typelist",M('ajcate')->where($type)->order('sort asc')->select());


		parent::_initialize();
	}
	function index(){
		$f=M('ajcate')->where("pid=0 and status=1")->select();
		foreach ($f as $k=>$v){
			$f[$k][son]=M('ajcate')->where("pid=".$v[id]." and status=1")->select();
		}
		$this->assign('list',$f);
		$this->display();
	}
	public function add(){
		if(IS_POST){

			$dde = D('Ajcate')->create();
			if($dde){

				D('Ajcate')->add($_POST);
				//var_dump(D('Ajcate')->getLastSql());die();
				$this->success('添加成功',U('ajcate/index'));
			}else{
				$this->error(D('Ajcate')->getError());
			}
		}else{
			$this->display();
		}

	}

	public function del(){


		$dde = M('Ajcate')->where('pid='.$_GET[id])->count();
		if($dde==0){
			M('Ajcate')->where('id='.$_GET[id])->delete();
			$this->success('删除成功',U('ajcate/index'));
		}else{
			$this->error('存在子类无法删除',U('ajcate/index'));
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
