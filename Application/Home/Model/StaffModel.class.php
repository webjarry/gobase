<?php

namespace Home\Model;
use Think\Model;

/**
 * 分类模型
 */
class StaffModel extends Model{
	
	
    public function wdz_price($uid){
		
		$map = array();
		$map['sid'] = array('eq',$uid);
		$map['is_transfer'] = array('eq',0);
		$map['is_confirm'] = array('eq',1);
		$map['status'] = array('eq',1);
        $wdz=M('order')->where($map)->select();
        if($wdz){
            $money=0;
            foreach($wdz as $k=>$v){
                $money+=$v[s_price];
            }
			return $money;
        }else{
            return 0;
        }
		
	}


}
