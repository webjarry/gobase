<?php
namespace App\Controller;

class OrderController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $this->param = $_POST;
        //$this->param = $param;
     //   $this->checkIndividual();
       // $this->checkUid($param);
    }

    //我的订单
    public function order(){
        $post = $this->param;
        $result = D('Order')->order($post);
        $this->appReturn($result);
    }
    
    //余额支付
    public function pay(){
        $post = $this->param;
        if( !$post['uid'] || !$post['pay_pwd'] || !$post['order_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->pay($post);
        $this->appReturn($result);
    }
    
    //申请退款
    public function cause(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id'] || !$post['cause'] || !$post['cause_content']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->cause($post);
        $this->appReturn($result);
    }
    
    //取消订单
    public function cancel(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->cancel($post);
        $this->appReturn($result);
    }
    
    //确认收货
    public function confim(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->confim($post);
        $this->appReturn($result);
    }
    
    //我的评论
    public function user_comment(){
        $post = $this->param;
        $result = D('Order')->user_comment($post);
        $this->appReturn($result);
    }
    
    //去评论
    public function comment(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id'] || !$post['shop'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->comment($post);
        $this->appReturn($result);
    }
    
    
    //立即购买
    public function add_order(){
        $post = $this->param;
        if( !$post['uid']  || !$post['address_id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->add_order($post);
        $this->appReturn($result);
    }
    
    
    
    
    //临时支付
    public function pay_order(){
        $post = $this->param;
        if( !$post['uid']  || !$post['order_id'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Order')->pay_order($post);
        $this->appReturn($result);
    }
	
	//支付宝
	public function appAlipay(){
		
		 $post = $this->param;
        if(!$post['orderno']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
		
		$map['orderno'] = array('eq',$post['orderno']);
		$order = M('order')->where($map)->find();
		
		if(!$order){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单不存在'));
			
		}
		
		$result = D('Order')->alipayPay($order['orderno'],$order['pay_price']);
        $this->appReturn($result);
		
	}
    
    
}