<?php

namespace App\Controller;

class ShopController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
    }
    
 
    //首页数据
    public function index(){
        $post = $this->param;
        if(!$post['city_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Shop')->index($post);
        $this->appReturn($result);
    }
    
    //搜索
    public function sell(){
        $post = $this->param;
        if(!$post['city_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Shop")->sell($post);
        $this->appReturn($result);
    }
    
    //商品详情页
    public function shop(){
        $post = $this->param;
        if(!$post['city_id'] || !$post['shop_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D("Shop")->shop($post);
        $this->appReturn($result);
    }
    
    

}
