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
class LawcrcleController extends AdminController {
	
	public function _initialize(){
		
		parent::_initialize();
	}
	
	public function _filter(&$sqlWhere){
		
		if(!empty($_GET['name'])){
			
			$sqlWhere['content'] = array('like','%' . $_GET['name'] . '%');
		}	
		
		return $sqlWhere;
		
	}
	
}
