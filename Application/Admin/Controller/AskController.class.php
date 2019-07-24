<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class AskController extends AdminController {
	
	public function _initialize(){

		parent::_initialize();
	}

    public function index(){
        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if(!empty($_GET['status'])&&$_GET['status']==1){
            $map['status']=array('eq',0);
        }elseif(!empty($_GET['status'])&&$_GET['status']==2){
            $map['status']=array('eq',1);
  
        }elseif(!empty($_GET['status'])&&$_GET['status']==3){
            $map['status']=array('eq',2);
        }elseif(!empty($_GET['status'])&&$_GET['status']==4){
            $map['status']=array('eq',2);
			$map['sover']=array('eq',1);
			$map['uover']=array('eq',0);
        }elseif(!empty($_GET['status'])&&$_GET['status']==5){
            $map['status']=array('eq',4);
        }elseif(!empty($_GET['status'])&&$_GET['status']==6){
            $map['status']=array('eq',7);
        }elseif(!empty($_GET['status'])&&$_GET['status']==7){
            $map['status']=array('eq',5);
        }elseif(!empty($_GET['status'])&&$_GET['status']==8){
            $map['status']=array('eq',6);
			$map['after']=array('eq',1);
        }elseif(!empty($_GET['status'])&&$_GET['status']==9){
            $map['status']=array('eq',9);
        }
        if(!empty($_GET['date1'])){
            $map['create_time']=array('gt',strtotime($_GET['date1']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date2'])){
            $map['create_time']=array('lt',strtotime($_GET['date2']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['name'])){
            $use['nickname|phone']=array('like','%'.$_GET['name'].'%');
			$useList = M('usermember')->where($use)->field('id')->select();
			
			$map['uid'] = array('in',implode(',',array_column($useList,'id')));
        }
		 if(!empty($_GET['staffname'])){
            $use['nickname|phone']=array('like','%'.$_GET['staffname'].'%');
			$useList = M('staff')->where($use)->field('id')->select();
			
			$map['sid'] = array('in',implode(',',array_column($useList,'id')));
        }
        $listsCount = M('ask')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('ask')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('ask')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){

            $user=M('usermember')->where('id='.$v[uid])->find();
            $list[$k][name]=$user[nickname];
            $list[$k][phone]=empty($v['phone'])?$user[phone]:$v['phone'];

            $staff=M('staff')->where('id='.$v['sid'])->find();
            $list[$k][staff]=$staff[nickname];

            $list[$k][ordertype]= ordertype($v['type']);
			
			//订单是否到账
			$ord = array();$ord['orderno'] = array('eq',$v['orderno']);
			$order = M('order')->where($ord)->field('is_transfer')->find();
			
			$list[$k]['is_transfer'] = empty($order['is_transfer'])?0:$order['is_transfer'];
			
			

        }
        int_to_string($list);
        $this->assign('list', $list);
      
        $this->display();
    }
    //回复
    public function edit(){
        if(IS_POST){
            $pid = I('request.tid');

            $_POST[type] = 'discuz';
            $_POST[uid] = 999999999;
            $_POST[create_time] = time();

            $f[tid]=$pid;
            $f[type]='discuz';
            if(M('comment')->where($f)->count()){
                $floor=M('comment')->where($f)->order('id desc')->find()['floor'];
                $_POST[floor] = $floor+1;
            }else{
                $_POST[floor] = 1;
            }
            $result = D('comment')->add($_POST);
            if($result){
                $mdrt = M('discuz')->where('id='.$pid)->save(array('ok'=>1));
                if($mdrt){
                    $this->success('回复成功！',U('index'));
                }
            }else{
                $this->error('回复失败！',U('index'));
            }
        }else{
            $id = I('request.id');
            $map[id] = $id;
            $result = M('ask')->where($map)->find();

            /*if(strpos($result[icon],',')){
                $color=explode(',',$result[icon]);
            }else{
                $color=array($result[icon]);
            }
            $result[icon]=$color;
            $result[color]=M('color')->where("id=".$result[color])->find()[name];
            $result[goodsname]=M('goods')->where("id=".$result[goodsid])->find()[name];*/
        
            $result[name]=modelField($result[uid],'usermember','nickname');
            //$result[reason]=M('order')->where("orderno=".$result[orderno])->find()[reason];
            $this->assign('vo',$result);
         
            $this->display();
        }



    }

    //删除
    public function del(){
        $id = I('request.id');
        if(is_array($id)){
            $where[id] = array('in',implode(',',$id));

        }else{
            $where[id] = array('eq',$id);

        }
        $delResult = M('ask')->where($where)->delete();

        if($delResult){
            $this->success('删除成功！',U('index'));
        }
    }
    public function state(){
        $ask=M('ask')->find($_GET['id']);
		$after_remark = empty($_GET['remark'])?'':$_GET['remark'];

        $result=M('ask')->where("id=".$_GET['id'])->save(array('after'=>$_GET['status'],'after_remark'=>$after_remark));
        if($result){

            if($_GET['status'] ==2 ){
                $money=$ask[money];
                
                M('ask')->where("id=".$_GET['id'])->save(array('after'=>2,'status'=>9));
				
				$map['orderno'] = array('eq',$ask['orderno']);
				M('order')->where($map)->save(array('is_confirm'=>0,'status'=>2,'update_time'=>time()));
				
				$order = M('order')->where($map)->find();
				
                $user=M('usermember')->find($ask['uid']);
                $staff=M('staff')->find($ask['sid']);

				
				$water = array(
					'uid'=>$ask['uid'],
					'type'=>1,
					'user_type'=>1,
					'water_type'=>20,//售后退款
					'new_price'=> $order['pay_price'],
					'create_time'=>time(),
					'remark'=>'售后退款',
					'return_id'=>$order['id'],
					'user_money'=>$user['balance']+$order['pay_price']
				);
				$res3 = M('flowater')->add($water);
				
                M('usermember')->where("id=".$ask['uid'])->save(array('balance'=>$user[balance]+$money));
                //M('staff')->where("id=".$ask['sid'])->save(array('balance'=>$staff[balance]-$money));
				//添加消息
				$ad = array(
					'user_id' =>$ask['uid'],
					'user_type'=>1,
					'message'=>"您申请的售后订单已处理成功！",
					'add_time'=>time(),
					'type'=>4,  //售后处理成功
					'c_type1'=>1,  //咨询订单
					'return_id'=>$_GET['id']
				);

				$res1 = M("mymessage")->add($ad);
				

            }else if($_GET['status'] ==3 ){
				
				$map['orderno'] = array('eq',$ask[orderno]);
				
				$order = M('order')->where($map)->find();
				
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
				
				$arr = M('ask')->where($map)->save(array('uover'=>1,'sover'=>1,'status'=>4,'state'=>3,'is_confirm'=>1));
				$res1 = M('order')->where($map)->save(array('is_confirm'=>1,'is_transfer'=>1,'update_time'=>time()));
				
				$res2 = M('staff')->where('id='.$order['sid'])->save(array('balance'=>$water['user_money']));
				$res3 = M('flowater')->add($water);
				
				
				//添加消息
				$ad = array(
					'user_id' =>$ask['uid'],
					'user_type'=>1,
					'message'=>"您申请的售后订单已被驳回！",
					'add_time'=>time(),
					'type'=>5,  //驳回售后
					'c_type1'=>1,  //咨询订单
					'return_id'=>$_GET['id']
				);

				$res2 = M("mymessage")->add($ad);
            }
            $this->success('更新成功!',U('index'));
        }else{
            $this->error('更新失败');
        }
    }

	public function goodstate(){
		$id=$_GET['id'];
		$recommend = M('discuz')->where("id=".$id)->find()['status'];
		$tj=$recommend==1?0:1;
		$re=M('discuz')->where("id=".$id)->save(array('status'=>$tj));
		if($re){
            $this->success('修改成功','/Admin/discuz/index.html');
		}else{
			$this->error('修改失败');
		}
	}


}
