<?php

namespace App\Controller;

class AddressController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
        $this->checkIndividual();
        $this->checkUid($param);
    }
    
    //我的收货地址
    public function address(){
        $post = $this->param;
        $result = D('Address')->address($post);
        $this->appReturn($result);
    }
    
    //设置默认地址
    public function def(){
        $post = $this->param;
        if( !$post['uid'] || !$post['address_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Address")->def($post);
        $this->appReturn($result);
    }
    
    //删除地址
    public function del(){
        $post = $this->param;
        if( !$post['uid'] || !$post['address_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Address")->del($post);
        $this->appReturn($result);
    }
    
    //新增地址
    public function add(){
        $post = $this->param;
        if( !$post['uid'] || !$post['user_name'] || !$post['phone'] || !$post['province'] || !$post['street'] || !$post['address'] || !$post['city'] || !$post['area']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Address")->add($post);
        $this->appReturn($result);
    }
    
    //新增地址
    public function edit(){
        $post = $this->param;
        if( !$post['uid'] || !$post['user_name'] || !$post['phone'] || !$post['province'] || !$post['street'] || !$post['address'] || !$post['city'] || !$post['area']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Address")->edit($post);
        $this->appReturn($result);
    }
    
    //查询省市区
    public function province(){
        $post = $this->param;
        $result = D("Address")->province($post);
        $this->appReturn($result);
    }
    
}
