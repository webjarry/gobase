<?php

namespace App\Controller;

class CouponController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
        $this->checkIndividual();
        $this->checkUid($param);
    }
    
    //积分兑换
    public function coupon(){
        $post = $this->param;
        $result = D("Coupon")->coupon($post);
        $this->appReturn($result);
    }
    
    //立即兑换
    public function coupon_dh(){
        $post = $this->param;
        if( !$post['uid'] || !$post['coupon_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Coupon')->coupon_dh($post);
        $this->appReturn($result);
    }
    
    //我的优惠券
    public function user_coupon(){
        $post = $this->param;
        $result = D('Coupon')->user_coupon($post);
        $this->appReturn($result);
    }
    
    //领劵中心
    public function coupon_mf(){
        $post = $this->param;
        $result = D('Coupon')->coupon_mf($post);
        $this->appReturn($result);
    }
    
    //马上领劵（领取免费的优惠券）
    public function coupon_lq(){
        $post = $this->param;
        if( !$post['uid'] || !$post['coupon_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Coupon')->coupon_lq($post);
        $this->appReturn($result);
    }
    
}