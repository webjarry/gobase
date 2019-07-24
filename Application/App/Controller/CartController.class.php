<?php
namespace App\Controller;

class CartController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
        $this->checkIndividual();
        $this->checkUid($param);
    }
    
     //我的购物车
    public function cart(){
        $post = $this->param;
        if( !$post['uid']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Cart')->cart($post);
        $this->appReturn($result);
    }
    
    //加入购物车
    public function add_cart(){
        $post = $this->param;
        if( !$post['uid'] || !$post['shop_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Cart')->add_cart($post);
        $this->appReturn($result);
    }
    
    //购物车删除
    public function del_cart(){
        $post = $this->param;
        if( !$post['uid'] || !$post['cart_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Cart')->del_cart($post);
        $this->appReturn($result);
    }
    
    
    
}