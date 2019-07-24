<?php

namespace Common\Model;

/**
 * 订单操作模型
 */
class CartModel {
    
     //我的购物车
    public function cart($post){
        $uid = $post['uid'];
        $field = "c.id,bs.id as shop_id,bs.shopname,bs.icon,bs.money,c.shop_num";
        $cart = M('shopcart c')->join("bhy_shop as bs on c.shop_id = bs.id")->where(array('uid'=>$uid))->field($field)->select();
        $cart = $this->fors($cart);
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$cart);
    }
    
    //加入购物车
    public function add_cart($post){
        $shop_id = $post['shop_id'];
        $uid = $post['uid'];
        $cart = M('shopcart')->where(array('shop_id'=>$shop_id,'uid'=>$uid))->find();
        if(!empty($cart)){
            $m = M();
            $result = $m->execute("update bhy_shopcart set shop_num=shop_num+1 where shop_id = $shop_id and uid = $uid limit 1");
        }else{
            $add = array(
                'shop_id'=>$shop_id,
                'uid'=>$uid,
                'shop_num'=>1,
                'add_time'=>time()
            );
            $result = M('shopcart')->add($add);
            
        }
        
        if($result){
            return array('status'=>true,'msg'=>'添加成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'添加失败','code'=>201,'data'=>$data);
        }
        
    }
    
    //删除购物车
    public function del_cart($post){
        $cart_id = $post['cart_id'];
        $uid = $post['uid'];
        $result = M('shopcart')->where("uid = $uid and id in($cart_id)")->delete();
        if($result){
            return array('status'=>true,'msg'=>'删除成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'删除失败','code'=>201,'data'=>$data);
        }
    }
    
    //转换图片路径
    public function fors($array){
        foreach($array as $k=>$v){
            $array[$k]['icon'] = picture($v['icon']);
        }
        return $array;
    }

}

?>
