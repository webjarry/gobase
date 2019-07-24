<?php

namespace Common\Model;

/**
 * 优惠券模型
 */
class CouponModel {

    //积分兑换
    public function coupon($post){
        
        $uid = $post['uid'];
        $where = "status=1";
        $score = M('usermember')->where(array('id'=>$uid))->field("score")->find();
        $coupon_data = M('coupon_data')->where(array('user_id'=>$uid))->select();
        
        if(!empty($coupon_data)){
            foreach($coupon_data as $vl){
                $where = $where." and id!=".$vl['coupon_id'];
            }
        }
        
        $result = M('coupon')->where($where)->select();
        
        
        
        $data['result'] = $result;
        $data['score'] = $score;
        return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        
    }
    
    //立即兑换
    public function coupon_dh($post){
        $m = M();
        $m->startTrans();
        $uid = $post['uid'];
        $coupon_id = $post['coupon_id'];
        $nums = $m->table('bhy_coupon')->where(array("id"=>$coupon_id))->field('nums,score')->find();
        $user_score = $m->table('bhy_usermember')->where(array("id"=>$uid))->field('score')->find();
        $coupon_data = $m->table('bhy_coupon_data')->where(array('user_id'=>$uid,'coupon_id'=>$coupon_id,'status'=>0))->find();
        $score = $nums['score'];
        if($nums['nums']<=0){
            return array('status'=>false,'msg'=>'数量不足','code'=>201,'data'=>$data);
        }else if($user_score['score']<$score){
            return array('status'=>false,'msg'=>'用户积分不足','code'=>201,'data'=>$data);
        }else if($coupon_data){
            return array('status'=>false,'msg'=>'您已经兑换过该优惠券了','code'=>201,'data'=>$data);
        }else{
        
            $new_nums = intval($nums['nums'])-1;
            $new_score = intval($user_score['score'])-$score;
            
            $add=array(
                'user_id'=>$uid,
                'coupon_id'=>$coupon_id,
                'add_time'=>time(),
                'num'=>1
            );
            
            $coupon = $m->table('bhy_coupon')->where(array("id"=>$coupon_id))->save(array("nums"=>$new_nums));
            $user = $m->table('bhy_usermember')->where(array('id'=>$uid))->save(array("score"=>$new_score));
            $add_coupon = $m->table('bhy_coupon_data')->add($add);
            
            $pay = array(
                'money'=>$score,
                'type'=>2,
                'm_type'=>1,
                'status'=>1,
                'add_time'=>time(),
                'user_id'=>$uid,
                'order_type'=>2
            );
            
            $pay_log = $m->table('bhy_pay_log')->add($pay);
            
            if($coupon&&$user&&$add_coupon&&$pay_log){
                $m->commit();
                return array('status'=>true,'msg'=>'兑换成功','code'=>200,'data'=>$data);
            }else{
                $m->rollback();
                return array('status'=>false,'msg'=>'兑换失败','code'=>201,'data'=>$data);
            }
            
        }
    }
    
    //我的优惠券
    public function user_coupon($post){
        $uid = $post['uid'];
        $m = M();
        $where = "cd.user_id=".$uid;
        if($post['type']=='wsy'){
            $where = $where." and cd.status = 0";
        }else if($post['type']=='ysy'){
            $where = $where." and cd.status = 1";
        }else if($post['type']=='ygq'){
            $time = time();
            $where = $where." and cd.end_time<".$time;
        }
        $result = $m->query("select bc.id,bc.max_money,bc.money,bc.coupon_name,bc.end_time,cd.status from bhy_coupon_data as cd left join bhy_coupon as bc on cd.coupon_id=bc.id where ".$where." order by cd.add_time desc");
        if($result){
            $data = $result;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    //领劵中心
    public function coupon_mf($post){
        $uid = $post['uid'];
        $where['status'] = 1;
        $where['score'] = 0;
        $result = M('coupon')->where($where)->select();
        $user_coupon = M('coupon_data')->where(array('user_id'=>$uid))->select();
        foreach($user_coupon as $us){
            foreach($result as $k=>$v){
                if($v['id']==$us['coupon_id']){
                    $result[$k]['ydh'] = 1;
                }
            }
        }
        if($result){
            $data = $result;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    //立即领取
    public function coupon_lq($post){
        $m = M();
        $m->startTrans();
        $uid = $post['uid'];
        $coupon_id = $post['coupon_id'];
        $nums = $m->table('bhy_coupon')->where(array("id"=>$coupon_id))->field('nums,score')->find();
        $coupon_data = $m->table('bhy_coupon_data')->where(array('user_id'=>$uid,'coupon_id'=>$coupon_id,'status'=>0))->find();
        $score = $nums['score'];
        if($nums['nums']<=0){
            return array('status'=>false,'msg'=>'数量不足','code'=>201,'data'=>$data);
        }else if($coupon_data){
            return array('status'=>false,'msg'=>'您已经领取过该优惠券了','code'=>201,'data'=>$data);
        }else{
            $new_nums = intval($nums['nums'])-1;
            
            $add=array(
                'user_id'=>$uid,
                'coupon_id'=>$coupon_id,
                'add_time'=>time(),
                'num'=>1
            );
            
            $coupon = $m->table('bhy_coupon')->where(array("id"=>$coupon_id))->save(array("nums"=>$new_nums));
            $add_coupon = $m->table('bhy_coupon_data')->add($add);
            
            if($coupon&&$add_coupon){
                $m->commit();
                return array('status'=>true,'msg'=>'领取成功','code'=>200,'data'=>$data);
            }else{
                $m->rollback();
                return array('status'=>false,'msg'=>'领取失败','code'=>201,'data'=>$data);
            }
            
        }
    }
    
}
