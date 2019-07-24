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
class PrintA4Controller extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		$type['pid'] = array('eq',7);
		$type['status'] = array('eq',1);
		$this->assign("typelist",M('category')->where($type)->order('sort asc')->select());
		parent::_initialize();
	}
	public function _filter(&$sqlWhere){
		
		if(!empty($_GET['name'])){
			
			$sqlWhere['name'] = array('like','%' . $_GET['name'] . '%');
		}	
		
		return $sqlWhere;
		
	}
	public function print_a4(){
		$id=$_GET['id'];
		$order = M('order')->where("id=".$id)->find();

		$goods = M('goods')->where("id=".$order[goodsid])->find();
		$order[name]=$goods[name];

		$order[color]=M('color')->where("id=".$order[color])->find()[name];

		$addr = M('addr')->where("id=".$order[addrid])->find();
		$order[addr]=$addr[city].$addr[addr];

		if($order[type]==2){
			$user=M('staff')->find($this->uid);
		}else{
			$user=M('usermember')->find($this->uid);
		}
		$order[nickname]=$user[nickname];
		$order[phone]=$user[phone];

		$order[time]=date('Y-m-d H:i');

;		$this->assign('vo',$order);
		$this->display();
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
}
