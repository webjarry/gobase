<?php

namespace App\Controller;

class StaffController extends HomeController {

    public $param;


    public function _initialize() {

        parent::_initialize();



    }
    
     //首页
    public function index(){
        $post = $this->param;
        if( !$post['uid'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->index($post);
        $this->appReturn($result);
    }
    
    //修改个人资料
    public function edit_user(){
        $post = $this->param;
        if( !$post['uid'] || !$post['icon'] || !$post['nikename'] ){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->edit_user($post);
        $this->appReturn($result);
    }
    
    //我的订单
    public function order(){
        $post = $this->param;
        if( !$post['uid']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->order($post);
        $this->appReturn($result);
    }
    
    //订单详情
    public function order_detile(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->order_detile($post);
        $this->appReturn($result);
    }
    
    
    //确认
    public function qrqh(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->qrqh($post);
        $this->appReturn($result);
    }
    
    //完成
    public function order_down(){
        $post = $this->param;
        if( !$post['uid'] || !$post['order_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Staff')->order_down($post);
        $this->appReturn($result);
    }
	
	//更改委托进度和结果
	
    public function wt_status(){
		
		$post = $this->param;
		
		if(!$post['id'] || !$post['progress']){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
			
		}
		
		$wt_info = M('wt')->where('id='.$post['id'])->find();
		
		if(!$wt_info){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'委托信息不存在'));
			
		}
		
		$data['last_time'] = time();
		$data['progress'] = $post['progress'];
		
		if(!empty($post['progress_result'])){
			
			$data['progress_result'] = $post['progress_result'];
			$data['status'] = 4;
			
		}
		
		$data['id'] = $wt_info['id'];
		
		if(M('wt')->save($data)){
			
			if(!empty($post['progress_result'])){
				
				$map['orderno'] = array('eq',$wt_info['orderno']);
				$re = M('order')->where($map)->save(array('update_time'=>time()));
				
			}
			
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'状态更新成功'));
			
		}else{
			
			$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'状态更新失败，请稍后再试！'));
			
		}
		
	}
	
	//确认委托完成
	public function wt_success(){
		
		$post = $this->param;
		
		if(!$post['id']){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
			
		}
		
		$wt_info = M('wt')->where('id='.$post['id'])->find();
		if($wt_info['status'] != 4 && empty($wt_info['progress_result'])){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单状态异常'));
			
		}
		$map['orderno'] = array('eq',$wt_info['orderno']);
		
		$orderInfo = M('order')->where($map)->find();
		
		$result = M('order')->where($map)->save(array('is_confirm'=>1,'is_transfer'=>1,'update_time'=>time()));
		
		$res1 = M('wt')->where('id='.$post['id'])->save(array('is_confirm'=>1,'status'=>4));
		
		
		//添加消息
		$message = array(
			'user_id' =>$orderInfo['sid'],
			'user_type'=>2,
			'message'=>"您的委托订单已自动确认完成",
			'add_time'=>time(),
			'type'=>4,  //自动确认订单
			'c_type1'=>5,  //咨询订单
			'return_id'=>$orderInfo['id']
		);
		
		$user=sinfo($orderInfo['sid']);
							
		$water = array(
			'uid'=>$orderInfo['sid'],
			'type'=>1,
			'user_type'=>2,
			'water_type'=>19,//订单到账
			'new_price'=> $orderInfo['s_price'],
			'create_time'=>time(),
			'remark'=>'订单到账',
			'return_id'=>$orderInfo['id'],
			'user_money'=>$user['balance']+$orderInfo['s_price']
		);
		
		$res3 = M('staff')->where('id='.$orderInfo['sid'])->save(array('balance'=>$water['user_money']));
		
		M('flowater')->add($water);
		M('mymessage')->add($message);
		
		if($result && $res1 && $res3){
			
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'订单确认成功'));
			
		}else{
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单确认失败，请稍后再试'));
			
		}
		
	}
	
	//互助详情
	public function hz_detail(){
		
		$fam = M('family')->count();
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('count'=>$fam,'person'=>0,'money'=>0.00)));
		
	}
	
	//添加图片咨询详情记录
	public function twinfoAdd(){
		
		$post = $this->param;
		if((!$post['content'] && !$post['icon'] && !$post['file']) || !$post['ask_id']){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
			
		}
		
		$data['ask_id'] = $post['ask_id'];
		$data['uid'] = $this->uid;
		$data['type'] = $this->type;
		$data['icon'] = empty($post['icon'])?'':$post['icon'];
		$data['file'] = empty($post['file'])?'':$post['file'];
		$data['content'] = empty($post['content'])?'':$post['content'];
		$data['create_time'] = time();
		
		//添加消息
		$message = array(
			'user_id' =>$this->uid,
			'user_type'=>$this->type,
			'message'=>$this->type == 1?'您的图文咨询订单已有会员回复 ！':'您的图文咨询消息律师已回复 ！！',
			'add_time'=>time(),
			'type'=>11,  //律师拒单
			'c_type1'=>1,  //咨询订单
			'return_id'=>$post['ask_id']
		);
		
		if(M('tw_info')->add($data) && M('mymessage')->add($message)){
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'发布成功'));
		}else{
			$this->appReturn(array('status'=>false,'code'=>201,'msg'=>'发布失败，请稍后再试'));
		}
		
	}
	
	//图片咨询详情记录
	public function twinfoList(){
		
		$post = $this->param;
		if(!$post['ask_id']){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
			
		}
		
		$list = M('tw_info')->where('ask_id='.$post['ask_id'])->order('create_time desc')->select();
		
		foreach($list as $k => $v){
			
			if(empty($v['icon'])){
				
				$list[$k]['icon'] = '';
				
			}else{
				$map['id'] = array('in',$v['icon']);
				$iconList = M('picture')->where($map)->field('path')->select();
				
				$list[$k]['icon'] = $iconList;
				
			}
			
			if(empty($v['file'])){
				
				$list[$k]['file'] = '';
				
			}else{
				$map['id'] = array('in',$v['file']);
				$fileList = M('picture')->where($map)->field('path,name,size')->select();
				
				$list[$k]['file'] = $fileList;
				
			}
			
			$list[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
			
			if($v['type'] == 1){
				
				$user = M('usermember')->where('id='.$v['uid'])->field('icon,phone')->find();
				
			}else{
				
				$user = M('staff')->where('id='.$v['uid'])->field('icon,phone')->find();
				
			}
			
			$list[$k]['phone'] = substr_replace($user['phone'], '****', 3, 4);
				
			if(empty($user['icon'])){
				
				$list[$k]['user_icon'] = '/Public/Home/img/user_tx.png';
				
			}else{
				
				if(is_numeric($user['icon'])){
				
					$list[$k]['user_icon'] = picture($user['icon']);
				
				}else{
					
					$list[$k]['user_icon'] = $user['icon'];
					
				}
				
			}
			
		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$list));
		
		
		
	}
	
	public function msgStatus(){
		
		$askNum = M('ask')->where('status<4 and status!=7 and status !=9 and uid='.$this->uid)->count();
		$map['type'] = array('in','16,17,18');
		$map['status'] = array('eq',1);
		$map['uid'] = array('eq',$this->uid);
		$qyNum = M('order')->where($map)->count();

		$wtNum = M('wt')->where('status<4 and status!=3 and uid='.$this->uid)->count();
		
		$sql = M('wt')->getLastSql();
		
		$map = array();
		$map['user_id'] = array('eq',$this->uid);
		$map['user_type'] = array('eq',1);
		$map['type'] = array('eq',1);
		$map['c_type1'] = array('eq',1);
		$map['is_view'] = array('eq',0);
		$faskNum = M('mymessage')->where($map)->select();
		
		foreach($faskNum as $k => $v){
			
			$fanser = M('fanswer')->where('tid='.$v['return_id'].' and choose=1')->find();
			
			if($fanser){
				
				unset($faskNum[$k]);
				
			}
			
		}
		
		$map = array();
		$map['user_id'] = array('eq',$this->uid);
		$map['user_type'] = array('eq',1);
		$map['type'] = array('eq','1,2');
		$map['c_type1'] = array('eq',2);
		$map['is_view'] = array('eq',0);
		$zcNum = M('mymessage')->where($map)->count();
		
		//$zcNum = M('zc')->where('uid='.$this->uid)->count();
		
		$famNum = M('family')->where('uid='.$this->uid)->count();
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('askNum'=>empty($askNum)?0:$askNum,'qyNum'=>empty($qyNum)?0:$qyNum,'wtNum'=>empty($wtNum)?0:$wtNum,'faskNum'=>empty($faskNum)?0:count($faskNum),'zcNum'=>empty($zcNum)?0:$zcNum,'famNum'=>empty($famNum)?0:$famNum),'sql'=>$sql));
		
	}
	
	//咨询订单状态数量
	public function askNumList(){
		
		$dfkNum = M('ask')->where('status=0 and uid='.$this->uid)->count();//待付款数量
		$djdNum = M('ask')->where('status=1 and uid='.$this->uid)->count();//待接单数量
		$yjdNum = M('ask')->where('status=2 and uid='.$this->uid)->count();//已接单数量
		$shNum = M('ask')->where('status=6 and after=1 and uid='.$this->uid)->count();//售后中数量
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('dfkNum'=>empty($dfkNum)?0:$dfkNum,'djdNum'=>empty($djdNum)?0:$djdNum,'yjdNum'=>empty($yjdNum)?0:$yjdNum,'shNum'=>empty($shNum)?0:count($shNum))));
		
	}
	
	//委托订单状态数量
	public function wtNumList(){
		
		
		$map['_string'] = '(w.status=1 and w.uid='.$this->uid.' and payed=0) or (w.status=2 and w.payed=1 and w.twopay=0 and w.uid='.$this->uid.')';
		
		$wtlist = M('wt as w')->where($map)->select();//待付款数量
		foreach($wtlist as $k => $v){
				
			if($v['status'] == 2){
				
				$re= M('wtjoin')->where('tid='.$v[id].' and choose=1')->find();
			
				if(!$re){
					
					unset($wtlist[$k]);
					
				}else{
					
					$newArray[] = $v;
					
				}
			}else{
				$newArray[] = $v;
			}
			
		}
		$dfkNum = $newArray;
		$map = array();
		$map['w.uid'] = array('eq',$this->uid);
		$map['w.status']=array('in','2,3');
		$map['w.payed']=array('eq',1);
		$map['w.twopay']=array('eq',0);
		$map['w.is_confirm']=array('eq',0);
		
		$bidNum = M('wt as w')->where($map)->count();//竞标完成数量
		
		$map['w.twopay']=array('eq',1);
		
		$bidIngNum = M('wt as w')->where($map)->count();
		
		$this->appReturn(array('status'=>true,'sql'=>$sql,'code'=>200,'msg'=>'数据获取成功','data'=>array('dfkNum'=>empty($dfkNum)?0:count($dfkNum),'bidNum'=>empty($bidNum)?0:$bidNum,'bidIngNum'=>empty($bidIngNum)?0:$bidIngNum)));
		
	}
	
	//判断是否设置支付密码
	public function pay_password(){
		
		$user = $this->me;
		
		if(empty($user['zfpwd'])){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未设置支付密码'));
			
		}else{
			
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'已设置支付密码'));
			
		}
		
	}
	
	//搜索律师、合同
	public function search_list(){
		
		$post = $this->param;
        $start=$post[page]?($post[page]-1)*8 : 0;
        $where['pass']=array('eq',1);
        if($post['addrid']>0){
            $where['addrid']=array('eq',$post['addrid']);
        }
        if(!empty($post['keywords'])){
			
			$map = array();
			$map['name'] = array('like','%'.$post['keywords'].'%');
			
			$cate = M('ajcate')->where($map)->field('id')->find();
			
			if(!empty($cate)){
				$whes['label'] = array('like','%'.$cate['id'].'%');
			}
			
			$whes['nickname|phone'] = array('like','%'.$post['keywords'].'%');
			$whes['_logic'] = 'or';
            $where['_complex'] = $whes;
        }

        if($post[sort]==1){
            $order="year desc";
        }else if($post[sort]==2){
            $order="score desc";
        }else{
            $order="sort desc";
        }
        $result = M('staff')->where($where)->order('sort desc,score desc,year desc')->limit($start,8)->select();
        foreach($result as $k=>$v){
            $staff=sinfo($v['id']);
            $result[$k][name]= $staff[nickname];
            $result[$k][icon]= $staff[icon];
            $result[$k][label]= $staff[label];
			$result[$k]['year'] = $v['work_year'];
        }
		
		//合同
		
		$map = array();
		$map['name'] = array('like','%'.$post['keywords'].'%');
		
		$map['status']=array('eq',1);
		
		$list = M('hetong')->where($map)->order('id desc')->limit($start,6)->select();
		
		foreach($list as $k=>$v){
            $list[$k]['icon']=expic($v['icon'])[0];
            $list[$k]['time']=date('Y-m-d H:i',$v[create_time]);
        }
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('staff'=>$result,'hetong'=>$list)));
	}
	
	//获取保全申请
	public function bq_list(){
		
		$post = $this->param;
		
		$map = array();
		$map['uid'] = array('eq',$this->uid);
		$map['utype'] = array('eq',$this->type);
		
		$list = M('baoquan')->where($map)->order('create_time desc')->select();
		
		foreach($list as $k => $v){
			
			$list[$k]['status_title'] = $v['status'] == 1?'未处理':'已处理';
			$list[$k]['create_time'] = format_date($v['create_time']);
			
		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$list));
		
	}
	
	//查看消息
	public function viewMessage(){
		
		$post = $this->param;
		$map['user_id'] = array('eq',$this->uid);
		$map['user_type'] = array('eq',$this->type);
		if(!empty($post['type'])){
			$map['type'] = array('eq',$post['type']);
		}
		if(!empty($post['c_type1'])){
			
			$map['c_type1'] = array('eq',$post['c_type1']);
			
		}
		if(!empty($post['return_id'])){
			
			$map['return_id'] = array('eq',$post['return_id']);
			
		}
		
		$result = M('mymessage')->where($map)->save(array('is_view'=>1,'update_time'=>time()));
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'查看成功'));
		
	}
	
	//会员收支明细
	public function userWater(){
		
		$post = $this->param;
	
		$string = '';
		if(!empty($post['date'])){
			$timestamp = strtotime( $post['date']);
			$start_time = date( 'Y-m-1 00:00:00', $timestamp );
			$mdays = date( 't', $timestamp );
			$end_time = date( 'Y-m-' . $mdays . ' 23:59:59', $timestamp );
			$string = 'create_time>='.strtotime($start_time).' and create_time <='.strtotime($end_time).' and';
		}
		
		$Dao = M();
		$sql = "SELECT FROM_UNIXTIME(create_time,'%Y-%m') as months FROM bhy_flowater WHERE ".$string." uid = ".$this->uid." and user_type = ".($this->type)." GROUP BY months desc";
		
	//	$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'查看成功','sql'=>$sql));
		
		$res = $Dao ->query($sql);
		
		foreach($res as $k => $v){
			
			$timestamp = strtotime( $v['months']);
			$start_time = date( 'Y-m-1 00:00:00', $timestamp );
			$mdays = date( 't', $timestamp );
			$end_time = date( 'Y-m-' . $mdays . ' 23:59:59', $timestamp );
			
			$map = array();
			$map['create_time'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)));
			$map['user_type'] = array('eq',$this->type);
			$map['uid'] = array('eq',$this->uid);
			$sonList = M('flowater')->where($map)->order('create_time desc')->select();
			$sql = M('flowater')->getLastSql();
			//$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'查看成功','res'=>$res));
			$income = 0;$expenditure = 0;
			foreach($sonList as $ks => $vs){
				
				$sonList[$ks]['create_time'] = date('Y-m-d H:i',$vs['create_time']);
				$sonList[$ks]['water_title'] = waterStatus($vs['water_type'],$vs['type']);
				if($vs['type'] == 1){
					$income += $vs['new_price'];
				}else{
					$expenditure += $vs['new_price'];
				}
			}
			$nowDate = date('Y-m');
			
			if($nowDate == $v['months']){
				$res[$k]['months'] = '本月';
			}
			$res[$k]['income'] = $income;
			$res[$k]['expenditure'] = $expenditure;
			$res[$k]['son_list'] = $sonList;

		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>empty($res)?'':$res,'sql'=>$sql,'uid'=>$this->uid));
		
	}
	
	//流水详情
	public function waterDetail(){
		
		$post = $this->param;
		$water = M('flowater')->where('id='.$post['id'])->find();
		$water['water_title'] = waterStatus($water['water_type'],$water['type']);
		$water['create_time'] = date('Y-m-d H:i',$water['create_time']);
		
		$order = M('order')->where('id='.$water['return_id'])->find();
		$water['order_number'] = $order['orderno'];
		$water['ordertype'] = ordertype($order['type']-1);
		if($this->type == 1){
			if(!empty($order['sid'])){
				$water['user']=sinfo($order['sid']);
			}else{
				$water['user']= '';
			}
		}else{
			if($order['utype'] == 2){
				$water['user']= '';
			}else{
				$water['user']=uinfo($order['uid']);
			}
		}
		if($water['water_type'] == 3){
			$water['user']= '';
		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$water));
		
		
	}
	
	//未到账详情
	public function wdzDetail(){
		
		$post = $this->param;
		$order = M('order')->where('id='.$post['id'])->find();
		$order['water_title'] = ordertype($order[type]-1);
		$order['create_time'] = date('Y-m-d H:i',empty($order['update_time'])?$order['create_time']:$order['update_time']);

		$order['user']=uinfo($order['uid']);
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$order));
		
	}
	
	//开关
	public function userSwitch(){
		
		$post = $this->param;
		if($this->type == 1){
			$order = M('order')->where('uid='.$this->uid.' and status=1')->find();
		}else{
			$order = M('order')->where('sid='.$this->uid.' and status=1')->find();
		}
		
		$lock = 3;
		if(empty($order)){
			$lock = 2;
		}
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('lock'=>$lock)));
		
	}
	
	//消息列表
	public function messageList(){
		
		$post = $this->param;
		$map['user_type'] = array('eq',$this->type);
		$map['user_id'] = array('eq',$this->uid);
		
		$list = M('mymessage')->where($map)->order('add_time desc')->limit(($post['page']-1)*10,10)->select();
		$sql = M('mymessage')->getLastSql();
		foreach($list as $k => $v){
			
			if($v[user_type]==1){
				$user=uinfo($v[user_id]);
				$list[$k][phone]=$user[phone];
				$list[$k][icon]=$user[icon];
			}else{
				$user=sinfo($v[uid]);
				$list[$k][phone]=$user[nickname];
				$list[$k][icon]=$user[icon];
			}
			
			$list[$k]['time'] = date('Y-m-d H:i',$v['add_time']);
			
		}
		
		M('mymessage')->where($map)->save(array('is_view'=>1,'update_time'=>time()));
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$list,'sql'=>$sql));
		
	}
	/*咨询回复*/
	public function answer(){
        $post = $this->param;
		
		$fask = M('fask')->find($post['id']);
		
		$answer = M('fanswer')->where('tid='.$fask['id'].' and pid=0')->find();
		
		$add['pid'] = $answer['id'];
        $add[tid]=$post[id];
        $add[content]=$post[content];
		$add['user_type'] = $this->type;
        $add['sid']=$answer['sid'];
		$add['uid']=$this->uid;
        $add[create_time]=time();
        $result = M('fanswer')->add($add);
		
		M('fask')->where('id='.$post['id'])->save(array('update_time'=>time()));

		
        if($result){
            $this->appReturn(array('status'=>true,'code'=>200,'msg'=>'回答成功','data'=>$result));
        }else{
            $this->appReturn(array('status'=>false,'code'=>201,'msg'=>'回答失败','data'=>$result));
        }
    }
	
	/*回复列表*/
	public function answer_list(){
		
		$post = $this->param;
		$ans_list = M('fanswer')->where('tid='.$post['id'])->order('create_time asc')->select();
		$sql = M('fanswer')->getLastSql();
		foreach($ans_list as $k => $v){
			
			if($v['user_type'] == 1){
				$user = uinfo($v['uid']);
			}else{
				$user = sinfo($v['sid']);
			}
			$ans_list[$k]['nickname'] = empty($user['nickname'])?$user['phone']:$user['nickname'];
			if(empty($user['icon'])){
				$ans_list[$k]['user_path'] = '';
			}else{
				$ans_list[$k]['user_path'] = is_numeric($user['icon'])?picture($user['icon']):$user['icon'];
			}
			
			$ans_list[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$ans_list,'sql'=>$sql));
		
	}
	
	/*咨询消息列表*/
	public function zx_list(){
		
		$post = $this->param;
		
		$map['id'] = array();
		$ans_list = M('fanswer')->where('user_type=1 and sid='.$this->uid)->order('create_time desc')->group('uid')->select();
		
		foreach($ans_list as $k => $v){
			
			$user = uinfo($v['uid']);
			$ans_list[$k]['nickname'] = empty($user['nickname'])?$user['phone']:$user['nickname'];
			if(empty($user['icon'])){
				$ans_list[$k]['user_path'] = '';
			}else{
				$ans_list[$k]['user_path'] = is_numeric($user['icon'])?picture($user['icon']):$user['icon'];
			}
			$ans_list[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
			
		}
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$ans_list));
		
	}
	
	/*律师咨询大于等于10的*/
	public function service_list(){
		
		$service = M('feept')->where('id>=10')->select();
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>$service));
		
	}
	
    
}