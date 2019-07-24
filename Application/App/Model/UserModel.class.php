<?php

namespace Common\Model;

/**
 * 用户个人中心模型
 */
class UserModel {
    
    //读取用户信息（）
    public function user($post){
        $uid = $post['uid'];

        $result = M('usermember')->where(array('id'=>$uid))->find();

        //$result['dfk'] = M('order')->where(array('user_id'=>$uid,'order_status'=>1))->count('id');
        if($result){
            $result['icon'] = picture($result['icon']);

            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$result);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$result);
        }
    }
    

    public function picture($id) {

        $picture = M('picture')->find($id);

        if (empty($picture['path'])) {

            return '/Public/Home/images/mr.jpg';
        } else {
            return $picture['path'];
        }
    }
    
    
    
    //我的钱包
    public function user_money($post){
        $uid = $post['uid'];
        $field = "balance,score";
        $coupan = M('coupon_data')->where(array("user_id"=>$uid))->count('id');
        $result = M('usermember')->where(array("id"=>$uid))->field($field)->find();
        if($result){
            $data = $result;
            $data['coupan'] = $coupan;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    //充值说明
    public function pay_explanin(){
        $result = M('pay_explanin')->find();
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
    //充值
    public function pay($post){
        $uid = $post['uid'];
        $money = intval($post['money']);
        $type = $post['type'];
        if($type=='ali'||$type=''){
            $type=1;
            $content = "支付宝充值";
        }else if($type=='wx'){
            $type=2;
            $content = "微信充值";
        }
        
        $m = M();
        $m->startTrans();
        $balance = M('usermember')->where(array('id'=>$uid))->field('balance')->find();
        $new_balance = floatval($balance['balance'])+$money;
        $save = M('usermember')->where(array('id'=>$uid))->save(array('balance'=>$new_balance));
        $add = array(
            'money'=>$money,
            'type'=>1,
            'm_type'=>2,
            'status'=>2,
            'content'=>$content,
            'add_time'=>time(),
            'pay_type'=>$type,
            'uid'=>$uid,
            'order_type'=>1
        );
        $pay_log = M('water')->add($add);
        if($save&&$pay_log){
            $m->commit();
            return array('status'=>true,'msg'=>'充值成功','code'=>200,'data'=>$data);
        }else{
            $m->rollback();
            return array('status'=>false,'msg'=>'充值失败','code'=>201,'data'=>$data);
        }
        
    }
    
    //充值记录
    public function pay_log($post){
        $uid = $post['uid'];
        
        $where = "user_id = $uid and m_type=2 and order_type=1";
        if($post['start_time']!=''){
            $where = $where." and add_time>=".$post['start_time'];
        }
        if($post['end_time']!=''){
            $where = $where." and add_time<=".$post['end_time'];
        }
        $field = "add_time,money,type";
        $page = $post['page'];
        if($page==1){
            $end = $page*5;
            $start = $end-5;
        }else{
            $start = 0;
            $end = 5;
        }
        $result = M('pay_log')->where($where)->field($field)->order('add_time desc')->limit($start,5)->select();
        if($result){
            $data = $result;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    //我的积分
    public function score($post){
        $uid = $post['uid'];
        $where['user_id'] = $uid;
        $where['order_type'] = 2;
        $field = "add_time,type,money";
        $page = $post['page'];
        if($page==''){
            $start = 0;
            $end = 9;
        }else{   
            $end = intval($page)*10 ;
            $start = $end-10;
        }
        $result = M('pay_log')->where($where)->field($field)->order('add_time desc')->limit($start,10)->select();
        $score = M('usermember')->where(array('id'=>$uid))->field("score")->find();
        $data['result'] = $result;
        $data['score'] = $score;
        return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
    }
    
    
    //反馈类型
    public function opinion_type(){
        $result = M('opinion_type')->select();
        return array('status'=>true,'msg'=>'获取成功','code'=>200,'data'=>$result);
    }
    
    
    //意见反馈
    public function opinion($post){
        $post['add_time'] = time();
        $result = M('opinion')->add($post);
        if($result){
            return array('status'=>true,'msg'=>'反馈成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'反馈失败','code'=>201,'data'=>$data);
        }
    }
    


    //我的收藏
    public function collection($post){
        $uid = $post['uid'];
        $page = $post['page'];
        if($page>0){
            $end = $page*15;
            $start = $end-14;
            $end = $end-1;
        }else{
            $start = 0;
            $end = 14;
        }
        $field = "c.id,sp.id as shop_id,sp.shopname,sp.icon,sp.money";
        $result = M('collection c')->join('bhy_shop as sp on c.shop_id = sp.id')->where(array('c.user_id'=>$uid))->field($field)->limit($start,$end)->select();
        if($result){
            foreach($result as $k=>$v){
                $result[$k]['icon'] = $this->picture($v['icon']);
                $result[$k]['yx'] = $this->yx($v['shop_id']);
            }
            $data  = $result;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    
    //删除收藏
    public function del_collection($post){
        $id = $post['id'];
        $uid = $post['uid'];
        $where  = "user_id = $uid and shop_id in ($id)";
        $result = M('collection')->where($where)->delete();
        if($result){
            return array('status'=>true,'msg'=>'删除成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'删除失败','code'=>201,'data'=>$data);
        }
    }
    
    //修改登录密码
    public function edit_pwd($post){
        $uid = $post['uid'];
        $yzm = S($post['phone']);
        if($yzm!=$post['yzm']){
            return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data);
        }else{
            $old_pwd = md5(md5($post['old_pwd']));
            $result = M('usermember')->where(array('id'=>$uid,'password'=>$old_pwd))->find();
            if($result){
                $new_pwd = md5(md5($post['new_pwd']));
                $pwd = M('usermember')->where(array('id'=>$uid))->save(array('password'=>$new_pwd,'no_pass'=>$post['new_pwd']));
                if($pwd){
                    S($post['phone'],null);
                    return array('status'=>true,'msg'=>'修改成功','code'=>200,'data'=>$data);
                }else{
                    return array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data);
                }
            }else{
                return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
            }
        }
        
    }
    
    //设置支付密码
    public function set_zfpwd($post){
        $uid = $post['uid'];
        $phone = $post['phone'];
        $yzm = $post['yzm'];
        $pwd = $post['pwd'];
        $qr_pwd = $post['qr_pwd'];
        $ser_yzm = S($phone);
        if($yzm!=$ser_yzm){
            return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data);
        }else if($pwd != $qr_pwd){
            return array('status'=>false,'msg'=>'两次密码输入不一致','code'=>201,'data'=>$data);
        }else{
            $md5 = md5(md5($pwd));
            $resutl = M('usermember')->where(array('id'=>$uid,'mobile'=>$phone))->save(array('pay_pas'=>$md5));
            if($resutl){
                S($phone,null);
                return array('status'=>true,'msg'=>'设置成功','code'=>200,'data'=>$data);
            }else{
                return array('status'=>false,'msg'=>'系统错误','code'=>201,'data'=>$data);
            }
        }
    }
    
    //修改支付密码
    public function edit_pay($post){
        $uid = $post['uid'];
        $yzm = S($post['phone']);
        if($yzm!=$post['yzm']){
            return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data);
        }else{
            $old_pwd = md5(md5($post['old_pwd']));
            $result = M('usermember')->where(array('id'=>$uid,'pay_pas'=>$old_pwd))->find();
            if($result){
                $new_pwd = md5(md5($post['new_pwd']));
                $pwd = M('usermember')->where(array('id'=>$uid))->save(array('pay_pas'=>$new_pwd));
                if($pwd){
                    S($post['phone'],null);
                    return array('status'=>true,'msg'=>'修改成功','code'=>200,'data'=>$data);
                }else{
                    return array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data);
                }
            }else{
                return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
            }
        }
    }
    
    
    
    //月销计算
    public function yx($id){
        $m = M();

        $id = $v['id'];
        $sell = $m->query("select count(id) as sell from bhy_order where shop_id = $id and date_sub(CURDATE(), INTERVAL 30 DAY) <= date(FROM_UNIXTIME(`order_time`));");
        if($sell!=''){
              return $sv['sell'];
        }else{
            return 0;
        }
    }
    

}

?>
