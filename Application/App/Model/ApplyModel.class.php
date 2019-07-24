<?php

namespace Common\Model;

/**
 * 订单操作模型
 */
class ApplyModel {
    
	//提现验证
	public function tx_check($post,$user_info){
		
		if($post['type'] == 1){
				
			$total_price = D('Zongcou')->zcBalance($user_info['id']);
			
			if($post['money'] > $total_price){
				return array('status'=>false,'code'=>201,'msg'=>'提现金额不能大于众筹余额！');
			}
			
		}else{
			if($post['money'] > $user_info['balance']){
				return array('status'=>false,'code'=>201,'msg'=>'提现金额不能大于余额！');
			}
			
		}

		if(empty($post['pay_type'])){
			return array('status'=>false,'code'=>201,'msg'=>'请选择提现方式！');
		}
		
		if($post['type'] == 1 && empty($user_info['zfb'])){
			
			return array('status'=>false,'code'=>201,'msg'=>'您尚未绑定支付宝，无法提现');
			
		}
		
		if($post['type'] != 1){
			
			if($post['pay_type'] == 2 && empty($user_info['zfb'])){
				
				return array('status'=>false,'code'=>201,'msg'=>'您尚未绑定支付宝，无法提现');
				
			}
			if($post['pay_type'] == 1 && empty($user_info['cardno'])){
				
				return array('status'=>false,'code'=>201,'msg'=>'您尚未绑定银行卡，无法提现');
				
			}
			
			
		}
		
		$post['cardno'] = $post['pay_type'] == 1?$user_info['cardno']:$user_info['zfb'];
		
		$post['balance'] = $user_info['balance'];
		
		return array('status'=>true,'code'=>200,'post'=>$post);
			
		
	}
	//提现
	public function tixian($post,$user_info,$return_id=0,$type){
		
		$water = array(
			'uid'=>$user_info['id'],
			'type'=>2,
			'user_type'=>$type,
			'water_type'=>3,//提现
			'new_price'=> $post['money'],
			'create_time'=>time(),
			'remark'=>'余额提现',
			'return_id'=>$return_id,
			'user_money'=>$user_info['balance']-$post['money']
		);
		if($type == 1){
			M('usermember')->where('id='.$user_info['id'])->save(array('balance'=>$user_info['balance']-$post['money']));
		}else if($type == 2){
			M('staff')->where('id='.$user_info['id'])->save(array('balance'=>$user_info['balance']-$post['money']));
		}
		
		$result = M('flowater')->add($water);
		
		
	}

}

?>
