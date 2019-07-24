<?php

namespace App\Controller;

class UserController extends HomeController {

    //public $param;

    public function _initialize() {

        parent::_initialize();

//        $list = file_get_contents('php://input');
//        $param = json_decode($list, true);
//        $this->param = $param;
        
          $this->checkIndividual();
        //$this->checkUid($param);
    }
    public function me(){
        if($this->type==1){
            $m=M('usermember');
        }else{
            $m=M('staff');
        }
        $user=$m->find($this->uid);
        $user[icon]=picture($user[icon]);
        if($this->type==1){
            $user[phone]=yc_phone($user[phone]);
        }else{
            $user[phone]=$user[nickname].'律师';
        }
        return $user;
    }
    
    public function uover(){
        $post = $this->param;
		
		$map['orderno'] = array('eq',$post[orderno]);
		
		$order = M('order')->where($map)->find();
		
		//添加消息
		$message = array(
			'user_id' =>$order['sid'],
			'user_type'=>2,
			'message'=>"您的咨询订单用户已确认完成",
			'add_time'=>time(),
			'type'=>3,  //用户确认订单
			'c_type1'=>1,  //咨询订单
			'return_id'=>$order['id']
		);
		
		$user=sinfo($order['sid']);
		
		$water = array(
			'uid'=>$order['sid'],
			'type'=>1,
			'user_type'=>2,
			'water_type'=>19,//订单到账
			'new_price'=> $order['s_price'],
			'create_time'=>time(),
			'remark'=>'订单到账',
			'return_id'=>$order['id'],
			'user_money'=>$user['balance']+$order['s_price']
		);
		
		M('mymessage')->add($message);

        $arr = M('ask')->where($map)->save(array('uover'=>1,'sover'=>1,'status'=>4,'state'=>3,'is_confirm'=>1));

		$res1 = M('order')->where($map)->save(array('is_confirm'=>1,'is_transfer'=>1,'update_time'=>time()));
		$res2 = M('staff')->where('id='.$order['sid'])->save(array('balance'=>$water['user_money']));
		$res3 = M('flowater')->add($water);
		
       
        if($arr){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'确认成功','data'=>$arr));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'','data'=>$sql));
        }

    }
    public function fxdetail(){
        $post = $this->param;
        if($post[type]==1){
            $fx=M('legalpaper')->where("id=".$post[id])->find();

            $fx[time] = date('Y-m-d H:i', $fx[create_time]);
            $fx[otype] = '法律告知函';
            if( $fx[status]==1){
                $fx[ostate] = '未处理';
            }else{
                $fx[ostate] = '已处理';
            }
        }else{
            $fx=M('fenxi')->where("id=".$post[id])->find();

            $fx[time] = date('Y-m-d H:i', $fx[create_time]);
            $fx[otype] = '律师分析报告';
            if( $fx[status]==1){
                $fx[ostate] = '未处理';
            }else{
                $fx[ostate] = '已处理';
            }
        }

        if($fx){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$fx));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>123,'data'=>123));
        }

    }
    public function knowledge(){
        $post = $this->param;

        $arr=M('zhishi')->where("uid=".$this->uid)->order('id desc')->select();
        foreach($arr as $k=>$v) {
             $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
        }
       
        if($arr){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$arr));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>123,'data'=>123));
        }

    }
	
	public function bq_detail(){
        $post = $this->param;

        $arr=M('baoquan')->where("uid=".$this->uid)->order('id desc')->select();
        foreach($arr as $k=>$v) {
             $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
        }
       
        if($arr){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$arr));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>123,'data'=>123));
        }

    }
    public function kdetail(){
        $post = $this->param;

        $result=M('zhishi')->find($post[id]);
        $result[time]=date('Y-m-d',$result[create_time]);
        if($result[status]==1){
            $result[otype]='等待客服人员回复';
        }elseif($result[status]==2){
            $result[otype]='已完成';
        }else{

            $result[otype]='未受理';
        }

        
        $user=M('usermember')->find($result[uid]);
        //$result[name]=$user[nickname];
        $result[phone]=$user[phone];

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>123,'data'=>123));
        }

    }

    public function tuwen(){
        $post = $this->param;

        if (!$post['id'] ) {
            $this->appReturn(array('status' => false,'code'=>201, 'msg' => '未完善信息'));
        }

        $a=M('tuwen')->where("tid=".$post[id])->find();
        if($this->type==1){
            $a[icon]=expic($a[uicon]);
        }else{
            $a[icon]=expic($a[icon]);
        }
      
        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$a));

    }
    public function qiyesure(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($post[type]==1){
            $m=M('legalpaper');
        }else{
            $m=M('fenxi');
        }
        $a=$m->find($post[id]);
        $re=$m->where('id='.$post[id])->save(array('state'=>2));
        $order=M('order')->where('orderno='.$a[orderno])->save(array('status'=>2));
       if($re){

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>123));
        }else{
            $this->appReturn(array('status'=>false,'code'=>200,'msg'=>$m->getLastSql(),'data'=>$result));
        }

    }
    public function usecp(){
        $post = $this->param;
        if(!$post['sid'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		
		if($post['coupon_id']){
			
			$map['id'] = array('eq',$post['coupon_id']);
			
		}
		$map['uid'] = array('eq',$this->uid);
		$map['sid'] = array('eq',$post['sid']);
		$map['type'] = array('eq',$post['type']);
		$map['last_time'] = array('gt',time());
		
		
        if($cp=M('coupon')->where($map)->find()){

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$cp[money]));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('coupon')->getLastSql(),'data'=>$result));
        }

    }
    public function yepay(){
        $post = $this->param;

        if(!$post['orderno']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }

        if($this->type==1){
            $m=M('usermember');
        }else{
            $m=M('staff');
        }
        $user=$m->find($this->uid);

        $orderno=$post['orderno'];
		$map['orderno'] = array('eq',$orderno);
        $order=M('order')->where($map)->find();
        if($order[type] != 8){
            if($order['status']==1){
                $this->appReturn(array('status'=>true,'code'=>201,'msg'=>'该订单已支付完成','data'=>$orderno));
            }
        }


        $money=$order['pay_price'];

        $balance=$user['balance'];

        if($money>$balance){
            $this->appReturn(array('status'=>true,'code'=>201,'msg'=>'余额不足','data'=>$orderno));
        }else {

            $jian = $m->where('id=' . $order['uid'])->save(array('balance' => $user[balance] - $money));
			//$sql = $m->getLastSql();

            if($order[status]==0){
                $save['status'] = 1;

				$save['update_time'] = time();
				if($order['type'] == 10){
					$save['is_transfer'] = 1;
					$save['is_confirm'] = 1;
				}
                $changeorder = M('order')->where('orderno=' . $orderno)->save($save);
            }


            if ($jian) {

                if ($order['type'] == 1) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>1,//咨询费支付
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'咨询费支付',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->alipay_info($order);

                } elseif ($order['type'] == 2 || $order['type'] == 3 || $order['type'] == 4 || $order['type'] == 5 || $order['type'] == 6 || $order['type'] == 7) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>5,//咨询订单支付
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'咨询订单支付',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->ask($order);

                } elseif ($order['type'] == 8 && $order['status']==0) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>6,//委托订单支付
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'委托订单支付',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->wtorder($order);

                } elseif ($order['type'] == 9) {
                    $this->chongzhi($order);

                } elseif ($order['type'] == 10) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>4,//送心意
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'送心意',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
					
                    $this->mind_pay($order);

                } elseif ($order['type'] == 11 || $order['type'] == 12) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>$order['type']==11?13:14,//升级VIP
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>$order['type']==11?'升级普通VIP':'升级高级VIP',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->buyvip1($order);

                } elseif ($order['type'] == 14) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>10,//购买合同
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'购买合同',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->hetong($order);

                } elseif ($order['type'] == 15) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>11,//众筹捐款
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'购买合同',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->donation($order);

                } elseif ($order['type'] == 16) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>12,//法律告知函
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'购买合同',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->legalpaper($order);

                } elseif ($order['type'] == 17) {
                    $this->shengji($order);

                } elseif ($order['type'] == 19) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>15,//互助缴费
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'互助缴费',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->familysuc($order);

                } elseif ($order['type'] == 23) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>16,//众筹委托律师费
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'众筹委托律师费',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->wtfee($order);

                } elseif(($order['type'] == 8 && $order['status'] == 1) || $order['type'] == 21){
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>9,//二次支付律师费
						'new_price'=> $order['pay_price']-50,
						'create_time'=>time(),
						'remark'=>'二次支付律师费',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->wtfee($order);

                } elseif ($order['type'] == 24) {
					$water = array(
						'uid'=>$order['uid'],
						'type'=>2,
						'user_type'=>$order['utype'],
						'water_type'=>17,//充值互助金
						'new_price'=> $order['pay_price'],
						'create_time'=>time(),
						'remark'=>'充值互助金',
						'return_id'=>$order['id'],
						'user_money'=>$user['balance']-$order['pay_price']
					);
					M('flowater')->add($water);
                    $this->reward($order);

                }
				if($order['type'] == 14){
					
					$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'支付完成，请前往我的界面企业服务包订单中的合同下载里面去下载','data'=>$orderno));
					
				}else{
					
					$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'余额支付成功','data'=>$orderno));
					
				}

                
            } else {
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'余额支付失败','data'=>$sql));
            }
        }

    }
    public function alipay_info($order){
        $fask = M('fask')->where('orderno='.$order['orderno'])->find();
        if(!$fask){exit('error');}

        M('fask')->where('id='.$fask['id'])->save(array('is_pay'=>1,'update_time'=>time()));


    }
    public function ask($order){
        $fask = M('ask')->where('orderno='.$order['orderno'])->find();
		//添加消息
		$ad = array(
            'user_id' =>$fask['sid'],
			'user_type'=>2,
            'message'=>"您有新订单了，快去看看吧 ！",
            'add_time'=>time(),
            'type'=>7,  //新订单
            'c_type1'=>1,  //下单
            'return_id'=>$order['id']
        );
		M("mymessage")->add($ad);
		
		if($order['type'] == 7){

			$res2 = M('ask')->where('id='.$fask['id'])->save(array('payed'=>1,'update_time'=>time(),'status'=>4,'update_time'=>time(),'state'=>3,'uover'=>1,'sover'=>1,'is_confirm'=>1));
			
			$map['orderno'] = array('eq',$fask['orderno']);
			$res2 = M('order')->where($map)->save(array('is_confirm'=>1,'update_time'=>time()));
		}else{
			
			$res2 = M('ask')->where('id='.$fask['id'])->save(array('payed'=>1,'update_time'=>time(),'status'=>1));
			
		}

        

        if($cp=M('coupon')->where("uid=".$fask['uid']." and sid=".$fask['sid']." and type=".$fask['type']." and last_time>".time())->find()){
            M('coupon')->delete($cp[id]);
        }

    }
    public function wtorder($order){
		
        $re = M('wt')->where('orderno='.$order['orderno'])->save(array('payed'=>1,'status'=>2));

    }
    public function wtfee($order){
		
		if(($order['type'] == 8 && $order['status'] == 1) || $order['type'] == 21){
			
			$map['orderno'] = array('eq',$order['orderno']);
			$re = M('wt')->where($map)->save(array('twopay'=>1));
			
		}else{
			
			$map['orderno'] = array('eq',$order['orderno']);
			$re = M('wt')->where($map)->save(array('status'=>4));
			$res2 = M('order')->where($map)->save(array('is_confirm'=>1,'update_time'=>time()));
			
		}

        $wt = M('wt')->where($map)->find();
        $money = M('wtjoin')->where("tid=".$wt[id]." and choose=1")->find()[money];
        M('order')->where($map)->save(array('pay_price'=>$money,'status'=>1,'is_confirm'=>1));

    }
    public function chongzhi($order){
        if($order[utype]==1){
            $m=M('usermember');
        }else{
            $m=M('staff');
        }
        $user=$m->find($order['uid']);
        $res2 = $m->where('id='.$order['uid'])->save(array('balance'=>$user['balance']+$order['pay_price']));

    }
    public function reward($order){
        $m=M('usermember');
		
        $user=$m->find($order['uid']);
		
		if(empty($user['hz_time'])){
			
			$res1 = M('usermember')->where('id='.$user['id'])->save(array('hz_time'=>time()));
			
		}
        $res2 = $m->where('id='.$order['uid'])->save(array('reward'=>$user['reward']+$order['pay_price']));

    }

    public function mind_pay($order){
        $sid=$order['sid'];
        $user = M('staff')->find($sid);
        $res2 = M('staff')->where('id='.$sid)->save(array('balance'=>$user['balance']+$order['pay_price']));
		
		$water = array(
			'uid'=>$sid,
			'type'=>1,
			'user_type'=>2,
			'water_type'=>4,//送心意
			'new_price'=> $order['pay_price'],
			'create_time'=>time(),
			'remark'=>'送心意',
			'return_id'=>$order['id'],
			'user_money'=>$user['balance']+$order['pay_price']
		);
		
		//添加消息
		$message = array(
			'user_id' =>$sid,
			'user_type'=>2,
			'message'=>"您有会员向您表示的心意，请立即查看 ！",
			'add_time'=>time(),
			'type'=>6,  //送心意
			'c_type1'=>1,  //咨询订单
			'return_id'=>$order['id']
		);

        if($res2){
            $add['uid']=$order['uid'];
            $add['sid']=$order['sid'];
            $add['money']=$order['pay_price'];
            $add['create_time']=time();
            $add['date_time']=date('Y-m');
            M('mind')->add($add);
			M('mymessage')->add($message);
			M('flowater')->add($water);

        }

    }
    public function buyvip1($order){
        if($order[type]==11){
            $vip=1;
        }else if($order[type]==12){
            $vip=2;
        }

        if($order[utype]==1){
            $m=M('usermember');
        }else{
            $m=M('staff');
        }
        $user=$m->find($this->uid);

        $gz=M('feeserver')->find(2);
        $ht=M('feeserver')->find(3);
        $fx=M('feeserver')->find(4);
        if($vip==1){
            $gznum=$gz[jichu];
            $htnum=$ht[jichu];
            $fxnum=$fx[jichu];
        }else{
            $gznum=$gz[gaoji];
            $htnum=$ht[gaoji];
            $fxnum=$fx[gaoji];
        }

        $m->where('id='.$order['uid'])->save(array('vip'=>$vip,'user_time'=>time()+12*30*24*3600,'gznum'=>$user[gznum]+$gznum,'htnum'=>$user[htnum]+$htnum,'fxnum'=>$user[fxnum]+$fxnum));
        file_put_contents('sql.txt',$m->getLastSql());


    }
    public function hetong($order){
        $tid=$order['tid'];
        $hetong= M('hetong')->find($tid);

        $add['orderno']=$order['orderno'];
        $add['uid']=$order['uid'];
        $add['tid']=$order['tid'];
        $add['type']=$hetong['type'];
        $add['money']=$order['pay_price'];
        $add['status']=1;
        $add['create_time']=time();
        $add['date_time']=date('Y-m');
        M('hetonguser')->add($add);



        //exit('success');
    }
    public function donation($order){
        $add['uid']=$order['uid'];
        $add['sid']=$order['sid'];
        $add['money']=$order['pay_price'];
        $add['create_time']=time();
        $add['date_time']=date('Y-m',time());
        M('mind')->add($add);

    }
    public function legalpaper($order){
        $fask = M('legalpaper')->where('orderno='.$order['orderno'])->find();

        $res2 = M('legalpaper')->where('id='.$fask['id'])->save(array('payed'=>1,'update_time'=>time()));

    }
    public function shengji($order){
        M('usermember')->where("id=".$order['uid'])->save(array('xs'=>1));

    }
    public function familysuc($order){
        $user=M('usermember')->where("id=".$order['uid'])->find();
        if(empty($user[hz_time])){
            M('usermember')->where("id=".$order['uid'])->save(array('hz_time'=>time()));
        }
        M('usermember')->where("id=".$order['uid'])->save(array('reward'=>$user[reward]+$order[pay_price]));

    }

    public function checkpwd(){
        $post = $this->param;
        if(!$post['pwd']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息pwd'));
        }
        if($this->type==1){
            $m=M('usermember');
        }else{
            $m=M('staff');
        }
        $user=$m->find($this->uid);

        if($user[zfpwd]==$post[pwd]){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'密码错误','data'=>$result));
        }


    }
    public function zcsure(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(M('zcsure')->where('tid='.$post[id].' and uid='.$this->uid)->find()){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'已确认！'));
        }
      
        $add[tid] = $post[id];
        $add[uid] = $this->uid;
        $add[content] = $post[content];
        $add[create_time] = time();

        $result=M('zcsure')->add($add);

      
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'确认成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function familyapp(){
        $post = $this->param;

        $bbb=$post[content];
        $a=explode('&',$bbb);

        $count=0;
        $need=array();
        foreach($a as $k=>$v){
            $c=str_replace("%5B%5D",'',$v);

            $n=explode('=',$c)[1];


            if($k%4==0){
                $arr=array();
                array_push($arr,$n);
            }else{
                array_push($arr,$n);
                if($k%4==3){
                    array_push($need,$arr);
                }

            }

        }
        //$this->appReturn(array('msg'=>'发布失败need！','code'=>201,'data'=>$need));
        if(!empty($need)){
            $num=0;
            foreach($need as $k=>$v){
                if(!$v[0] || !$v[1]  || !$v[2]  || !$v[3]){
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请完善家人信息','data'=>$need));
                }
                if(M('family')->where('uid='.$this->uid.' and name='.$v[0])->find()){
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请勿重复加入家人','data'=>123));
                }
                $add[orderno]=orderNumber();
                $add[uid]=$this->uid;
                $add[name]=$v[0];
                $add[gx]=$v[3];
                $add[iccard]=$v[1];
                $add[money]=$v[2];
                $add[create_time]=time();
                M('family')->add($add);
                $num++;
            }
            $this->appReturn(array('status'=>false,'code'=>200,'msg'=>'','data'=>$num,'post'=>$post));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请选择要加入的家人','data'=>$arr));
        }
    }
    public function family(){
        $post = $this->param;

        //var_dump($post);exit();
        $arr=[];
        foreach($post as $k=>$v){
            foreach($v as $kk=>$vv){

                $arr[$kk][$k] = $vv;

            }
        }
        //$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请选择要加入的家人1','data'=>$arr));
        if(!empty($arr)){
            $num=0;$order_number = orderNumber();
            foreach($arr as $k=>$v){
                if(!$v[name] || !$v[gx]  || !$v[iccard]  || !$v[money]){
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请完善家人信息','data'=>123,'list'=>$arr));
                }
                $map = array();
				$map['uid'] = array('eq',$this->uid);
				$map['name'] = array('eq',$v['name']);
				
                if(M('family')->where($map)->find()){
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请勿重复加入家人','data'=>123));
                }
                $add[orderno]=$order_number;
                $add[uid]=$this->uid;
                $add[name]=$v[name];
                $add[gx]=$v[gx];
                $add[iccard]=$v[iccard];
                $add[money]=$v[money];
                $add[create_time]=time();
                M('family')->add($add);
                $num++;
            }
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'','data'=>$num,'order_number'=>$order_number));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请选择要加入的家人','data'=>123));
        }


    }
    public function peoplenum(){
        $post = $this->param;
        $result = M('family')->where("uid=".$this->uid)->count();

        $this->appReturn(array('status'=>false,'code'=>200,'msg'=>'','data'=>$result));
    }
    public function infocheck(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }

        $re = M('infocheck')->where("uid=".$this->uid."  and tid=".$post[id])->find();
        if($re){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'已经确认该条信息','data'=>$result));
        }else{
            M('infocheck')->add($add);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    //会员免费
    public function freeorder(){
            $post=$this->param;

            if(!empty($post[orderno])){
                $orderno=$post[orderno];
            }else{
                $orderno=orderNumber();
            }

            if(0){

            }else{

                if(!empty($post['id'])){
                    $order['tid'] = $post['id'];
                }
                $order['orderno'] = $orderno;
                $order['type'] = $post[type];
                $order['uid'] = $this->uid;
                $order['utype'] = $this->type;
                if(!empty($post['sid'])){
                    $order['sid'] = $post['sid'];
                }
                $order['status'] = 2;
                $order['pay_price'] = 0;
                $order['pay_type'] = 0;
                $order['create_time'] = time();
                $order['date_time'] = date('Y-m');

                $re=M('order')->add($order);

                if($re){
                    $order=M('order')->find($re);
                    $hetong=M('hetong')->find($post[id]);
                    if($post['type']==14){
                        $add['orderno'] = $order[orderno];
                        $add['uid'] = $this->uid;
                        $add['type'] = $hetong['type'];
                        $add['tid'] = $post['id'];
                        $add['money'] = 0;
                        $add['create_time'] = time();
                        $add['date_time'] = date('Y-m');
                        $re=M('hetonguser')->add($add);
                    }else if($post['type']==2 || $post['type']==3){
                        $add['order_id'] = $re;
                        $add['orderno'] = $orderno;
                        $add['uid'] = $this->uid;
                        $add['sid'] = $post['sid'];
                        $add['type'] = $post['type']-1;
                        $add['money'] = $order['pay_price'];
                        $add['create_time'] = time();
                        $re=M('ask')->add($add);
                    }
                    $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$orderno,'data'=>123));
                }else{
                    $this->appReturn(array('status'=>true,'code'=>201,'msg'=>'生成订单失败','data'=>123));
                }
            }

    }
    //下订单
    public function order(){
        $post = $this->param;

        if(!empty($post[orderno])){
            $orderno=$post[orderno];
        }else{
            $orderno=orderNumber();
        }

            if(!empty($post['id'])){
                $order['tid'] = $post['id'];
            }
            $order['orderno'] = $orderno;
            $order['type'] = $post[type];
            $order['uid'] = $this->uid;
            $order['utype'] = $this->type;
            if(!empty($post['sid'])){
                $order['sid'] = $post['sid'];
            }
            if($post[type]==1){
                $order['pay_price'] = C('WEB_LTATION_SITE');
            }else{
                $order['pay_price'] = $post[money];
            }
            if($post[type]==2 || $post[type]==3 || $post[type]==4 || $post[type]==5 || $post[type]==6 || $post[type]==7 || $post[type]==8){
                $bili=bili($post[type]);
                $s_price=$post[money](100-$bili)/100;
                $order['s_price'] = $s_price;
            }else{
				
				$order['s_price'] = $post[money];
			}
            $order['pay_type'] = $post['pay_type'];
            $order['create_time'] = time();
            $order['date_time'] = date('Y-m');

            $re=M('order')->add($order);

            if($re){
                $order=M('order')->find($re);
                if($post['type']==15){
                    $add['order_id'] = $re;
                    $add['orderno'] = $orderno;
                    $add['uid'] = $this->uid;
                    $add['utype'] = $this->type;
                    $add['tid'] = $post['id'];
                    $add['money'] = $post['money'];
                    $add['create_time'] = time();
                    $add['date_time'] = date('Y-m');
                    $re=M('zcjoin')->add($add);
                }else if($post['type']==2 || $post['type']==3){
                    $add['order_id'] = $re;
                    $add['orderno'] = $orderno;
                    $add['uid'] = $this->uid;
                    $add['sid'] = $post['sid'];
                    $add['type'] = $post['type']-1;
                    $add['money'] = $order['pay_price'];
                    $add['create_time'] = time();
                    $re=M('ask')->add($add);
                }else if($post['type']==14){
                    //$add['order_id'] = $re;
                    $add['orderno'] = $orderno;
                    $add['uid'] = $this->uid;
                    $add['sid'] = $post['sid'];
                    $add['type'] = $post['type'];
                    $add['money'] = $order['pay_price'];
                    $add['create_time'] = time();
                    $re=M('hetonguser')->add($add);
                }
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$orderno,'data'=>123));

            }else{
                $this->appReturn(array('status'=>true,'code'=>201,'msg'=>'生成订单失败','data'=>123));
            }

    }
    public function sindex(){
        $post = $this->param;

        $result[tuwen]= M('ask')->where("sid=".$this->uid." and payed=1 and type=1 and status=1 and state=1")->count();
        $result[tel]=   M('ask')->where("sid=".$this->uid." and payed=1 and type=2 and status=1 and state=1")->count();
        $result[face]=  M('ask')->where("sid=".$this->uid." and payed=1 and type=3 and status=1 and state=1")->count();
        $result[book]=  M('ask')->where("sid=".$this->uid." and payed=1 and type=4 and status=1 and state=1")->count();
        $result[lawyer]=M('ask')->where("sid=".$this->uid." and payed=1 and type=5 and status=1 and state=1")->count();

        $ids=implode(',',array_column(M("wt")->where("status=2")->select(),'id'));
        $b['tid']=array('in',$ids);
        $b['uid']=array('eq',$this->uid);
        $b['choose']=array('eq',1);
        $result[wt]= M('wtjoin')->where($b)->count();

		
		$map = array();
		$a=strtotime('this week Monday',time());
		$map['sid']=array('eq',$this->uid);
		$map['type'] = array('in','2,3,4,5,6,7,8,10');
		$map['status']=array('in','1,2,3');
		$map['is_transfer'] = array('eq',1);
		$map['create_time']=array('gt',$a);
		
		$xy_num = M('order')->where($map)->sum('s_price');
		
		if(!empty($xy_num)){
			
			$result[week] = $xy_num;
		}else{
            $result[week]=0;
        }
		
		
		
		$sy['sid']=array('eq',$this->uid);
        $sy['status']=array('in','1,2,3');
        $sy['type']=array('in','2,3,4,5,6,7,8,10');
        $sy['is_transfer']=array('eq',1);
		
		

		
		$total_count = M('order')->where($sy)->count();

        if($total_count){
            $result[total]=M('order')->where($sy)->sum('s_price');
        }else{
            $result[total]=0;

        }
		
		
		
		
		
		$result[wdz] = D('Home/Staff')->wdz_price($this->uid);
        
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result,'sql'=>$sql,'wdz'=>$wdz));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }

    public function logout(){
        $post = $this->param;
        if($this->type==1){
            $result = M("usermember")->save(array('id'=>$this->uid,'token'=>''));
        }else{
            $result = M("staff")->save(array('id'=>$this->uid,'token'=>''));
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function buyvip(){
        $post = $this->param;
        if(!$post['vip']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }

        $result = M('ask')->where("id=".$this->uid)->save(array('vip'=>$post[vip]));
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    //读取用户个人信息
    public function user(){  
        $post = $this->param;
        if($this->type==1){
            $result = M('usermember')->find($this->uid);

            if(strpos($result[icon],'http://') !== false){

            }else{
                $result[icon]=picture($result[icon]);
            }

            if($result[vip]==0){
                $a=0;
            }else{
                $a=1;
            }
            if($result['hz_time']==0){
                $result['hz_time']=0;
            }else{
                $result['hz_time']=ceil( (time()-$result['hz_time'])/(24*3600) );
            }

        }else{
            $result = M('staff')->where('id='.$this->uid)->find();
			$sql = M('staff')->getLastSql();
            //$result[tic]=M('order')->where(array('uid'=>$this->uid,'type'=>2))->sum('tic');
            $a=2;

            $result[iconid]=$result[icon];
            if(strpos($result[icon],'http://') !== false){

            }else{
                $result[icon]=picture($result[icon]);
            }
            $result[n1id]=$result[ipzm];
            $result[ipzm]=picture($result[ipzm]);
            $result[n2id]=$result[ipfm];
            $result[ipfm]=picture($result[ipfm]);
            $result[n3id]=$result[iczm];
            $result[iczm]=picture($result[iczm]);
            $result[n4id]=$result[icfm];
            $result[icfm]=picture($result[icfm]);
            if($result[label] != ''){
                if(strpos($result[label],',')){
                    $c=explode(',',$result[label]);
                }else{
                    $c=array($result[label]);
                }
                $str='';
                foreach($c as $sk=>$sv){
                    $name= M('ajcate')->find($sv)[name];
                    $str.=' '.$name;
                }
                $result[labelstr]=$str;
            }else{
                $result[labelstr]='';
            }
        }
        $result['user_time']=date('Y-m-d', $result['user_time']);

        if($result){
			
			$user_tx_price = C('WEB_MEMBER_TIXIAN');$staff_tx_price = C('WEB_STAFF_TIXIAN');
			
            //$result[rednum]=M('order')->where(array('uid'=>$this->uid,'type'=>$a,'status'=>1))->count();
            $this->appReturn(array('status'=>true,'msg'=>'成功','code'=>200,'data'=>$result,'sql'=>$sql,'user_tx_price'=>$user_tx_price,'staff_tx_price'=>$staff_tx_price));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'失败','code'=>201,'data'=>$result));
        }

    }
    //修改个人信息
    public function edit_user(){
        $post = $this->param;
        //unset($post[token]);
        foreach($post as $k=>$v){
            if($v=='' || $v==null){
                unset($post[$k]);
            }
        }
        if($this->type==1){
            $m = M('usermember');
        }else{
            $m = M('staff');
        }
        $result=$m->where(array('id'=>$this->uid))->save($post);
        if($result){
            $this->appReturn(array('status'=>true,'msg'=>'保存成功','code'=>200,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'保存成功','code'=>200,'data'=>$result));
        }

    }
    //修改登录密码
    public function edit_pwd(){
        $post = $this->param;
        if( !$post['old_pwd'] || !$post['new_pwd'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($this->type==1){
            if($post['old_pwd'] != M('usermember')->where(array('id'=>$this->uid))->find()[password]){
                $this->appReturn(array('status'=>false,'msg'=>'原密码错误','code'=>201,'data'=>$data));
            }
            $result = M('usermember')->where(array('id'=>$this->uid))->save(array('password'=>$post['new_pwd']));

        }else{
            if($post['old_pwd'] != M('staff')->where(array('id'=>$this->uid))->find()[password]){
                $this->appReturn(array('status'=>false,'msg'=>'原密码错误','code'=>201,'data'=>$data));
            }
            $result = M('staff')->where(array('id'=>$this->uid))->save(array('password'=>$post['new_pwd']));
        }
        if($result){
            $this->appReturn(array('status'=>true,'msg'=>'重置密码成功','code'=>200,'data'=>$data));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data));
        }
    }

    public function search(){
        $post = $this->param;
        if(!$post['key'] || !$post['actiontype'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(!M('history')->where("uid=".$this->uid." and key='".$post['key']."' and type=".$post['type'])->find()){
            $add[uid]=$this->uid;
            $add[utype]=$this->type;
            $add[key]=$post['key'];
            $add[type]=$post['type'];
            M('history')->add($add);
        }

        if($post['actiontype']==1){
            $start=$post[page]?($post[page]-1)*10 : 0;
            $ww["name"]=array('like','%'.$post['key'].'%');

            $result = M('hetong')->where($ww)->limit($start,10)->select();
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $start=$post[page]?($post[page]-1)*4 : 0;
            $ww["name"]=array('like','%'.$post['key'].'%');
            $id = M('ajcate')->where($ww)->find()[id];
            if($id){
                //$where['_string'] =  'FIND_IN_SET('.$id.',label)';
                //$result = M('staff')->where($where)->limit($start,4)->select();
                $result = M('staff')->where("FIND_IN_SET($id,label)")->select();
                foreach($result as $k=>$v){
                    $staff=sinfo($v['id']);
                    $result[$k][name]= $staff[nickname];
                    $result[$k][icon]= $staff[icon];
                    $result[$k][label]= $staff[label];
					$result[$k]['year'] = $v['work_year'];

                }
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M('staff')->getLastSql(),'data'=>$result));
            }else{
                $wh["nickname"]=array('like','%'.$post['key'].'%');
                $result = M('staff')->where($wh)->select();
                if($result){
                    foreach($result as $k=>$v){
                        $staff=sinfo($v['id']);
                        $result[$k][name]= $staff[nickname];
                        $result[$k][icon]= $staff[icon];
                        $result[$k][label]= $staff[label];
						$result[$k]['year'] = $v['work_year'];

                    }
                    $this->appReturn(array('status'=>false,'code'=>200,'msg'=>M('staff')->getLastSql(),'data'=>$result));
                }else{
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('staff')->getLastSql(),'data'=>$result));
                }


            }

        }

    }
    public function clearsearch(){
        $post = $this->param;
        if(!$post['type'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('history')->where("uid=".$this->uid." and type=".$post['type'])->delete();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'清除成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'','data'=>$result));
        }
    }
    public function searchhistory(){
        $post = $this->param;
        if(!$post['type'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('history')->where("uid=".$this->uid." and type=".$post['type'])->select();

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'','data'=>$result));
        }
    }
    public function caiwu(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*6 : 0;

        if($post['date'] != ''){
             $a=trim(str_replace(['年','月','日'],'-',$post['date']),'-');
             $stime=strtotime($a.' 00:00:00');
             $etime=strtotime($a.' 23:59:59');
             $ddd['create_time']=array(array('gt',$stime),array('lt',$etime));
        }

        $where['uid']=$this->uid;
        $where['isuserdel']=array('eq',0);
        if($this->type==1){
            $vip=M("usermember")->where("id=".$this->uid)->find()[vip];

            if($vip==0){
                $where['type']=array('eq',0);
            }else{
                $where['type']=array('eq',1);
            }
        }else{
            $where['type']=array('eq',2);
        }

        //$where[state]=1;
        if($post['date'] != ''){
            $result=M("order")->where($where)->where($ddd)->order("id desc")->limit($start,6)->select();
        }else{
            $result=M("order")->where($where)->order("id desc")->limit($start,6)->select();
        }
        $sql=M("order")->getLastSql();

        foreach($result as $k=>$v){
            $goods=M("goods")->where("id=".$v[goodsid])->find();
            $result[$k][name]=$goods[name];
            $result[$k][icon]=picture($goods[icon]);
            $result[$k][cate]=M("cate")->where("id=".$v[cid])->find()[name];
            $result[$k][time]=date('m-d H:i',$v[create_time]);

        }
        $total=M("order")->where($where)->sum('money');
        $aaa=strtotime(date('Y-m'));
        $month=M("order")->where($where)->where("create_time>".$aaa)->sum('money');

        $result[data]=$result;
        $result[total]=$total;
        $result[month]=$month;
        if($this->type==2){
            $map[uid]=array('eq',$this->uid);
            $map[type]=array('eq',$this->type);
            $result[tic]=M("order")->where($map)->sum('tic');
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
        }

    }
    public function overorder(){
        $post = $this->param;
        if(!$post['id'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($re=M("order")->where("uid=".$this->uid." and id=".$post['id'])->find()){
            $result=M("order")->where("id=".$post['id'])->save(array('status'=>5));

            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'非法订单','data'=>$result));
        }


    }
    public function delorder(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($re=M("order")->where("uid=".$this->uid." and id=".$post['id'])->find()){
            $result=M("order")->where("id=".$post['id'])->save(array('isuserdel'=>1));
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'删除成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'删除失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'非法订单','data'=>$result));
        }


    }
    public function delaskorder(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($re=M("ask")->where("id=".$post['id'])->find()){
            $result=M("order")->where("id=".$post['id'])->save(array('isuserdel'=>1));

            $user=M("usermember")->where("id=".$re['uid'])->find();
            M("usermember")->where("id=".$re['uid'])->save(array('balance'=>$user[balance]+$re[money]));

            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'删除成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'删除失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'非法订单','data'=>$result));
        }

    }
    public function qxorder1(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(M("order")->where("id=".$post['id'])->delete()){
             $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'删除成功','data'=>$result));
        }else{
             $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'删除失败','data'=>$result));
        }
    }
    public function backorder(){
        $post = $this->param;
        if(!$post['id'] || !$post['reason'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($re=M("order")->where("uid=".$this->uid." and id=".$post['id'])->find()){
            $save[state]=1;
            $save[reason]=$post['reason'];
            $save[back_time]=time();
            if(!empty($post['icon'])){
                $save[icon]=$post['icon'];
            }
            $result=M("order")->where("id=".$post['id'])->save($save);
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'申请退单成功，等待处理中。。。','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'非法订单','data'=>$result));
        }

    }

    public function mycart(){
        $post = $this->param;
        /*if(!$post['id'] ||!$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }*/
        if($post['id']>0){
            $c=M('mycart')->where("id=".$post['id'])->find();
            if($post['type']==1){
                if($c[num]==1){
                    M('mycart')->where("id=".$post['id'])->delete();
                }else{
                    M('mycart')->where("id=".$post['id'])->save(array('num'=>$c[num]-1));
                }
            }else if($post['type']==2){
                M('mycart')->where("id=".$post['id'])->save(array('num'=>$c[num]+1));
            }
        }

        $result = M('mycart')->where("uid=".$this->uid." and type=".$this->type)->select();

        $total=0;
        foreach($result as $k=>$v){
            $goods=M("goods")->where("id=".$v[goodsid])->find();
            if($this->type==1){
                if(M('usermember')->find($this->uid)[vip]==0){
                    $price=$goods[price];
                }else{
                    $price=$goods[vipprice];
                }
            }else{
                    $price=$goods[vipprice];
            }

            $money=$price*$v[num];
            $total+= $money;

            $result[$k][money]=$money;
            $result[$k][name]=$goods[name];
            $result[$k][icon]=picture($goods[icon]);
            $result[$k][color]=M("color")->where("id=".$v[color])->find()[name];

        }
        $a[data]=$result;
        $a[total]=$total;
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$a));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function cartorder(){
        $post = $this->param;
        $ids=$post['ids'];

        if($this->type==2){
            $user=M('staff')->find($this->uid);
        }else{
            $user=M('usermember')->find($this->uid);
        }

        $w[uid]=array('eq',$this->uid);
        $w[type]=array('eq',$this->type);
        $w[id]=array('in',$ids);
        $result = M('mycart')->where($w)->select();
        foreach($result as $k=>$v){
            $orderno=str_shuffle(time());

            $orderadd['orderno']=$orderno;
            $orderadd['goodsid']=$v[goodsid];
            $orderadd['num']=$v[num];
            $orderadd['color']=$v[color];

            $orderadd['uid']=$this->uid;
            if($post[jid]>0){
                $orderadd['jid']=$post[jid];
            }
            $orderadd['nickname']=$user[nickname];
            $re=M('goods')->where("id=".$v[goodsid])->find();
            if($this->type==1){
                if(M('usermember')->find($this->uid)[vip]==0){
                    $orderadd['type']=0;
                    $price=$re[price];
                }else{
                    $orderadd['type']=$this->type;
                    $price=$re[vipprice];
                }
            }else{
                $orderadd['type']=$this->type;
                $price=$re[vipprice];
            }
            $money=$price*$v[num];
            $orderadd['price']=$price;
            $orderadd['money']=$money;
            $orderadd['addrid']=$post['addrid'];
            $orderadd['create_time']=time();
            if($this->type==2){
                $bili=M('staff')->find($this->uid)[bili];
                $tic=$bili*$money/100;
                $orderadd['tic']=$tic;
            }
            $res=M('order')->add($orderadd);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function chat(){
        $post = $this->param;

        if($post[type]==1){
            $post[uid]=$this->uid;
            $post[type]=$this->type;
            $post[create_time]=time();

            $result = M("chat")->add($post);
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }

        }else if($post[type]==2){
            $result = M("chat")->where("id=".$post[id])->find();
            if($result[state]==2 && $result[status]==1){
                M("chat")->where("id=".$result[id])->save(array('status'=>2));
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result[back]));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }
        }else{
            $result = M("chat")->where("uid=".$this->uid." and type=".$this->type)->select();
            /*foreach($result as $k=>$v){
                $result[$k][back]=M("back")->where("tid=".$v[id])->order('id asc')->select();
            }*/
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }

        }

    }
    public function history(){
        $post = $this->param;
        if( !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $where[uid]=$this->uid;
        $where[type]=$this->type;
        if($post['type']==1){
            $result=M("history")->where($where)->order("id desc")->select();
        }elseif($post['type']==2){
            $result=M("history")->where($where)->delete();
        }elseif($post['type']==3){
            $add[uid]=$this->uid;
            $add[type]=$this->type;
            $add[key]=$post['key'];
            $result=M("history")->add($add);
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("history")->getLastSql(),'data'=>$result));
        }
    }

    public function apply(){
        $post = $this->param;
        if(!$post['model']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($post['last_time']){
			
			if(time() > strtotime($post['last_time'])){
				$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'结束时间不能小于当前时间'));
			}
			
            $post['last_time']=strtotime($post['last_time']);
        }
        if($post['lasttime']){
			if(time() > strtotime($post['lasttime'])){
				$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'结束时间不能小于当前时间'));
			}
            $post['lasttime']=strtotime($post['lasttime']);
        }
        if($post['agreetime']){
			if(time() > strtotime($post['agreetime'])){
				
				$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'预约时间不能小于当前时间'));
			}
            $post['agreetime']=strtotime($post['agreetime']);
        }
		
		if($post['model'] == 'fask' && !empty($post['content'])){
			$map = array();
			$map['uid'] = $this->uid;
			$map['content'] = array('eq',$post['content']);
			$map['_string'] = '(private=0 and reward_price=0) or ((private=1 and reward_price=0) and is_pay=1))';
			$askInfo = M('fask')->where($map)->find();
			
			if(!empty($askInfo)){
				$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'您已发布过当前咨询！'));
			}
		}
        $w[uid]=array('eq',$this->uid);
        $w[utype]=array('eq',$this->type);
        $w[create_time]=array('gt',time()-60);
        if($post['model']=='zhishi'){
            $w[type]=array('eq',$post[type]);
        }
        $m=M($post['model']);
        if($m->where($w)->find()){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'一分钟内请勿重复提交'));
        }
		
		if($post['model'] == 'tixian'){
			$tx_check  = D('Apply')->tx_check($post,$this->me);
			if($tx_check['status'] == false){
				$this->appReturn($tx_check);
			}
			$post = $tx_check['post'];
		}
		
		if(!empty($post['agreetime'])){
			
			if($post['agreetime'] < time()){
				$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'预约时间不能小于当前时间'));
			}
			
		}

        $orderno=orderNumber();
        if($post['model']=='ask' || $post['model']=='fask'|| $post['model']=='zc' || $post['model']=='wt' || $post['model']=='fenxi' || $post['model']=='legalpaper' || $post['model']=='zhishi'){
            $post[orderno]=$orderno;
        }

        $post[uid]=$this->uid;
        $post[utype]=$this->type;
        $post[create_time]=time();

        $re=$m->add($post);
        if($re){
			$userInfo = $this->me;
            $d[orderno]=$orderno;
            $d[money]=$post[money];

            if($post['model']=='tixian'){
				
				D('Apply')->tixian($post,$this->me,$re,$this->type);
				
			}else if($post['model']=='fenxi' && $userInfo['vip'] == 1){
				
				$order['orderno'] = $orderno;
                $order['type'] = 18;
                $order['uid'] = $this->uid;
                $order['utype'] = $this->type;
                if(!empty($post['sid'])){
                    $order['sid'] = $post['sid'];
                }

                $order['pay_price'] = 0;
                $order['pay_type'] = 0;
                $order['create_time'] = time();
                $order['date_time'] = date('Y-m');
				$order['status'] = 1;
                $re=M('order')->add($order);
				
			}

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功, 等待处理中。。。','data'=>$d,'orderno'=>$orderno,'id'=>$re ));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'提交失败','data'=>$re));
        }

    }
    public function advice(){
        $post = $this->param;
        if(!$post['phone'] || !$post['content']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $w[uid]=array('eq',$this->uid);
        $w[utype]=array('eq',$this->type);
        $w[create_time]=array('gt',time()-60);
        if(M('advice')->where($w)->find()){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请勿重复提交'));
        }
        if(isset($post['icon'])){
            $add[icon]=$post['icon'];
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[content]=$post['content'];
        $add[phone]=$post['phone'];
        $add[create_time]=time();
        $data=M('advice')->add($add);
        if($data){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'提交失败','data'=>$data));
        }

    }
    public function adddongtai(){
        $post = $this->param;
        if(!$post['content']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $w[uid]=array('eq',$this->uid);
        $w[utype]=array('eq',$this->type);
        $w[create_time]=array('gt',time()-10);
        if(M('dongtai')->where($w)->find()){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'请勿重复提交'));
        }
        if(isset($post['icon'])){
            $add[icon]=$post['icon'];
        }
        if(!empty($post['id'])){
            $add[pid]=$post['id'];
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[content]=$post['content'];
        $add[create_time]=time();
        $data=M('dongtai')->add($add);
        if($data){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'提交失败','data'=>$data));
        }

    }
    public function addtuwen(){
        $post = $this->param;

        if($re=M('tuwen')->where('tid='.$post['id'])->find()){
            if($this->type==1){
                $a=trim($re[uicon].','.$post['icon'],',');
                $data=M('tuwen')->where('tid='.$post['id'])->save(array('uicon'=>$a));
            }else{
                $a=trim($re[icon].','.$post['icon'],',');
                $data=M('tuwen')->where('tid='.$post['id'])->save(array('icon'=>$a,'know'=>0));
            }

        }else{
            $add['orderno']= $post['orderno'];
            $add['tid']= $post['id'];
            $add[uid]=$this->uid;
            $add[type]=$this->type;
            if($this->type==1){
                $add[uicon]=$post['icon'];
            }else{
                $add[icon]=$post['icon'];
            }


            $add[create_time]=time();
            $data=M('tuwen')->add($add);
        }

        if($data){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'上传成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'提交失败','data'=>$data));
        }

    }
    public function addcoupon(){
        $post = $this->param;
        if(!$post['type'] || !$post['money'] || !$post['num'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $add['sid']=$this->uid;
        $add['type']=$post['type'];
        $add['money']=$post['money'];
        $add[create_time]=time();
        for($i=0;$i<$post[num];$i++){
            M('coupon')->add($add);
        }
        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$data));
    }
    public function getcoupon1(){
        $post = $this->param;
        if(!$post['sid'] || !$post['type'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $id=M('coupon')->where("uid=0 and sid=".$post['sid']." and type=".$post[type])->order('id asc')->find()[id];
        if($id){
            $re=M('coupon')->where("id=".$id)->save(array('uid'=>$this->uid));
            if($re){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$re));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$re));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'优惠券已领完','data'=>$re));
        }


    }
    public function getcoupon(){
        $post = $this->param;
        if(!$post['sid'] || !$post['type'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
            if(M('coupon')->where("uid=".$this->uid." and sid=".$post['sid']." and type=".$post[type])->find()){
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('coupon')->getLastSql(),'data'=>$re));
            }else{
                $add[uid]=$this->uid;
                $add[type]=$post[type];
                $add['sid']=$post['sid'];

                $a=cptype($post['type']);

                $t=M('couponpt')->find($a);
                $b=strtotime(substr($t['last_time'],0,10));
                $add['last_time']=$b;

                $add['money']=$t[money];
                $re=M('coupon')->add($add);
                if($re){
                    $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$re));
                }else{
                    $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('coupon')->getLastSql(),'data'=>$re));
                }
            }




    }
    public function couponinfo(){
        $post = $this->param;
        /*$arr=array();
        $a=array(1,2,3,4,5);
        for($i=1;$i<=count($a);$i++){
            $result=M('coupon')->field('*,count(id) as num')->where("sid=".$this->uid." and uid=0"." and type=".$i)->group('type')->select();
            if($result){

                $array['num']=$result[0]['num'];
                $array['money']=$result[0]['money'];
            }else{

                $array['num']=0;
            }
            array_push($arr,$array);
            $array=array();
        }*/

        $result=M('couponpt')->order('id asc')->select();
        foreach ($result as $k=>$v) {
            $find=M('couponjoin')->where("type=".($k+1)." and sid=".$this->uid)->find();
            if($find){
                $result[$k][num]= 1;
            }else{
                $result[$k][num]= 0;
            }
           
        }
        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$result));
    }
    public function couponswitch(){
        $post = $this->param;
        
        if($post[action]==1){
            $add[type]=$post[type];
            $add['sid']=$this->uid;
            M('couponjoin')->add($add);
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $id=M('couponjoin')->where("type=".$post['type']." and sid=".$this->uid)->find()[id];
            if($id){
                M('couponjoin')->where("id=".$id)->delete();
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('couponjoin')->getLastSql(),'data'=>$result));
            }

        }

    }
    public function coupon1(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*5 : 0;
        $where['type']=array('eq',$post[type]);
        $result=M('coupon')->where($where)->group('sid')->order('id desc')->limit($start,5)->select();

        if($result){
            foreach ($result as $k=>$v) {
                $staff=sinfo($v['sid']);
                $result[$k][name]= $staff[nickname];
                $result[$k][icon]= $staff[icon];
                $result[$k][ordertype]= ordertype($v[type]);
                $result[$k][lasttime]= date('Y-m-d',$v[last_time]);
                if(time() > $v[last_time]){
                    $result[$k][guoqi]=1;
                }else{
                    $result[$k][guoqi]=0;
                }
                if(M('coupon')->where("uid=".$this->uid." and sid=".$v['sid']." and type=".$v['type'])->find()){
                    $result[$k][is_have]=1;
                }else{
                    $result[$k][is_have]=0;
                }

            }

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }




    }
    public function coupon(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*5 : 0;
        if($post['sid']>0){
            $where['sid']=array('eq',$post['sid']);
        }
        $where['type']=array('eq',$post[type]);
        $result=M('couponjoin')->where($where)->order('id desc')->limit($start,5)->select();

        if($result){
            foreach ($result as $k=>$v) {
                $staff=sinfo($v['sid']);
                $result[$k][name]= $staff[nickname];
                $result[$k][icon]= $staff[icon];
                $result[$k][ordertype]= ordertype($v[type]);
                $result[$k][money]= M('couponpt')->find(cptype($v[type]))[money];

                $b=M('couponpt')->find(cptype($v['type']))['last_time'];

                $result[$k][lasttime]= $b;
                $last_time=strtotime(substr($b,0,10));
                if(time() > $last_time){
                    $result[$k][guoqi]=1;
                }else{
                    $result[$k][guoqi]=0;
                }
                if(M('coupon')->where("uid=".$this->uid." and sid=".$v['sid']." and type=".$v['type'])->find()){
                    $result[$k][is_have]=1;
                }else{
                    $result[$k][is_have]=0;
                }

            }

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }




    }

    public function ucoupon(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*5 : 0;
        $where['uid']=array('eq',$this->uid);
        $result=M('coupon')->where($where)->order('id desc')->limit($start,5)->select();
        if($result){
            foreach ($result as $k=>$v) {
                $staff=sinfo($v['sid']);
                $result[$k][name]= $staff[nickname];
                $result[$k][icon]= $staff[icon];

                $result[$k][ordertype]= ordertype($v[type]);
                $result[$k][money]= M('couponpt')->find(cptype($v[type]))[money];


                if(time()>$v[last_time]){
                    $result[$k][guoqi]=1;
                }else{
                    $result[$k][guoqi]=0;
                }
                $result[$k][lasttime]= date('Y-m-d',$v[last_time]);
            }

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function uhetong(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4 : 0;
        if($post[type]>0){
            $where['h.type']=array('eq',$post[type]);
        }
        $where['h.uid']=array('eq',$this->uid);
		$where['o.utype'] = array('eq',$this->type);
		$where['o.status'] = array('eq',1);
        $result=M('hetonguser as h')->field('h.*')->join('bhy_order as o on o.orderno=h.orderno')->where($where)->order('h.id desc')->limit($start,4)->select();
		$sql = M('hetonguser as h')->getLastSql();
		file_put_contents('sql1.txt',$sql);
        if($result){
            foreach ($result as $k=>$v) {
                $hetong=M('hetong')->find($v[tid]);
                $result[$k][name]= $hetong[name];
                $result[$k][icon]= expic($hetong[icon])[0];
				
				
				
				
				
            }

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function addpl(){
        $post = $this->param;
        if(!$post['id'] || !$post['content'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		$askInfo = M('ask')->where('id='.$post[id])->find();
		
		if($askInfo['status'] == 5){
			
			$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'当前订单已评论'));
			
		}
		
        if(isset($post['icon'])){
            $add[icon]=$post['icon'];
        }
        if(isset($post['star'])){
            $add[star]=$post['star'];
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;

        $add[tid]=$post['id'];
        $add[type]=$post['type'];
        $add[content]=$post['content'];
        $add[create_time]=time();
        $id=M('pl')->add($add);
        if($id){
            $data=M('pl')->find($id);
            $me=$this->me();
            $data[icon]=$me[icon];
            $data[phone]=$me[phone];
            $data[time]=date('Y-m-d H:i',$data[create_time]);

            if(isset($post['star'])){
                M('ask')->where('id='.$post[id])->save(array('status'=>5));
            }
			
			//添加消息
			$message = array(
				'user_id' =>$askInfo['sid'],
				'user_type'=>2,
				'message'=>"您的订单有人评价了 ！",
				'add_time'=>time(),
				'type'=>9,  //用户评价订单
				'c_type1'=>1,  //咨询订单
				'return_id'=>$askInfo['id']
			);
			M('mymessage')->add($message);
			
			M('staff')->where('id='.$askInfo['sid'])->save(array('score'=>score($askInfo['sid'])));

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'发表成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>M('pl')->getLastSql()));
        }

    }
    public function addarticle(){
        $post = $this->param;
        if(!$post['name'] || !$post['content']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(isset($post['icon'])){
            $add[icon]=$post['icon'];
        }
        $add['sid']=$this->uid;
        $add[name]=$post['name'];
        $add[content]=$post['content'];
        $add[typeid]=$post['type'];
        $add[create_time]=time();
        $data=M('news')->add($add);
        if($data){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$data));
        }

    }
    public function addpub(){
        $post = $this->param;
        $post[uid]=$this->uid;
        $post[utype]=$this->type;
        //$add[name]=$post['name'];
        //$add[content]=$post['content'];
        $post[create_time]=time();
        $data=M('pub')->add($post);
        if($data){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'提交成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$data));
        }

    }

    public function uaskorder(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4 : 0;

        $where['uid']=array('eq',$this->uid);
        
        if( $post[status] >= 0 && $post[status] != null){
            if($post[status]==4){
                $where['status']=array('eq',$post[status]);
                $where['uover']=array('eq',1);

            }else if($post[status]==2){
                $where['status']=array('eq',$post[status]);
                $where['uover']=array('eq',0);
            }else if($post[status]==6){
                $where['_string'] = 'status=6 or after=2 or after=3';
            }else{
                $where['status']=array('in',$post[status]);
            }

        }

        if($post[status]>=1){
            $where['payed']=array('eq',1);
        }

        $result=M("ask")->where($where)->order("id desc")->select();
        //$result=M("ask")->where($where)->order("id desc")->limit($start,4)->select();
        foreach($result as $k=>$v){
            $staff=sinfo($v['sid']);
            $result[$k][name]= $staff[nickname];
            $result[$k][icon]= $staff[icon];
			$result[$k][staff_phone]= $staff[phone];

            $result[$k][ordertype]= ordertype($v['type']);
			$result[$k]['status_title'] = askStatus($v['status']);
            if($v['status'] == 5){
				
				$re=M("pl")->where("tid=".$v[id]." and type=3")->find();
                if($re){
                    $result[$k][star]= $re[star];
                    $result[$k][plcon]= $re[content];
					$result[$k][reply]= $re[reply];
                }else{
					
					$result[$k][star]= '';
                    $result[$k][plcon]= '';
					$result[$k][sql]= M("pl")->getLastSql();
					
				}
            }

        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("ask")->getLastSql(),'data'=>$result));
        }

    }
    public function qxask(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        M('ask')->where("id=".$post[id])->save(array('status'=>7));

        $result =M('ask')->where("id=".$post[id])->find();
        if($result){
			
			$map = array();
			$map['orderno'] = array('eq',$result['orderno']);
			$order = M('order')->where($map)->find();
			
            M("order")->where($map)->save(array('status'=>5));
			
			if($order['pay_type'] == 2){  //微信退款
					
				$result = D('Pay')->refund($order);

			}elseif($order['pay_type'] == 1){  //支付宝退款
				
				
				
			}elseif($order['pay_type'] == 3){  //余额退款
			
				$user=M("usermember")->where("id=".$result['uid'])->find();
				$water = array(
					'uid'=>$user['id'],
					'type'=>1,
					'user_type'=>1,
					'water_type'=>18,//取消订单退款
					'new_price'=> $result[money],
					'create_time'=>time(),
					'remark'=>'取消订单退款',
					'return_id'=>$order['id'],
					'user_money'=>$user[balance]+$result[money]
				);
				M("usermember")->where("id=".$result['uid'])->save(array('balance'=>$user[balance]+$result[money]));
				M('flowater')->add($water);
				
			}

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'取消成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('ask')->getLastSql(),'data'=>$result));
        }
    }
    public function qiyeorder(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4 : 0;

        $where['type']=array('eq',$post['type']);
        $where['uid']=array('eq',$this->uid);
        $where['status']=array('egt',1);

        $result=M("order")->where($where)->order("id desc")->limit($start,4)->select();
		$sql = M("order")->getLastSql();
        foreach($result as $k=>$v){
            $result[$k][money]= $v[pay_price];
            if($v[type]==16){
                $tid=M("legalpaper")->where("orderno=".$v[orderno])->find();
            }else{
                $tid=M("fenxi")->where("orderno=".$v[orderno])->find();
            }
            $result[$k][tid]= $tid['id'];
            $result[$k][order_status]= $tid['status'];
            $result[$k][state]= $tid['state'];

        }
        if($result){	
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result,'sql'=>$sql));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
        }

    }
    public function qxorder(){
        $post = $this->param;
        $m=M($post[model]);
		$map['orderno'] = array('eq',$post['orderno']);
		
		$info = $m->where($map)->find();
		
        $res1=$m->where($map)->save(array('status'=>7));
        $res2=M('order')->where($map)->save(array('status'=>-1,'update_time'=>time()));
		
		//添加消息
		$message = array(
			'user_id' =>$info['uid'],
			'user_type'=>1,
			'message'=>"您的订单已成功取消 ！！",
			'add_time'=>time(),
			'type'=>13,  //取消订单
			'c_type1'=>1,  //
			'return_id'=>$info['id']
		);
		
		M('mymessage')->add($message);
        
        if($res1){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'取消订单成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("ask")->getLastSql(),'data'=>$result));
        }

    }
    public function shouhou(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $ask=M('ask')->find($post['id']);

        $array=array('status'=>6,'reason'=>$post['content']);
        $result = M('ask')->where("id=".$post['id'])->save($array);

        if($order=M("order")->where("orderno=".$ask[orderno])->find()){
            M('order')->where("id=".$order['id'])->save(array('reason'=>$post['content'],'status'=>3));
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function chakan(){
        $post = $this->param;

        if (!$post['id'] || !$post['type']) {

            $this->appReturn(array('status' => false,'code'=>201, 'msg' => '未完善信息'));
        }

        if($post[type]==1){
            $m=M('ask');
        }else if($post[type]==2){
            $m=M('wt');
        }
        $a=$m->find($post[id]);
        if($post[type]==2){
            $a[price]=$a[minprice].'-'.$a[maxprice];
            $a[xq]='普通代理';
            $a[aj]=M('ajcate')->find($a[ajid])[name];
            if($a[type]==0){
                $b='普通委托';
            }else if($a[type]==1){
                $b='互助委托';
            } if($a[type]==2){
                $b='众筹委托';
            }
            $a[otype]=$b;
			
			if(empty($a[mobile])){
				
				$a[phone] = M('usermember')->find($a[uid])[phone];
				
			}else{
				
				$a[phone] = $a[mobile];
				
			}
            
            $choose=M('wtjoin')->where("tid=".$post[id]." and uid=".$this->uid." and choose=1")->find()?1:0;
            if($choose==1 && $a['twopay'] == 0){
                $c='已中标';
            }elseif($choose==1 && $a['twopay'] == 1 && $a['is_confirm'] == 0){
				
				$c='已支付律师费';
				
			}elseif($choose==1 && $a['twopay'] == 1 && $a['is_confirm'] == 1){
				
				$c='已确认完成';
				
			}else{
                $c='未中标';
            }
			$a['choose'] = $choose;
            $a[ostate]=$c;
        }else{
           
			
			$a['otype'] = ordertype($a[type]);
			if($a['type'] == 3){
				$a[aj]=M('ajcate')->find($a[ajid])[name];
				
			}

            $a[ostate]=askStatus($a['status']);
            $a[agreetime]=date('Y-m-d H:i',$a[agreetime]);

        }
        $a[time]=date('Y-m-d H:i',$a[create_time]);

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$a));

    }
    public function saskorder(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*4 : 0;

        $where['sid']=array('eq',$this->uid);
        $where['payed']=array('eq',1);
        if($post[type]>0){
            $where['type']=array('eq',$post[type]);
        }
        if($post[state]>0){
            $where['status']=array('eq',$post[state]);
			if($post[state] == 6){
				$where['after']=array('eq',1);
			}
        }
        
        

        if(!empty($post[sort])){
            $order='id asc';
        }else{
            $order='id desc';
        }

        $result=M("ask")->where($where)->order($order)->limit($start,4)->select();
        foreach($result as $k=>$v){
            $user=uinfo($v[uid]);
            $result[$k][name]= $user[nickname];
            $result[$k][icon]= $user[icon];

            $user=M("usermember")->find($v[uid]);
            if($v[type]==1 || $v[type]==2){
                $result[$k][phone]= empty($v['phone'])?$user[phone]:$v['phone'];
            }


            $result[$k][ordertype]= ordertype($v[type]);
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }

    }
    
    public function swdz(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*10 : 0;
        $where['sid']=array('eq',$this->uid);
        $where['is_transfer']=array('eq',0);
        $where['is_confirm']=array('eq',1);
        //$where['type']=array('in','2,3,4,5,6,7');
        $where['status']=array('eq',1);
        if(!empty($post[sort])){
            $order='id asc';
        }else{
            $order='id desc';
        }

        $result=M("order")->where($where)->order($order)->limit($start,10)->select();
        foreach($result as $k=>$v){
            $bili=bili($v[type]);

            $user=uinfo($v[uid]);
            $result[$k][name]= $user[phone];
            $result[$k][icon]= $user[icon];
            $result[$k][money]= $v[pay_price]*(100-$bili)/100;
            $result[$k][ordertype]= ordertype($v[type]-1);
        }

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }

    }
    public function askorderstatus(){
        $post = $this->param;
        if(!$post['id'] || !$post['status']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $ask= M('ask')->find($post[id]);
        $ordInfo= M('order')->where("orderno=".$ask['orderno'])->find();
		$money = $ordInfo['pay_price'];
        $user=M('usermember')->find($ask['uid']);
        $staff=M('staff')->find($ask['sid']);

        if($post['status']==2 ){
            $array=array('status'=>$post['status'],'state'=>2,'is_confirm'=>1);
			//添加消息
			$message = array(
				'user_id' =>$ask['uid'],
				'user_type'=>1,
				'message'=>"您发布的咨询律师接单了 ！！",
				'add_time'=>time(),
				'type'=>2,  //律师接单
				'c_type1'=>1,  //咨询订单
				'return_id'=>$ordInfo['id']
			);
			
			
			
			$res1 = M('order')->where('id='.$ordInfo['id'])->save(array('is_confirm'=>1,'update_time'=>time()));
				
			
        }else if($post['status']==3){
			//添加消息
			$message = array(
				'user_id' =>$ask['uid'],
				'user_type'=>1,
				'message'=>"您发布的咨询律师拒单了 ！！",
				'add_time'=>time(),
				'type'=>8,  //律师拒单
				'c_type1'=>1,  //咨询订单
				'return_id'=>$ordInfo['id']
			);

			$array=array('status'=>9,'state'=>5);
			
			if($ordInfo['pay_type'] == 2){  //微信退款
					
				$result = D('Pay')->refund($ordInfo);

			}elseif($ordInfo['pay_type'] == 1){  //支付宝退款
				
				
				
			}elseif($ordInfo['pay_type'] == 3){  //余额退款
			
				$water = array(
					'uid'=>$ask['uid'],
					'type'=>1,
					'user_type'=>1,
					'water_type'=>7,//咨询订单拒单退款
					'new_price'=> $money,
					'create_time'=>time(),
					'remark'=>'咨询订单拒单退款',
					'return_id'=>$ordInfo['id'],
					'user_money'=>$user['balance']+$money
				);
				
				//$user=M("usermember")->where("id=".$ask['uid'])->find();
				M("usermember")->where("id=".$ask['uid'])->save(array('balance'=>$user[balance]+$result[money]));
				M('flowater')->add($water);
			}
			
			
            $array=array('status'=>9,'state'=>5);
            M('usermember')->where("id=".$ask['uid'])->save(array('balance'=>$user[balance]+$money));
			M('mymessage')->add($message);
			
			 

        }else if($post['status']==4){
			
			if($this->type == 2){
				
				$array=array('sover'=>1,'is_confirm'=>1);
				$res1 = M('order')->where('id='.$ordInfo['id'])->save(array('is_confirm'=>1,'update_time'=>time()));
			}else{
				$array=array('status'=>$post['status'],'state'=>3);
			}         
        }
        $result = M('ask')->where("id=".$post['id'])->save($array);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result,'sql'=>M('ask')->getLastSql()));
        }
    }
    public function fask(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*4: 0;

        $result = M('fask')->where("uid=".$this->uid)->order('id desc')->limit($start,4)->select();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function fanswer(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4: 0;

        $result = M('fanswer')->where("uid=".$this->uid)->order('id desc')->limit($start,4)->select();
        foreach ($result as $k=>$v){
            $staff=sinfo($v['sid']);
            $result[$k][icon]= $staff[icon];
            $result[$k][time]= date('Y-m-d H:i',$v[create_time]);

            $ask=M('fask')->find($v[tid])[ajid];
            $result[$k][ask]= $ask[content];
            $result[$k][ajtype]= M('ajcate')->where("id=".$ask[ajid])->find()[name];


        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function answer(){
        $post = $this->param;
		
		$fask = M('fask')->find($post['id']);

        $add[tid]=$post[id];
        $add[content]=$post[content];
        $add['sid']=$this->uid;
        $add[create_time]=time();
        $result = M('fanswer')->add($add);
		
		//添加消息
		$ad = array(
            'user_id' =>$fask['uid'],
			'user_type'=>1,
            'message'=>"您发布的咨询已经有律师解答了，快去看看吧",
            'add_time'=>time(),
            'type'=>1,  //回复咨询
            'c_type1'=>1,  //下单
            'return_id'=>$post[id]
        );
		
		M('fask')->where('id='.$post['id'])->save(array('update_time'=>time()));

        $res1 = M("mymessage")->add($ad);
		
        if($result && $res1){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'回答成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'回答失败','data'=>$result));
        }
    }
    public function joinwt(){
        $post = $this->param;
        if(!$post['id'] || !$post['money']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		
		$wtInfo = M('wt')->find($post['id']);
		
		if(time() > $wtInfo['last_time']){
			
			$this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'当前委托已过投标时间'));
			
		}
		
        if(M('wtjoin')->where("tid=".$post['id'] ." and uid=".$this->uid)->find()){
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'已投标,请勿重复投标'));
        }
        $add[uid]=$this->uid;
        $add[tid]=$post['id'];
        $add[money]=$post['money'];
        $add[create_time]=time();
        $id=M('wtjoin')->add($add);
        if($id){
            $data=M('wtjoin')->find($id);
            $me=sinfo($this->uid);
            $data[icon]=$me[icon];
            $data[nickname]=$me[nickname];
            $data[label]=$me[label];
            $data[time]=date('Y-m-d H:i',$data[create_time]);
			
			//添加消息
			$message = array(
				'user_id' =>$wtInfo['uid'],
				'user_type'=>1,
				'message'=>"您发布的委托已有律师竞标了 ！！",
				'add_time'=>time(),
				'type'=>1,  //律师竞标
				'c_type1'=>5,  //委托订单
				'return_id'=>$wtInfo['id']
			);
			M('mymessage')->add($message);

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'投标成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'投标失败'));
        }

    }
    public function joinzc(){
        $post = $this->param;
        if(!$post['id'] || !$post['money']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(M('zcjoin')->where("tid=".$post['id'] ." and uid=".$this->uid)->find()){
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'已投标'));
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[tid]=$post['id'];
        $add[money]=$post['money'];
        $add[create_time]=time();
        $id=M('zcjoin')->add($add);
        if($id){
            /*$data=M('zcjoin')->find($id);
            $me=sinfo($this->uid);
            $data[icon]=$me[icon];
            $data[nickname]=$me[nickname];
            $data[label]=$me[label];
            $data[time]=date('Y-m-d H:i',$data[create_time]);*/

            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'投标成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'投标失败'));
        }

    }
    public function joinhz($order){


        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if(M('hzjoin')->where("tid=".$post['id'] ." and uid=".$this->uid)->find()){
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'已加入'));
        }
        $me=M('usermember')->find($this->uid);
        if($me[balance]<10){
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'余额不足10元'));
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[tid]=$post['id'];

        $add[create_time]=time();
        $id=M('hzjoin')->add($add);
        if($id){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'加入成功','data'=>$data));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'加入失败'));
        }

    }
    public function donation_detail(){
        $post = $this->param;
        $where['status']=array('eq',1);
        $where['type']=array('eq',15);
        $where['uid']=array('eq',$this->uid);

        if(!empty($post['date'])){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=M("order")->where($where)->where($ddd)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $data=M('zc')->find($v[tid]);
                    $result[$k][name]=$data[name];
                    $result[$k][money]=$v['pay_price'];
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                }
                $result[month]=$result;
                $result[monthmoney]=M("order")->where($where)->where($ddd)->sum('pay_price');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
        }else{
            $g=M("order")->where($where)->group("date_time")->order('id desc')->select();
            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $a['date_time']=array('like','%' .$mv. '%');
                    $each=M("order")->where($where)->where($a)->select();
                    foreach($each as $k=>$v){
                        $data=M('zc')->find($v[tid]);
                        $each[$k][name]=$data[name];
                        $each[$k][money]=$v['pay_price'];
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);

                    }

                    $item[month]=$each;

                    if($mv == date('Y-m')){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    $item[monthmoney]=M("order")->where($where)->where($a)->sum('pay_price');
                    $month[$mk]=$item;

                }
                $result=$month;

            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
            }
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));

    }
    public function donation_total(){
        $post = $this->param;
        $where['status']=array('eq',1);
        $where['type']=array('eq',15);
        $where['uid']=array('eq',$this->uid);
        $arr=array();
        if(M("order")->where($where)->count()){

            $result[money]=M("order")->where($where)->sum('pay_price');
            $result[num]=M("order")->where($where)->count();
        }else{
            $result[money]=0;
            $result[num]=0;
        }
        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));
    }

    public function is_col(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('collection')->where("tid=".$post['id']." and type=".$post['type']." and uid=".$this->uid." and utype=".$this->type)->find();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function collection(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]?($post[page]-1)*3: 0;
        $result= M('collection')->where("uid=".$this->uid." and utype=".$this->type." and type=".$post['type'])->order('id desc')->limit($start,3)->select();
        if($result){

            if($post[type] == 1){
                foreach ($result as $k=>$v){
                    $news=M('news')->find($v[tid]);
                    $result[$k][name]=$news[name];
                    $result[$k][icon]=picture($news[icon]);
                    $result[$k][time]=format_date($news[create_time]);
                    $result[$k][num]= M('pl')->where("tid=".$v[tid]." and type=1")->count();


                }
            }else if($post[type] == 2){
                foreach ($result as $k=>$v){
                    $staff=sinfo($v['uid']);
                    $result[$k][nickname]= $staff[nickname];
                    $result[$k][icon]= $staff[icon];

                    $dt=M('dongtai')->find($v[tid]);
                    $result[$k][content]= $dt[content];
                    $result[$k][time]= date('Y-m-d H:i',$dt[create_time]);

                    $result[$k][icons]= expic($dt[icon]);

                    $result[$k][zf]= M('dongtai')->where("pid=".$v[tid])->count();
                    $result[$k][pl]= M('pl')->where("tid=".$v[tid]." and type=2")->count();
                    $result[$k][dz]= M('dz')->where("tid=".$v[tid]." and type=2")->count();
                }
            }else if($post[type] == 3){
                foreach ($result as $k=>$v){
                    $staff=sinfo($v['tid']);
                    $result[$k]['sid']= $staff[id];
                    $result[$k][name]= $staff[nickname];
                    $result[$k][icon]= $staff[icon];
                    $result[$k][year]= $staff[year];
                    $result[$k][label]= $staff[label];
                    $result[$k][addr]= $staff[addr];
                    $result[$k][depart]= $staff[depart];


                }
            }
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'您没有收藏','data'=>$result));
        }
        $this->appReturn($result);
    }
    public function add_collection(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[tid]=$post['id'];
        $add[type]=$post['type'];
        $add[create_time]=time();

        $result = M('collection')->add($add);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'收藏成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'收藏失败','data'=>$result));
        }
    }
    public function del_collection(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        if($id= M('collection')->where("tid=".$post[id]." and type=".$post[type]." and uid=".$this->uid." and utype=".$this->type)->find()[id]){
            $result = M('collection')->delete($id);
            if($result){
                $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'您没有收藏','data'=>$result));
        }

    }
    public function is_dz(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('dz')->where("tid=".$post['id']." and type=".$post['type']." and uid=".$this->uid." and utype=".$this->type)->find();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function add_dz(){
        $post = $this->param;
        if(!$post['id'] || !$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[tid]=$post['id'];
        $add[type]=$post['type'];
        $add[create_time]=time();

        $result = M('dz')->add($add);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function is_caina(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('fanswer')->where("tid=".$post['id']." and choose=1")->find();
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function caina(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		
		$fanser = M('fanswer')->where("id=".$post[id])->find();
		$fask = M('fask')->where('id='.$fanser['tid'])->find();
		
        $result = M('fanswer')->where("id=".$post[id])->save(array('choose'=>1));
		$res2 = M('fask')->where('id='.$fanser['tid'])->save(array('sid'=>$fanser['sid']));
		
        if($result && $res2){
			
			if($fask['private'] == 1 || $fask['reward_price']>0){
				
				$map['orderno'] = array('eq',$fask['orderno']);
				$map['status'] = array('eq',1);
				$order = M('order')->where($map)->find();
				
				if($order){
					$staff=sinfo($fanser['sid']);
					//添加流水
					$water = array(
						'uid'=>$fanser['sid'],
						'type'=>1,
						'user_type'=>2,
						'water_type'=>21,//采纳咨询费
						'new_price'=> $order['pay_price']*0.7,
						'create_time'=>time(),
						'remark'=>'采纳咨询费',
						'return_id'=>$order['id'],
						'user_money'=>$staff['balance']+ ($order['pay_price']*0.7)
					);
					
					M('flowater')->add($water);
					
					M('staff')->where('id='.$fanser['sid'])->setInc('balance',$order['pay_price']*0.7);
					M('order')->where('id='.$order['id'])->save(array('s_price'=>$order['pay_price']*0.7,'sid'=>$fanser['sid']));
					
				}
				
			}
			
			//添加消息
			$message = array(
				'user_id' =>$fanser['sid'],
				'user_type'=>2,
				'message'=>"您的意见已被用户采纳 ！",
				'add_time'=>time(),
				'type'=>12,  //采纳律师
				'c_type1'=>1,  //
				'return_id'=>$fask['id']
			);
			
			M('mymessage')->add($message);
			
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'采纳成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function closewt(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		
		$wtinfo = M('wt')->where("id=".$post[id])->find();
		$map = array();
		$map['orderno'] = array('eq',$wtinfo['orderno']);
		$order = M('order')->where($map)->find();
        $result = M('wt')->where("id=".$post[id])->save(array('status'=>3));
		
		M("order")->where($map)->save(array('status'=>5));

        $user=M('usermember')->find($this->uid);
		
		if($order['pay_type'] == 2){  //微信退款
			
			$res = D('Pay')->refund($order);

		}elseif($order['pay_type'] == 1){  //支付宝退款
			
			
			
		}elseif($order['pay_type'] == 3){  //余额退款
			
			M('usermember')->where("id=".$this->uid)->save(array('balance'=>$user[balance]+50));
			$water = array(
				
				'uid'=>$user['id'],
				'user_type'=>1,
				'type'=>1,
				'water_type'=>8,
				'new_price'=> 50,
				'create_time'=>time(),
				'remark'=>'关闭委托退回保证金',
				'return_id'=>$post[id],
				'user_money'=>$user['balance']+50
			
			);
			
			M('flowater')->add($water);
			M('usermember')->where("id=".$this->uid)->save(array('balance'=>$user[balance]+50));
			
		}

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function is_zb(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('wtjoin')->where("tid=".$post['id']." and choose=1")->find();
        if($result){
			
			
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function zb(){
        $post = $this->param;
        if(!$post['id']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = M('wtjoin')->where("id=".$post[id])->save(array('choose'=>1));
        if($result){

            $join = M('wtjoin')->where("id=".$post[id])->find();
            $orderno = M('wt')->where("id=".$join[tid])->find()[orderno];
			
			$bili=bili(8);
		    $s_price=$join['money']*(100-$bili)/100;
			
            M('order')->where("orderno='".$orderno."'")->save(array('sid'=>$join[uid],'pay_price'=>$join['money'],'s_price'=>$s_price));
			
			//添加消息
			$message = array(
				'user_id' =>$join['uid'],
				'user_type'=>2,
				'message'=>"您已被用户采纳为委托律师，请点击查看！！",
				'add_time'=>time(),
				'type'=>2,  //采纳律师
				'c_type1'=>5,  //委托订单
				'return_id'=>$v['id']
			);
			M('mymessage')->add($message);
			
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function givemind(){
        $post = $this->param;
        if(!$post['id']|| $post['money']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $add['uid']=$this->uid;
        $add['sid']=$post['id'];
        $add['money']=$post['money'];
        $add['create_time']=time();
        $result = M('mind')->add($add);
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'送心意成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'失败','data'=>$result));
        }
    }
    public function umind(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*6: 0;
        $where['uid']=$this->uid;
        $result=M("mind")->where($where)->order('id desc')->limit($start,6)->select();
        if($result){
            foreach($result as $k=>$v){
                $staff=sinfo($v['sid']);
                $result[$k][name]=$staff[nickname];
                $result[$k][icon]=$staff[icon];
                $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
        }
        $result[data]=$result;
        if(M("mind")->where($where)->count()){
            $result[total]=M("mind")->where($where)->sum('money');
        }else{
            $result[total]=0;
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));

    }
    public function dzzf(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*6: 0;
        $where['d.uid']=$this->uid;
        if($post[type]==1){
            $result=M("dz z")->join('bhy_dongtai d on z.tid=d.id')
                ->where("d.uid=".$this->uid." and d.utype=".$this->type)
                ->field('z.*')->order('z.id desc')->limit($start,6)->select();
        }else{
            $result=M("zf z")->join('bhy_dongtai d on z.tid=d.id')
                ->where("d.uid=".$this->uid." and d.utype=".$this->type)
                ->field('z.*')->group('z.uid')->order('z.id desc')->limit($start,6)->select();
        }

        if($result){
            foreach($result as $k=>$v){
                if($v[utype]==1){
                    $user=uinfo($v['uid']);
                    $result[$k][name]=$user[phone];
                    $result[$k][icon]=$user[icon];
                }else{
                    $staff=sinfo($v['uid']);
                    $result[$k][name]=$staff[nickname].'律师';
                    $result[$k][icon]=$staff[icon];
                }
                $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
            }
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
        }
        if(M("mind")->where($where)->count()){
            $result[total]=M("mind")->where($where)->sum('money');
        }else{
            $result[total]=0;
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));

    }
    public function smind(){
        $post = $this->param;
        $where['sid']=array('eq',$this->uid);

        if($post['date'] != ''){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=M("mind")->where($where)->where($ddd)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $user=uinfo($v[uid]);
                    $result[$k][phone]=$user[phone];
                    $result[$k][icon]=$user[icon];
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                }
                $result[month]=$result;
                $result[monthmoney]=M("mind")->where($where)->where($ddd)->sum('money');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
            $result[data]=$result;

        }else{
            $g=M("mind")->where($where)->group("date_time")->order('id desc')->select();

            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $a['date_time']=array('like','%' .$mv. '%');
                    $each=M("mind")->where($where)->where($a)->order("id desc")->select();
                    foreach($each as $k=>$v){
                        $user=uinfo($v[uid]);
                        $each[$k][phone]=$user[phone];
                        $each[$k][icon]=$user[icon];
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);
                    }
                    $item[month]=$each;
                    $cmonth=date('Y-m');
                    if($mv == $cmonth){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    $item[monthmoney]=M("mind")->where($where)->where($a)->sum('money');
                    $month[$mk]=$item;


                }
                $result[data]=$month;

            }
        }
        if(M("mind")->where($where)->count()){
            $result[total]=M("mind")->where($where)->sum('money');
        }else{
            $result[total]=0;
        }
        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M("mind")->getLastSql(),'data'=>$result));

    }
    public function ubudget(){
        $post = $this->param;

        if($this->type==1){
            $where['uid']=array('eq',$this->uid);
        }else{
            $where['sid']=array('eq',$this->uid);
        }

        //$where['utype']=array('eq',$this->type);
        $where['status']=array('in','1,2');

        if(!empty($post['date'])){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=M("order")->where($where)->where($ddd)->order("update_time desc,id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $result[$k][money]=$v[pay_price];
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                    if($v[type]==9){
                        $action='充值';
                        $result[$k][jia]=1;
                    }else if($v[type]==13){
                        $action='提现';
                    }else if($v[status]==2){
                            $action='收入';
							$each[$k][jia]=1;
                        }else{
                        $action='支出';
                    }
                    $result[$k][action]=$action;

                }
                $result[month]=$result;
                $result[monthmoney]=M("order")->where($where)->where($ddd)->sum('pay_price');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
        }else{
            $g=M("order")->where($where)->group("date_time")->order('update_time desc,id desc')->select();
            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $a['date_time']=array('like','%' .$mv. '%');
                    $each=M("order")->where($where)->where($a)->order('update_time desc,id desc')->select();
                    foreach($each as $k=>$v){
                        $each[$k][money]=$v[pay_price];
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);
                        if($v[type]==9){
                            $action='充值';
                            $each[$k][jia]=1;
                        }else if($v[type]==13){
                            $action='提现';
                        }else if($v[status]==2){
                            $action='收入';
							$each[$k][jia]=1;
                        }else{
                            $action='支出';
                        }
						
						
                        $each[$k][action]=$action;
                    }
                    $item[month]=$each;

                    $cmonth=date('Y-m');
                    if($mv == $cmonth){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    $item[monthmoney]=M("order")->where($where)->where($a)->sum('pay_price');
                    $month[$mk]=$item;

                }
                $result=$month;

            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
            }
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));

    }
    public function sbudget(){
        $post = $this->param;

        if($this->type==1){
            $where['uid']=array('eq',$this->uid);
        }else{
            $where['sid']=array('eq',$this->uid);
        }

        //$where['utype']=array('eq',$this->type);
        $where['status']=array('eq',1);
		//$where['pay_price'] = array('gt',);

        if(!empty($post['date'])){
            $where['date_time']=array('like','%'.$post['date'].'%');
            $result=M("order")->where($where)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $each[$k][money]=$v[s_price]==0 || $v[s_price]==0.00?$v[pay_price]:$v[s_price];
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                    if($v[type]==9){
                        $action='充值';
                    }else if($v[type]==13){
                        $action='提现';
                    }else{
                        $action='收益';
                    }
                    $result[$k][action]=$action;

                }
                $result[month]=$result;
                $result[monthmoney]=M("order")->where($where)->sum('s_price');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
        }else{
            $g=M("order")->where($where)->group("date_time")->order('id desc')->select();
            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $where['date_time']=array('like','%' .$mv. '%');
                    $each=M("order")->where($where)->order('id desc')->select();
                    foreach($each as $k=>$v){
                        $each[$k][money]=$v[s_price]==0 || $v[s_price]==0.00?$v[pay_price]:$v[s_price];
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);
                        if($v[type]==9){
                            $action='充值';
                        }else if($v[type]==13){
                            $action='提现';
                        }else{
                            if($this->type==1){
                                $action='支出';
                            }else{
                                $action='收益';
                            }

                        }
                        $each[$k][action]=$action;
						
                    }
                    $item[month]=$each;

                    $cmonth=date('Y-m');
                    if($mv == $cmonth){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    $item[monthmoney]=M("order")->where($where)->sum('s_price');
                    $month[$mk]=$item;


                }
                $result=$month;

            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
            }
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result,'type'=>$this->type));

    }
    public function sincome(){
        $post = $this->param;
        $where['sid']=array('eq',$this->uid);
        $where['type']=array('in','2,3,4,5,6,7,8,10');
        $where['status']=array('in','1,2,3');
        $where['is_transfer']=array('eq',1);

        if($post['date'] != ''){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=M("order")->where($where)->where($ddd)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $user=uinfo($v[uid]);
                    $result[$k][phone]=$user[phone];
                    $result[$k][icon]=$user[icon];
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                    $result[$k][ordertype]=otype($v[type]);
                    if($v[type]==2 || $v[type]==3 || $v[type]==4 || $v[type]==5 || $v[type]==6 || $v[type]==7 || $v[type]==8){
                        $result[$k][money]=$v[s_price];
                        //$result[$k][money]=$v[s_price];
                    }else{
                        $result[$k][money]=$v[s_price];
                    }

                }
                $result[month]=$result;
                $result[monthmoney]=M("order")->where($where)->where($ddd)->sum('s_price');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
        }else{
            $g=M("order")->where($where)->group("date_time")->order('id desc')->select();
            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $a['date_time']=array('like','%' .$mv. '%');
                    $each=M("order")->where($where)->where($a)->order('id desc')->select();
					$sql = M("order")->getLastSql();
                    foreach($each as $k=>$v){
                        $user=uinfo($v[uid]);
                        $each[$k][phone]=$user[phone];
                        $each[$k][icon]=$user[icon];
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);
                        $each[$k][ordertype]=otype($v[type]);
                        if($v[type]==2 || $v[type]==3 || $v[type]==4 || $v[type]==5 || $v[type]==6 || $v[type]==7 || $v[type]==8){
                            //$result[$k][money]=$v[s_price];
                            $each[$k][money]=$v[s_price];
                        }else{
                            $each[$k][money]=$v[s_price];
                        }
                    }
                    $item[month]=$each;

                    $cmonth=date('Y-m');
                    if($mv == $cmonth){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    $item[monthmoney]=M("order")->where($where)->where($a)->sum('s_price');
                    $month[$mk]=$item;

                }
                $result[data]=$month;

            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
            }
        }

        $this->appReturn(array('status'=>true,'code'=>200,'date'=>$g,'msg'=>$sql,'data'=>$result));

    }
    public function hzhistory(){
        $post = $this->param;
        $where['uid']=array('eq',$this->uid);
        $where['status']=array('eq',1);

        if($post['date'] != ''){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=M("order")->where($where)->where($ddd)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    $hz=M("hz")->find($v['tid']);
                    $result[$k][name]='中青年法律互助计划';
                    $result[$k][num]=2;
                    $result[$k][money]=8.66;
                    $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
                }
                $result[month]=$result;
                $result[monthmoney]=M("order")->where($where)->where($ddd)->sum('pay_price');
            }else{
                $result[monthmoney]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
            $result[num]=2;
        }else{
            $g=M("hz")->where($where)->group("date_time")->order('id desc')->select();
            if($g){
                $month=array_column($g,'date_time');
                foreach($month as $mk=>$mv) {
                    $a['date_time']=array('like','%' .$mv. '%');
                    $each=M("hz")->where($where)->where($a)->select();
                    foreach($each as $k=>$v){
                        $each[$k][num]=2;
                        $each[$k][money]=8.66;
                        $each[$k][time]=date('Y-m-d H:i',$v[create_time]);

                    }
                    $item[month]=$each;

                    $cmonth=date('Y-m');
                    if($mv == $cmonth){
                        $item[monthname]='本月';
                    }else{
                        $item[monthname]=monthname(explode('-',$mv)[1]);
                    }
                    if(M("order")->where($where)->where($a)->count()){
                        $item[monthmoney]=M("order")->where($where)->where($a)->sum('pay_price');
                    }else{
                        $item[monthmoney]=8.66;
                    }

                    $item[num]=2;
                    $month[$mk]=$item;

                }
                $result=$month;

            }else{
                $this->appReturn(array('status'=>false,'code'=>201,'msg'=>$sql,'data'=>$result));
            }
        }

        $this->appReturn(array('status'=>true,'code'=>200,'msg'=>$sql,'data'=>$result));

    }
    public function smsg(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]>0?($post[page]-1)*4 : 0;
        if($post['type']==1){
            $result=array();
            $arr=M("ask")->field("uid,create_time")->where("sid=".$this->uid." and status=1")->order('id desc')->select();
            foreach($arr as $k=>$v) {
                $user=uinfo($v[uid]);
                $arr[$k][type]=1;
                $arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
            $ids=implode(',',array_column(M("dongtai")->where("uid=".$this->uid." and utype=".$this->type)->select(),'id'));
            $where['tid']=array('in',$ids);
            $where['type']=array('eq',2);
            $arr=M("pl")->field("tid,uid,utype,create_time")->where($where)->order("id desc")->select();

            foreach($arr as $k=>$v) {
                $arr[$k][type]=2;

                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                if($v[utype]==1){
                    $user=uinfo($v[uid]);
                    $arr[$k][phone]=$user[phone];
                    $arr[$k][icon]=$user[icon];
                }else{
                    $user=sinfo($v[uid]);
                    $arr[$k][phone]=$user[nickname];
                    $arr[$k][icon]=$user[icon];
                }
                array_push($result,$arr[$k]);
            }
			

			
			$ids=implode(',',array_column(M("ask")->field("id")->where("sid=".$this->uid." and status=5")->order('id desc')->select(),'id'));
			$where['tid']=array('in',$ids);
            $where['type']=array('eq',3);
            $arr=M("pl")->field("tid,uid,utype,create_time")->where($where)->order("id desc")->select();

            foreach($arr as $k=>$v) {
                $user=uinfo($v[uid]);
                $arr[$k][type]=3;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                if($v[utype]==1){
                   
                    $arr[$k][phone]=$user[phone];
                    $arr[$k][icon]=$user[icon];
                }else{
               
                    $arr[$k][phone]=$user[nickname];
                    $arr[$k][icon]=$user[icon];
                }
                array_push($result,$arr[$k]);
            }
			
			$arr = M('order')->where('sid='.$this->uid.' and type=10 and status=1')->field('uid,create_time')->select();
			foreach($arr as $k=>$v) {
                $user=uinfo($v[uid]);
                $arr[$k][type]=4;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                $arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                array_push($result,$arr[$k]);
            }
			
			$arr = M('fanswer')->where('sid='.$this->uid.' and choose=1')->select();
			foreach($arr as $k=>$v) {
				
				$fask  = M('fask')->find($v['tid']);
				
                $user=uinfo($fask['uid']);
                $arr[$k][type]=5;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                $arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                array_push($result,$arr[$k]);
            }
			
			//图文咨询回复消息
			$arr=M("tw_info as t")->join('bhy_ask as a on a.id=t.ask_id')->field('t.*')->where("t.type=1 and a.sid=".$this->uid)->select();
            foreach($arr as $k=>$v) {
				
				$user=uinfo($v['uid']);
                $arr[$k][type]=6;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
				$arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                array_push($result,$arr[$k]);
            }
			
			//$result=array();
			//确认订单完成
            $arr=M("ask")->field("uid,create_time")->where("sid=".$this->uid." and status=4 and type !=6")->order('id desc')->select();
            foreach($arr as $k=>$v) {
                $user=uinfo($v[uid]);
                $arr[$k][type]=7;
                $arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
			//支付律师费
			$arr=M("ask")->field("uid,create_time")->where("sid=".$this->uid." and status=4 and type=6")->order('id desc')->select();
            foreach($arr as $k=>$v) {
                $user=uinfo($v[uid]);
                $arr[$k][type]=8;
                $arr[$k][phone]=$user[phone];
                $arr[$k][icon]=$user[icon];
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
			
			$last_names = array_column($result,'time');
			array_multisort($last_names,SORT_DESC,$result);
			
			$map = array();
			$map['user_type'] = array('eq',$this->type);
			$map['user_id'] = array('eq',$this->uid);
			
			M('mymessage')->where($map)->save(array('is_view'=>1,'update_time'=>time()));

        }else{
            $result=M("sysmsg")->where("status=1")->order('id desc')->limit($start,4)->select();
            foreach($result as $k=>$v) {
                $result[$k][time] = date('Y-m-d H:i', $v[create_time]);
            }
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>1,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>1,'data'=>$result));
        }
    }
	

	
    public function umsg(){
        $post = $this->param;
        if(!$post['type']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $start=$post[page]>0?($post[page]-1)*10 : 0;
        if($post['type']==1){
			
			
			
            $result=array();
            $re=M("fask")->field('id')->where("uid=".$this->uid)->order('id desc')->select();
            $ids=implode(',',array_column($re,'id'));
            $wh['tid']=array('in',$ids);
            $arr=M("fanswer")->field('tid,create_time')->where($wh)->order('id desc')->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=1;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }

            $ids=implode(',',array_column(M("dongtai")->where("uid=".$this->uid." and utype=".$this->type)->select(),'id'));
            $where['tid']=array('in',$ids);
            $where['type']=array('eq',2);
            $arr=M("pl")->field("tid,uid,utype,create_time")->where($where)->order("id desc")->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=2;

                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                if($v[utype]==1){
                    $user=uinfo($v[uid]);
                    $arr[$k][phone]=$user[phone];
                    $arr[$k][icon]=$user[icon];
                }else{
                    $user=sinfo($v[uid]);
                    $arr[$k][phone]=$user[nickname];
                    $arr[$k][icon]=$user[icon];
                }
                array_push($result,$arr[$k]);
            }
			
			
			
            
            $ids=implode(',',array_column(M("ask")->where("uid=".$this->uid)->select(),'id'));
            $t['tid']=array('in',$ids);
            //$t['know']=array('eq',0);
            $arr=M("tuwen")->where($t)->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=3;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }

            $arr=M("ask")->where("uid=".$this->uid." and status=2")->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=4;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }

            $re=M("wt")->field('id')->where("uid=".$this->uid)->order('id desc')->select();
            $ids=implode(',',array_column($re,'id'));
            $wh['tid']=array('in',$ids);
            $arr=M("wtjoin")->field('tid,create_time')->where($wh)->order('id desc')->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=5;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
			
			$arr=M("ask")->where("uid=".$this->uid." and status=9 and state=5")->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=6;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
			
			
			$arr=M("ask")->where("uid=".$this->uid." and status=7")->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=7;
                $arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
                array_push($result,$arr[$k]);
            }
			
			//注册提示
			$user = $this->me;
			$arr = array('type'=>8,'time'=>date('Y-m-d H:i', $user[create_time]));
			array_push($result,$arr);
			//图文咨询回复消息
			$arr=M("tw_info as t")->join('bhy_ask as a on a.id=t.ask_id')->field('t.*')->where("t.type=2 and a.uid=".$this->uid)->select();
			
			if($arr){
				
				foreach($arr as $k=>$v) {
					$arr[$k][type]=9;
					$arr[$k][time] = date('Y-m-d H:i', $v[create_time]);
					array_push($result,$arr[$k]);
				}
				
			}
			
			$map = array();
			$map['utype'] = array('eq',1);
			$map['uid'] = array('eq',$this->uid);
			$map['reply'] = array('neq','');
			
			$arr=M("pl")->field("tid,uid,utype,update_time")->where($map)->order("id desc")->select();
            foreach($arr as $k=>$v) {
                $arr[$k][type]=10;

                $arr[$k][time] = date('Y-m-d H:i', $v[update_time]);
				
				$askInfos = M('ask')->find($v['tid']);
				
                $user=sinfo($askInfos['sid']);
				$arr[$k][phone]=$user[nickname];
				$arr[$k][icon]=$user[icon];
                array_push($result,$arr[$k]);
            }
            
			$last_names = array_column($result,'time');
			array_multisort($last_names,SORT_DESC,$result);
			
			$map = array();
			$map['user_type'] = array('eq',$this->type);
			$map['user_id'] = array('eq',$this->uid);
			
			M('mymessage')->where($map)->save(array('is_view'=>1,'update_time'=>time()));

           // array_multisort(array_column($result,'create_time'),SORT_DESC,$result);
        }else{
            $result=M("sysmsg")->where("status=1")->order('id desc')->select();
            foreach($result as $k=>$v) {
                $result[$k][time] = date('Y-m-d H:i', $v[create_time]);
            }
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>1,'data'=>$result,'sql'=>$sql));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>1,'data'=>$result));
        }
    }
    public function notice(){
        $post = $this->param;
		$map = array();
		$map['user_type'] = array('eq',$this->type);
		$map['user_id'] = array('eq',$this->uid);
		$map['is_view'] = array('eq',0);
		
		$result = M('mymessage')->where($map)->count();
		
        

        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result,'sql'=>M("fanswer")->getLastSql()));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result,'sql'=>M("fask")->getLastSql()));
        }

    }
    public function know(){
        $post = $this->param;
        $ids=implode(',',array_column(M("fask")->where("uid=".$this->uid)->select(),'id'));
        $wh['tid']=array('in',$ids);
        $wh['know']=array('eq',0);
        $fask=M("fanswer")->field('id')->where($wh)->select();
        foreach($fask as $k=>$v) {
            M("fanswer")->where("id=".$v[id])->save(array('know'=>1));
        }

        $ids=implode(',',array_column(M("dongtai")->where("uid=".$this->uid." and utype=".$this->type)->select(),'id'));
        $where['tid']=array('in',$ids);
        $where['type']=array('eq',2);
        $where['know']=array('eq',0);
        $pl=M("pl")->field('id')->where($where)->select();
        foreach($pl as $k=>$v) {
            M("pl")->where("id=".$v[id])->save(array('know'=>1));
        }

        $ids=implode(',',array_column(M("ask")->where("uid=".$this->uid)->select(),'id'));
        $t['tid']=array('in',$ids);
        $t['know']=array('eq',0);
        $tuwen=M("tuwen")->where($t)->select();
        foreach($tuwen as $k=>$v) {
            M("tuwen")->where("id=".$v[id])->save(array('know'=>1));
        }

        $arr=M("ask")->where("uid=".$this->uid." and status=2 and know=0")->select();
        foreach($arr as $k=>$v) {
            M("ask")->where("id=".$v[id])->save(array('know'=>1));
        }

        $re=M("wt")->field('id')->where("uid=".$this->uid)->select();
        $ids=implode(',',array_column($re,'id'));
        $wt['tid']=array('in',$ids);
        $wt['know']=array('eq',0);
        $arr=M("wtjoin")->field('id')->where($wt)->select();
        foreach($arr as $k=>$v) {
            M("wtjoin")->where("id=".$v[id])->save(array('know'=>1));
        }
        if(1){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
    public function swt(){
        $post = $this->param;
        $start=$post[page]>0?($post[page]-1)*4 : 0;

        $sort=$post['sort'];
        if($sort==1){
            $order="w.id desc";
        }elseif($sort==2){
            $order="w.id desc";
        }else{
            $order="w.id desc";
        }
        $ww['j.uid']=array('eq',$this->uid);
        if($post['choose']>0){
			
			if($post['choose'] == 1){
				$ww['j.choose']=array('eq',1);
				
			}else{
				$ww['j.choose']=array('eq',1);
				$ww['w.status']=array('eq',4);
				$ww['w.payed']=array('eq',1);
				$ww['w.is_confirm']=array('eq',1);
			}
			
            
        }else{
            $ww['j.choose']=array('eq',0);
        }
        if($post['ajid']>0){
            $ww['w.ajid']=array('eq',$post['ajid']);
        }
        $result = M('wtjoin j')->field("w.*,j.uid as sid,j.money,j.choose")->join("bhy_wt w on w.id=j.tid")->where($ww)->order($order)->limit($start,6)->select();
        foreach($result as $k=>$v){
            $user=M('usermember')->find($v[uid]);
            $result[$k][icon]=picture($user[icon]);
            $result[$k][phone]=yc_phone($user[phone]);
            $result[$k][addr]=$user[addr];

            $result[$k][ajtype]=M('ajcate')->find($v[ajid])[name];
            $result[$k][price]=$v[money];
            $result[$k][time]=date('Y-m-d H:i',$v[create_time]);
            $result[$k][num]=M('wtjoin')->where("tid=".$v[id])->count();
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>M('wtjoin j')->getLastSql(),'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M('wtjoin j')->getLastSql(),'data'=>$result));
        }

    }
    public function spl(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4 : 0;

        $ids=implode(',',array_column(M("ask")->where("sid=".$this->uid)->select(),'id'));
        $where['tid']=array('in',$ids);
        $where['type']=array('eq',3);
        $result=M("pl")->where($where)->order("id desc")->limit($start,4)->select();
        foreach($result as $k=>$v){
            $ask=M("ask")->find($v[tid]);
            $user=uinfo($ask[uid]);
            $result[$k][phone]= $user[phone];
            $result[$k][icon]= $user[icon];
            $result[$k][time]= date('Y-m-d',$v[create_time]);

            $result[$k][ordertype]= ordertype($ask[type]);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }

    }
    public function upl(){
        $post = $this->param;
        $start=$post[page]?($post[page]-1)*4 : 0;
        
        $ids=implode(',',array_column(M("ask")->where("uid=".$this->uid)->select(),'id'));
        $where['tid']=array('in',$ids);
        $where['type']=array('eq',3);
        $result=M("pl")->where($where)->order("id desc")->limit($start,4)->select();
        foreach($result as $k=>$v){
            $ask=M("ask")->find($v['sid']);
            $user=sinfo($ask['sid']);
            $result[$k][name]= $user[nickname];
            $result[$k][icon]= $user[icon];
            $result[$k][time]= date('Y-m-d H:i',$v[create_time]);

            $result[$k][ordertype]= ordertype($ask[type]);
        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>M("order")->getLastSql(),'data'=>$result));
        }

    }

    public function replypl(){
        $post = $this->param;
        if(!$post['id'] || !$post['reply']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
		
		$plInfo = M('pl')->where("id=".$post[id])->find();
		
        $id=M('pl')->where("id=".$post[id])->save(array('reply'=>$post[reply],'update_time'=>time()));
        if($id){
			
			//添加消息
			$message = array(
				'user_id' =>$plInfo['uid'],
				'user_type'=>1,
				'message'=>"您的评价已有律师回复 ！！",
				'add_time'=>time(),
				'type'=>10,  //用户评价订单
				'c_type1'=>1,  //咨询订单
				'return_id'=>$plInfo['tid']
			);
			M('mymessage')->add($message);
			
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'发表成功','data'=>$id));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败'));
        }

    }

    public function bankcard(){
        $post = $this->param;
        if($post['yzm']){
            if(S($post['phone'])!=$post['yzm']){
                $this->appReturn( array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data));
            }
        }
        $arr=array('cardno'=>$post[cardno],'bank'=>$post[bank]);
        if($this->type==1){
            $result = M('usermember')->where(array('id'=>$this->uid))->save($arr);
        }else{
            $result = M('staff')->where(array('id'=>$this->uid))->save($arr);
        }
        if($result){
            $this->appReturn(array('status'=>true,'msg'=>'绑定成功','code'=>200,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'绑定失败','code'=>200,'data'=>$result));
        }


    }
    public function yaoqin(){
        $post = $this->param;
        if($this->type==1){
            $m=M('usermember');
        }elseif($this->type==2){
            $m=M('staff');
        }

        $where['pid']=array('eq',$this->uid);

        if(!empty($post['date'])){
            $ddd['date_time']=array('like','%'.$post['date'].'%');
            $result=$m->where($where)->where($ddd)->order("id desc")->select();
            if($result){
                foreach($result as $k=>$v){
                    if ($this->type == 1) {
                        $user = uinfo($v[id]);
                        $result[$k][icon] = $user[icon];
                        $result[$k][phone] = $user[phone];
                    } elseif ($this->type == 2) {
                        $user = sinfo($v[id]);
                        $result[$k][icon] = $user[icon];
                        $result[$k][phone] = $user[nickname];
                    }

                    $result[$k][time] = date('Y-m-d H:i', $v[create_time]);
                }
                $result[month]=$result;
                $result[num]=$m->where($where)->where($ddd)->count();
            }else{
                $this->appReturn(array('status'=>false,'msg'=>'失败','code'=>200,'data'=>$result));
                $result[num]=0;
            }
            $result[monthname]=monthname(explode('-',$post['date'])[1]);
        }else {
            $g = $m->where($where)->group("date_time")->order('id desc')->select();
            if ($g) {
                $month = array_column($g, 'date_time');
                foreach ($month as $mk => $mv) {
                    $a['date_time'] = array('like', '%' . $mv . '%');
                    $each = $m->where($where)->where($a)->field('id,phone,icon,create_time')->select();
                    $sql=$m->getLastSql();
                    foreach ($each as $k => $v) {
                        if ($this->type == 1) {
                            $user = uinfo($v[id]);
                            $each[$k][icon] = $user[icon];
                            $each[$k][phone] = $user[phone];
                        } elseif ($this->type == 2) {
                            $user = sinfo($v[id]);
                            $each[$k][icon] = $user[icon];
                            $each[$k][phone] = $user[nickname].'律师';
                        }

                        $each[$k][time] = date('Y-m-d H:i', $v[create_time]);
                        $each[$k][money] = 1;

                    }
                    $item[month] = $each;
                    $cmonth = date('Y-m');
                   /* if ($mv == $cmonth) {
                        $item[monthname] = '本月';
                    } else {
                        $item[monthname] = explode('-', $mv)[1] . '月';
                    }*/
                    $item[num] = $m->where($where)->where($a)->count();
                    $month[$mk] = $item;


                }
                $result = $month;

            }
        }
        if($result){
            $this->appReturn(array('status'=>true,'msg'=>$sql,'code'=>200,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'失败','code'=>200,'data'=>$result));
        }

    }
    public function zhuanfa(){
        $post = $this->param;
        $dt = M('dongtai')->find($post[id]);


        $add[uid]=$this->uid;
        $add[utype]=$this->type;
        $add[pid]=$post[id];
        $add[content]=$dt[content];
        $add[icon]=$dt[icon];
        $add[create_time]=time();
        $re= M('dongtai')->add($add);
        if($re){
                $result=M('dongtai')->find($re);
                $result[icons]= expic($result[icon]);
                if($this->type==1){
                    $user=uinfo($this->uid);
                    $result[nickname]= yc_phone($user[phone]);
                    $result[icon]= $user[icon];
                }else{
                    $staff=sinfo($this->uid);
                    $result[nickname]= $staff[nickname].'律师';
                    $result[icon]= $staff[icon];
                }

                $result[time]= date('Y-m-d H:i',time());

                
            $this->appReturn(array('status'=>true,'msg'=>'成功','code'=>200,'data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'msg'=>'失败','code'=>200,'data'=>$result));
        }


    }
    //充值说明
    public function pay_explanin(){
        $result = D("User")->pay_explanin();
        $this->appReturn($result);
    }
    //充值
    public function pay(){
        $post = $this->param;
        if( !$post['uid'] || !$post['type'] || !$post['money'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = D('User')->pay($post);
        $this->appReturn($result);
    }
    //充值记录
    public function pay_log(){
        $post = $this->param;
        $result = D("User")->pay_log($post);
        $this->appReturn($result);
    }
    //我的积分
    public function score(){
        $post = $this->param;
        $result = D("User")->score($post);
        $this->appReturn($result);
    }
    //设置支付密码
    public function set_zfpwd(){
        $post = $this->param;
        if( !$post['uid'] || !$post['yzm'] || !$post['pwd'] || !$post['qr_pwd'] || !$post['phone'] ){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = D('User')->set_zfpwd($post);
        $this->appReturn($result);
    }
    //修改支付密码
    public function edit_pay(){
        $post = $this->param;
        if( !$post['uid'] || !$post['yzm'] || !$post['old_pwd'] || !$post['new_pwd'] || !$post['phone']){
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'未完善信息'));
        }
        $result = D('User')->edit_pay($post);
        $this->appReturn($result);
    }

    public function fenhong(){
        $post = $this->param;

        $start=$post[page]?($post[page]-1)*10 : 0;
        
        $w[pid]=array('eq',$this->uid);
        $w[vip]=array('neq',0);
        $result = M('usermember')->field('id,nickname,vip,create_time')->where($w)->order("create_time desc")->limit($start,10)->select();
        foreach ($result as $k=>$v){
            $result[$k][money]= M('red')->where("uid=".$v[id])->find()[money];

        }
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'失败','data'=>$result));
        }

    }
	
	//律师撤标
	public function cancelwt(){
		
		$post = $this->param;
		
		$join = M('wtjoin')->where("tid=".$post['id'] ." and uid=".$this->uid)->find();
		if(!$join){
			
			$this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'撤标异常'));
			
		}
		
		$result = M('wtjoin')->where('id='.$join['id'])->delete();
		
		if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'撤标成功'));
        }else{
            $this->appReturn(array('status'=>false ,'code'=>201,'msg'=>'撤标失败，请稍后再试！'));
        }
		
	}

}
