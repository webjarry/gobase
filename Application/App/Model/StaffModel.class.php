<?php

namespace Common\Model;

/**
 * 收货地址操作模型
 */
class StaffModel {
    
    //配送端首页
    public function index($post){
        $uid = $post['uid'];
        $user = M('staff')->where(array('id'=>$uid))->find();
        $user['icon'] = picture($user['icon']);
        if($user){
            $user['dqh'] = M('order')->where(array('ps_id'=>$uid,'order_status'=>2))->count('id');
            $user['psz'] = M('order')->where(array('ps_id'=>$uid,'order_status'=>3))->count('id');
            $user['yps'] = M('order')->where(array('ps_id'=>$uid,'order_status'=>4))->count('id');
            $user['all'] = M('order')->where(array('ps_id'=>$uid))->count('id');
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$user);
        }
    }
    
    
    //修改信息
    public function edit_user($post){
        $uid = $post['uid'];
        $save = M('staff')->where(array('id'=>$uid))->save($post);
        if($save){
            return array('status'=>true,'msg'=>'修改成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data);
        }
    }
    
    //我的订单
    public function order($post){
        $uid = $post['uid'];
        $type = $post['type'];
        $where = "ps_id = $uid";
        if($type=='dps'){
            $where = $where." and order_status = 2";
        }else if($type=='psz'){
            $where = $where." and order_status = 3";
        }else if($type=='yps'){
            $where = $where." and order_status = 4";
        }
        if($page){
            $end = $page*15;
            $start = $end-15;
        }else{
            $start = 0;
        }
        $result = M('order')->where($where)->limit($start,15)->select();
        foreach($result as $k=>$v){
            $result[$k]['shop'] = $this->fors(M('order_shop')->where(array('order_id'=>$v['id']))->field("shop_id,shopname,icon,shop_num,shop_money")->select());
        }
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
    //订单详情
    public function order_detile($post){
        $uid = $post['uid'];
        $order_id = $post['order_id'];
        $result = M('order')->where(array( 'order_id'=>$order_id,'ps_id'=>$uid))->find();
        $result['shop'] = $this->fors(M('order_shop')->where(array('order_id'=>$result['id']))->field("shop_id,shopname,icon,shop_num,shop_money")->select());
        if($result){
            return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
        }else{
            return array('status'=>false,'msg'=>'订单不存在','code'=>201,'data'=>$result);
        }
    }
    
    //确认取货
    public function qrqh($post){
        if(IS_POST){
            $order_id = $post['order_id'];
            $uid = $post['uid'];
            $order = M('order')->where(array('order_id'=>$order_id,'ps_id'=>$uid,'order_status'=>2))->field("id")->find();
            if($order){
                $save = M("order")->where(array('order_id'=>$order_id,'ps_id'=>$uid,'order_status'=>2))->save(array('order_status'=>3));
                if($save){
                    return array('status'=>true,'msg'=>'取货成功','code'=>200,'data'=>$data);
                }else{
                    return array('status'=>false,'msg'=>'系统错误','code'=>201,'data'=>$data);
                }
            }else{
                return array('status'=>false,'msg'=>'订单不存在','code'=>201,'data'=>$data);
            }
        }
    }
    
    //配送完成
    public function order_down($post){
        if(IS_POST){
            $order_id = $post['order_id'];
            $uid = $post['uid'];
            $order = M('order')->where(array('order_id'=>$order_id,'ps_id'=>$uid,'order_status'=>3))->field("id")->find();
            if($order){
                $save = M("order")->where(array('order_id'=>$order_id,'ps_id'=>$uid,'order_status'=>3))->save(array('order_status'=>9));
                if($save){
                    return array('status'=>true,'msg'=>'配送完成','code'=>200,'data'=>$data);
                }else{
                    return array('status'=>false,'msg'=>'系统错误','code'=>201,'data'=>$data);
                }
            }else{
                return array('status'=>false,'msg'=>'订单不存在','code'=>201,'data'=>$data);
            }
        }
    }

    
    public function fors($array){
        foreach($array as $k=>$v){
            $array[$k]['icon'] = picture($v['icon']);
        }
        return $array;
    }
    
    
}