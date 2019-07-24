<?php

namespace Common\Model;

/**
 * 订单操作模型
 */
class OrderModel {
    
    //我的订单
    public function order($post){
        $uid = $post['uid'];
        $page = intval($post['page']);
        $where = "user_id = $uid";
        if($page!=''){
            $end = $page*10;
            $start = $end-10;
        }else{
            $start = 0;
            $end = 10;
        }
        $type= intval($post['type']);
        if($type!=''){
            $where = $where." and order_status = $type";
        }
        
        $result = M("order")->where($where)->limit($start,10)->select();
        foreach($result as $k=>$v){

            $fd = "shop_id,shopname,icon,shop_money,shop_num";
            $order_shop = M('order_shop')->where(array('order_id'=>$v['id']))->field($fd)->select();
            foreach($order_shop as $ok=>$ov){
                $order_shop[$ok]['icon'] = picture($ov['icon']);
            }
            $result[$k]['order_shop'] = $order_shop;
        }
        $data = $result;
        return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);

    }
    
    //返回头像地址
    public function picture($id) {

        $picture = M('picture')->find($id);

        if (empty($picture['path'])) {

            return '/Public/Home/images/mr.jpg';
        } else {
            return $picture['path'];
        }
    }
    
    //余额支付
    public function pay($post){
        $uid = $post['uid'];
        $pwd = md5(md5($post['pay_pwd']));
        $order_id = $post['order_id'];
        $user = M('usermember')->where(array('id'=>$uid,'pay_pas'=>$pwd))->field('balance')->find();
        if($user){
            $order = M('order')->where(array('order_id'=>$order_id))->find();
            $order_money = floatval($order['order_money']);
            $balance = floatval($user['balance']);
            if($balance<$order_money){
                return array('status'=>false,'msg'=>'您的余额不足','code'=>201,'data'=>$data);
            }else if($order['order_status']!=1){
                return array('status'=>false,'msg'=>'订单已经支付，请勿重复支付','code'=>201,'data'=>$data);
            }else{
                $m = M();
                $m->startTrans();
                $new_balance = $balance-$order_money;
                $edit_user = $m->table('bhy_usermember')->where(array('id'=>$uid))->save(array('balance'=>$new_balance));
                $save = array(
                    'order_status'=>2,
                    'pay_money'=>$order_money,
                    'pay_time'=>time(),
                    'pay_type'=>3
                );
                $order_status = $m->table("bhy_order")->where(array('order_id'=>$order_id,'user_id'=>$uid))->save($save);
                if($edit_user&&$order_status){
                    $m->commit();
                    return array('status'=>true,'msg'=>'支付成功','code'=>200,'data'=>$data);
                }else{
                    $m->rollback();
                    return array('status'=>false,'msg'=>'支付失败','code'=>201,'data'=>$data);
                }
            }
        }else{
            return array('status'=>false,'msg'=>'密码错误','code'=>201,'data'=>$data);
        }
    }
    
    //申请退款
    public function cause($post){
        $order_id = $post['order_id'];
        $uid = $post['uid'];
        $cause = $post['cause'];
        $cause_img = $post['cause_img'];
        $cause_content = $post['cause_content'];
        $order = M("order")->where(array('order_id'=>$order_id))->find();
        if($order['cause']!=''&&$order['status']==6){
            return array('status'=>false,'msg'=>'您已经申请了，请勿重复申请','code'=>201,'data'=>$data);
        }else{
            $time = time();
            $result = M('order')->where(array('order_id'=>$order_id))->save(array('order_status'=>6,'cause'=>$cause,'cause_img'=>$cause_img,'cause_content'=>$cause_content,'cause_time'=>$time));
            if($result){
                return array('status'=>true,'msg'=>'申请成功','code'=>200,'data'=>$data);
            }else{
                return array('status'=>false,'msg'=>'申请失败','code'=>201,'data'=>$data);
            }
        }
    }
    
    //取消订单
    public function cancel($post){
        $order_id = $post['order_id'];
        $uid = $post['uid'];
        $order = M('order')->where(array('order_id'=>$order_id,'user_id'=>$uid))->find();
        if($order){
            $m = M();
            $m->startTrans();
            
            $result = $m->table('bhy_order')->where(array('order_id'=>$order_id,'user_id'=>$uid))->save(array('order_status'=>5));
            $id = $order['id'];
            $order_shop = M('order_shop')->where(array('order_id'=>$id))->select();
            foreach($order_shop as $v){
                $shop_id = $v['shop_id'];
                $shop = $m->table('bhy_shop')->where(array('id'=>$shop_id))->find();
                $shop_num = $shop['nums'];
                $new_num = intval($shop_num)+intval($v['shop_num']);
                $nums = $m->table('bhy_shop')->where(array('id'=>$shop_id))->save(array('nums'=>$new_num));
            }   
            if($result&&$nums){
                $m->commit();
                return array('status'=>true,'msg'=>'成功取消','code'=>200,'data'=>$data);
            }else{
                $m->rollback();
                return array('status'=>false,'msg'=>'操作失败','code'=>201,'data'=>$data);
            }
        }else{
            return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
        }
        
    }
    
    //我的评论
    public function user_comment($post){
        $uid = $post['uid'];
        $page = $post['page'];
        if($page!=''){
            $end = $page*10;
            $start = $end-10;
            $end = $end -1;
        }else{
            $start = 0;
            $end = 9;
        }
        $field = "bs.id,bs.shopname,bs.icon as shop_img,c.imgarr as icon,c.content,c.create_time,c.stars";
        $result = M('comment c')->join('bhy_shop as bs on bs.id = c.project_id')->where(array('c.uid'=>$uid))->field($field)->limit($start,$end)->select();
        foreach($result as $k=>$v){
            $result[$k]['shop_img'] = $this->picture($v['shop_img']);
            $icon = explode(",", $v['icon']);
            $num=0;
            $sc='';
            foreach($icon as $ic){
                $sc[$num] = picture($ic);
                $num = $num+1;
            }
            $result[$k]['icon'] = $sc;
        }
        $data = $result;
        return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        
    }
    
    //去评论
    public function comment($post){
        
        $uid = $post['uid'];
        $order_id = $post['order_id'];
        
        $shop = $post['shop'];
        
        $m = M();
        $pl = M('order')->where(array('order_id'=>$order_id,'user_id'=>$uid,'pl_type'=>0))->find();
        if($pl){
            $m->startTrans();
            foreach($shop as $k=>$v){
                $add = array(
                    'project_id'=>$v['shop_id'],
                    'uid'=>$uid,
                    'content'=>$v['content'],
                    'create_time'=>  time(),
                    'stars'=>$v['stars'],
                    'imgarr'=>$v['imgs']
                );
                $comment = M('comment')->add($add);
            }     
            
            
            $save = array(
                'pl_type'=>1,
                'psfw'=>$post['psfw'],
                'shop_bz'=>$post['shop_bz'],
                'speed'=>$post['speed'],
                'order_status'=>4
            );
            $pl_order = M('order')->where(array('order_id'=>$order_id,'user_id'=>$uid))->save($save);
            
            if($comment&&$pl_order){
                $m->commit();
                return array('status'=>true,'msg'=>'评论成功','code'=>200,'data'=>$data);
            }else{
                $m->rollbank();
                return array('status'=>false,'msg'=>'评论失败','code'=>201,'data'=>$data);
            }
        }else{
            return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
        }
        
    }
    
    //确认收货
    public function confim($post){
        $uid = $post['uid'];
        $order_id = $post['order_id'];
        $result = M('order')->where(array('user_id'=>$uid,'order_id'=>$order_id))->field("order_status")->find();
        if($result['order_status']=='3'){
            $save = M('order')->where(array('order_id'=>$order_id,"user_id"=>$uid))->save(array('order_status'=>9));
            if($save){
                return array('status'=>true,'msg'=>'操作成功','code'=>200,'data'=>$data);
            }else{
                return array('status'=>false,'msg'=>'操作失败','code'=>201,'data'=>$data);
            }
        }
    }
    
    //立即购买
    public function add_order($post){
        $coupon_id = $post['coupon_id'];
        $shop_id = $post['shop_id'];
        $uid = $post['uid'];
        $address_id = $post['address_id'];
        $cart_id = $post['cart_id'];
        
        $m = M();
        $m->startTrans();
        $address = $this->address($address_id,$uid); 
        
        if($shop_id!=''){
            $shop[0] = $this->shop($shop_id);
            $del_cart=true;
        }else if($cart_id!=''){
            $shop = $this->cart($cart_id,$uid);
            $del_cart = M('shopcart')->where("id in ($cart_id)")->delete();
        }else{
            return array('status'=>false,'msg'=>'参数有误','code'=>201,'data'=>$data);
        }
  
        $order_id = $this->order_id();
  
        $add = array(
            'order_id'=>$order_id,
            'user_id'=>$uid,
//            'shop_id'=>$shop_id,
//            'shopname'=>$shop['shopname'],
//            'icon'=>$shop['icon'],
//            'shop_num'=>1,
            'order_user'=>$address['user_name'],
            'order_time'=>time(),
//            'order_money'=>$order_money,
//            'shop_money'=>$shop['money'],
            'order_status'=>1,
            'address'=>$address['province'].$address['city'].$address['area'].$address['street'].$address['address'],
            'phone'=>$address['phone'],
            'order_type'=>1,
            'longitude'=>$address['longitude'],
            'latitude'=>$address['latitude']
        );
        //生成订单
        $orders = M('order')->add($add);
        
        //添加订单商品
        $order_money=0;
        foreach($shop as $k=>$v){
            if($v['shop_num']==''){
                $v['shop_num']=1;
            }
            
            $order_shop = array(
                'order_id'=>$orders,
                'shop_id'=>$v['id'],
                'shopname'=>$v['shopname'],
                'icon'=>$v['icon'],
                'shop_num'=>$v['shop_num'],
                'shop_money'=>$v['money'],
                'add_time'=>time()
            );
            $order_money = $order_money+intval($v['shop_num'])*floatval($v['money']);
            $m->execute("update bhy_shop set nums=nums-".$v['shop_num']." where id=".$v['id']." limit 1");
            $add_shop = M('order_shop')->add($order_shop);
        }
        
        
        
        if($coupon_id){
            $coupon = $this->coupon($coupon_id,$uid);
            if($coupon){
                $order_money = floatval($order_money)-intval($coupon['money']);
            }else{
                $order_money = $order_money;
            }
        }else{
            $order_money = $order_money;
        }
        
        $new_money = M('order')->where(array('id'=>$orders))->save(array('order_money'=>$order_money));
        
        $data['order_id'] = $order_id;
        
        if($orders&&$add_shop&&$new_money&&$del_cart){
            $m->commit();
            return array('status'=>true,'msg'=>'订单生成成功','code'=>200,'data'=>$data);
        }else{
            $m->rollback();
            return array('status'=>false,'msg'=>'订单生成失败','code'=>201,'data'=>null);
        }
        
    }
    
    //临时支付
    public function pay_order($post){
        $uid = $post['uid'];
        $order_id = $post['order_id'];
        $order = M('order')->where(array('order_id'=>$order_id,'user_id'=>$uid))->field('order_money,order_status')->find();
        if($order['order_status']==1){
            $save = array(
                'pay_money'=>$order['order_money'],
                'pay_time'=>time(),
                'pay_type'=>1,
                'order_status'=>2
            );
            $result = M('order')->where(array('order_id'=>$order_id,'user_id'=>$uid))->save($save);
            if($result){
                return array('status'=>true,'msg'=>'支付成功','code'=>200,'data'=>null);
            }else{
                return array('status'=>false,'msg'=>'支付失败','code'=>201,'data'=>null);
            }
        }else{
            return array('status'=>false,'msg'=>'订单已支付或订单已取消，请勿重复操作','code'=>201,'data'=>null);
        }
    }
    
    
    //取得购物车数据
    public function cart($cart_id,$uid){
        $cart_id = explode(",", $cart_id);
        $field="sc.shop_num,bs.id,bs.shopname,bs.icon,bs.money,bs.nums";
        foreach($cart_id as $k=>$v){
            $cart[$k] = M('shopcart sc')->join("bhy_shop as bs on sc.shop_id=bs.id")->where(array('sc.id'=>$v,'sc.uid'=>$uid))->field($field)->find();
        }
        return $cart;
    }
    
    
    //取得单个商品信息
    public function shop($id){
        $field = "id,shopname,icon,money,nums";
        return M('shop')->where(array('id'=>$id,'status'=>1))->field($field)->find();
    }
    
    
    //用户地址
    public function address($id,$uid){
        $field = "province,city,area,street,address,user_name,phone,longitude,latitude";
        $address =  M('address')->where(array('id'=>$id,'user_id'=>$uid))->field($field)->find();
        $province = M('region')->where(array('id'=>$address['province']))->find();
        $city = M('region')->where(array('id'=>$address['city']))->find();
        $area = M('region')->where(array('id'=>$address['area']))->find();
        $street = M('region')->where(array('id'=>$address['street']))->find();
        $address['province'] = $province['name'];
        $address['city'] = $city['name'];
        $address['area'] = $area['name'];
        $address['street'] = $street['name'];
        return $address;
    }
    
    //用户优惠券
    public function coupon($id,$uid){
        $m = M();
        $field = "bc.max_money,bc.money,d.num";
        $coupon = M('coupon_data d')->join('bhy_coupon as bc on d.coupon_id = bc.id')->where(array('d.id'=>$id,'d.user_id'=>$uid))->field($field)->find();
        if($coupon['num']>0){
            $m->execute("update bhy_coupon_data set num=num-1 where user_id = $uid and id=$id limit 1");
            return $coupon;
        }else{
            return false;
        }
        
    }
    
    //确认收货
    public function sh(){
        
    }
    
    //生成订单号
    public function order_id(){
        $order_id = time().rand(1000,9999);
        $result = M('order')->where(array('order_id'=>$order_id))->field('id')->find();
        if($result){
            $this->order_id();
        }else{
            return $order_id;
        }
    }
    
    
    //支付宝支付
    public function alipayPay($post,$user,$order){
        $data = $this->ali_pay($order['id'],$order['price']);
        if($data){
            return array('status'=>true,'msg'=>'请求成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'暂未开通支付宝支付','code'=>201,'data'=>'');
        }
    }
	
	//组装支付宝支付参数
    public function ali_pay($order_id,$money){
		
		header("Content-type: text/html; charset=utf-8");   
		
        require_once 'alipay/config.php';
        require_once 'alipay/pagepay/service/AlipayTradeService.php';
        require_once 'alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2018080760954378";
        $aop->rsaPrivateKey = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCDvFXhR6sv+vbBcJAJ+UCDT1msUaXLMBkC9S2mHGbXXFI9sPkdeASVS6LNLxzL6tXe/WLF2y9iFZ9TT6GuygN55/UksiXnOOuUS2wFDydxwkyMGoohZkwt2N5nzpEZl5uFzjtlMtBdZ/yyqWAKaUJzQ03Sd2kVGGIwiZpx3K5SsaKguabnjzB7abozrYhmN5OfcqbVl6pqTiiCh3yK7MbAANesXEbvWu8cH0slCcQfilby/fHO88VxMVvJC03L+A929kX+UEVFPOsu/8FtUhDwaxGn451NPu18+E0crgB3jRUu/r6DhpWJ6YreN5LdyARftIV3q8/CSaf7ZwLT784hAgMBAAECggEAExTzx6cO9+s9VPXOF3PAUYTr81b8UftMlC4Zh6czilx4GTGKVCdvWoTTNy9s12jTw/ImHYzLR0TkiDfPrrbaXA0qaD9Z2C1ZimiGZBE24HaJif3KJl5gCIASbyzvvapFP9OIrPb4UZ9fbi20mK14j6OfIS5kzUZz6p3z/iw+IAIdf7fMmei9kH0Rkv+UN+Y1WVYpdeUDok1LBYtjTOMJ7u7l1+Z3c7avWvC1z6KE651iiioGmGdBzLT4ZDg1D/MeIrEEeVu3NIsfoNyK5pShWes5EIE27fyyU7KHXOeAfLOb1rlPV8jH6y0gZIzv2CtPY9zSPhgXjUphZ+tOEVYp1QKBgQD3NTpKBn7fS2hEEo+v/6Bqeim6eD38fOXW1ntXv/jpXzCtegf0YhEhkVDMUtxgVyMtBbdTkYlVPtVCsnxPC0I9H4ssYx657o5TKoqLBbUSvqda3nDmXbPW3hKKap/JPwk0r4Ry/nckeFX1VnHt3VKCwxDtAWaEZMH0ScWO4XoohwKBgQCIa8JXQDjPJTOlUfoihVJVaUFnqEeLDNpvh3gEURr/wx0AIhY1GUxqt6bAnJyIpLeTb1LXPLEPW7vUPFLS2Itfd1fr/pbz8AJEvjQeuWP8bT0e9/xssIEFZ6woKVy71xR18QF63yeO8xNJHSlivYyTPmjpeU8X0hWVsr5/OqUGFwKBgFvSmpcaeI/Ke60lU9fk1JoYTF2mAihB4EF/o4sBlJxirjsRo9jDgGd+iYuLj639T+SORf01htRyJVbD42ac4PrBN7nCC4y2roj3uURQV2TiClm3XCpFTeKW1D4zwqxGzM2UEFeCa5DRXeRmOqVG2zoU6LmQFptArcZmjIo4+F4LAoGACvjXxZq1tvtf/wDr5FDiwVnY19RyBJ4BB41WusonKMDxmSwkqxqgyciBkeZGtLrCxQnkGMmZ2Alhvv0nECw4cXIw0RtF2tLH6+18VahFgWzryIzbcYZ0qhGfiA9jlPwEekPrAY3nKZklSPEefgGOx9zyJS0LjnfoIoUgyyzVTvsCgYEAwhp4gFgObWxMNapiUeF0DzRcUqv32Rwrh+dNxNgr7gn5HyjJklITHmzsUfVFB9GdI+BgiKd0bT5KaYeifP3N846eMc+2GgBDQA6P8duy5TD9lV/cJ9LfV7MIayyq9Feb7mxF05ximDuKeuBqZm7Z6mfH0CIo22odhlY+KvW1vTM=';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5ALDQHo99MPhlnXZMe7ZYgeyKYvNQz7kod7LoLJAZ9uRyivw353h7/FKSRargKti1xf6mOI4ofpdqbzqUjPWOukDjbzqCW9KYAVST/rrKQheyWIi1I6eP1bB3Pl4+mWZ27DW9xeExsx0bNgmnt+W0nWmjL1Zq09/BCf6ad2GoK6ps1Qd++fJlMmSxKYbBrhYvpThWhs5LFdLRCNozWWO8x7MvUz1DypUDd6bj86y377QqMO8hfgpcAfsTNEP6rlAZtXF9yI5aw9MJVQYdW9w1ml8Oji+ts7j5xyZAUPV9sJSSjVw1bYi1qdHxL/SEIDrMnK5XQSJN8byewVDUIC6YwIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"法援宝\","
            . "\"subject\": \"订单支付\","
            . "\"out_trade_no\": \"$order_id\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"0.01\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";
        $request->setNotifyUrl("http://www.test2.test/home/pay/alipay_notify_url");
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
		
		file_put_contents('pay.txt',$response);

        //就是orderString 可以直接给客户端请求，无需再做处理。
        return array('status'=>true,'msg'=>'请求成功','code'=>200,'data'=>$response);
    }
    

}

?>
