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
    
    
    
    

}

?>
