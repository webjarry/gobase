<?php

namespace Common\Model;

/**
 * 收货地址操作模型
 */
class AddressModel {
    
    //我的收货地址
    public function address($post){
        $uid = $post['uid'];
        $result = M('address')->where(array('user_id'=>$uid))->select();
        $field = "name";
        foreach($result as $k=>$v){
            $province = M('region')->where(array('id'=>$v['province']))->field($field)->find();
            $result[$k]['province_name'] = $province['name'];
            
            $city = M('region')->where(array('id'=>$v['city']))->field($field)->find();
            $result[$k]['city_name'] = $city['name'];
            
            $area = M('region')->where(array('id'=>$v['area']))->field($field)->find();
            $result[$k]['area_name'] = $area['name'];
            
            $street = M('region')->where(array('id'=>$v['street']))->field($field)->find();
            $result[$k]['street_name'] = $street['name'];

        }
        if($result){
            $data = $result;
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$data);
        }
    }
    
    //设置默认地址
    public function def($post){
        $uid = $post['uid'];
        $address_id = $post['address_id'];
        $result = M('address')->where(array('user_id'=>$uid,'id'=>$address_id))->find();
        if($result){
            M('address')->where(array('user_id'=>$uid))->save(array('default'=>0));
            $save = M('address')->where(array('user_id'=>$uid,'id'=>$address_id))->save(array('default'=>1));
            if($save){
                return array('status'=>true,'msg'=>'设置成功','code'=>200,'data'=>$data);
            }else{
                return array('status'=>false,'msg'=>'设置失败','code'=>201,'data'=>$data);
            }
        }else{
            return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
        }
    }
    
    //删除地址
    public function del($post){
        $uid = $post['uid'];
        $address_id = $post['address_id'];
        $result = M('address')->where(array('user_id'=>$uid,'id'=>$address_id))->delete();
        if($result){
            return array('status'=>true,'msg'=>'删除成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'参数错误','code'=>201,'data'=>$data);
        }
        
    }
    
    //新增地址
    public function add($post){
        $post['user_id'] = $post['uid'];
        $result = M('address')->add($post);
        if($result){
            return array('status'=>true,'msg'=>'添加成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'添加失败','code'=>201,'data'=>$data);
        }
    }
    
    //修改地址
    public function edit($post){
        $post['user_id'] = $post['uid'];
        $result = M('address')->save($post);
        if($result){
            return array('status'=>true,'msg'=>'修改成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data);
        }
    }
    
    //查询省市区
    public function province($post){
        
        if($post['type']=='province'){
            $id = $post['id'];
            $where = "parent_id = $id and level = 2";
        }else if($post['type']=='city'){
            $id = $post['id'];
            $where = "parent_id = $id and level = 3";
        }else if($post['type']=='area'){
            $id = $post['id'];
            $where = "parent_id = $id and level = 4";
        }else{
            $where = "parent_id = 0 and level = 1";
        }
        
        $result = M('region')->where($where)->select();
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
        
    }
    
}