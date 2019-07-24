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
			
		}
		
		$data['id'] = $wt_info['id'];
		
		if(M('wt')->save($data)){
			
			if(!empty($post['progress_result'])){
				
				$map['orderno'] = array('eq',$wt_info['orderno']);
				$re = M('wt')->where($map)->save(array('status'=>4));
				
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
		if($wt_info['status']!=4 || empty($wt_info['progress_result'])){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单状态异常'));
			
		}
		$map['orderno'] = array('eq',$wt_info['orderno']);
		$result = M('order')->where($map)->save(array('is_confirm'=>1,'update_time'=>time()));
		
		if($result){
			
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
		
		if(M('tw_info')->add($data)){
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
		
		$askNum = M('ask')->where('status<=4 and uid='.$this->uid)->count();
		$map['type'] = array('in','16,17,18');
		$map['status'] = array('neq',1);
		$map['uid'] = array('eq',$this->uid);
		$qyNum = M('order')->where($map)->count();
		
		$wtNum = M('wt')->where('status<4 and uid='.$this->uid)->count();
		
		$map = array();
		$map['f.uid'] = array('eq',$this->uid);
		
		$faskNum = M('fask as f')->where($map)->count();
		
		$sql = M('fask as f')->getLastSql();
		
		$zcNum = M('zc')->where('uid='.$this->uid)->count();
		
		$famNum = M('family')->where('uid='.$this->uid)->count();
		
		$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'数据获取成功','data'=>array('askNum'=>empty($askNum)?0:$askNum,'qyNum'=>empty($qyNum)?0:$qyNum,'wtNum'=>empty($wtNum)?0:$wtNum,'faskNum'=>empty($faskNum)?0:$faskNum,'zcNum'=>empty($zcNum)?0:$zcNum,'famNum'=>empty($famNum)?0:$famNum),'sql'=>$sql));
		
	}
    
}