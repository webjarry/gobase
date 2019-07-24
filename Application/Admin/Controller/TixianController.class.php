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
class TixianController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		$type['pid'] = array('eq',7);
		$type['status'] = array('eq',1);
		$this->assign("typelist",M('category')->where($type)->order('sort asc')->select());
		parent::_initialize();
	}
    public function _filter(&$sqlWhere){

        /*if(!empty($_GET['name'])){

            $sqlWhere['name'] = array('like','%' . $_GET['name'] . '%');
        }
        if(!empty($_GET['id'])){

            $sqlWhere['id'] = array('like','%' . $_GET['id'] . '%');
        }
        if(!empty($_GET['date1'])){
            $date1=strtotime($_GET['date1']);
            $sqlWhere['create_time'] = array('gt',$date1);
        }
        if(!empty($_GET['date2'])){
            $date2=strtotime($_GET['date2']);
            $sqlWhere['create_time'] = array('lt',$date2);
        }

        return $sqlWhere;*/

    }
    public function index1(){
        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if(!empty($_GET['status'])&&$_GET['status']==1){
            $map['status']=array('eq',$_GET['status']);
        }elseif(!empty($_GET['status'])&&$_GET['status']==2){
            $map['status']=array('not in','0,1,3');
        }elseif(!empty($_GET['status'])&&$_GET['status']==3){
            $map['status']=array('not in','0,1,2');
        }else{
            $map['status']=array('eq',1);
        }
        if(!empty($_GET['date1'])){
            $map['time']=array('gt',strtotime($_GET['date1']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date2'])){
            $map['time']=array('lt',strtotime($_GET['date2']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['name'])){
            $map['name|orderno']=array('eq',$_GET['name']);
        }
        $map['actiontype']=array('eq',2);


        $prefix   = C('DB_PREFIX');
        $l_table  = $prefix.('tixian');
        $r_table  = $prefix.('usermember');

        $model    = M()->table( $l_table.' o' )->join ( $r_table.' u ON u.id=uid' );
        //$_REQUEST = array();
        $list = $this->lists($model,$map,'id desc',null,'u.name,u.balance,u.bank,*');
        int_to_string($list);
        $this->assign('_list', $list);

        $d=M('order')->where('status=1 and actiontype=2')->count();
        $this->assign('d',$d);
        $c=M('order')->where('status=2 and actiontype=2')->count();
        $this->assign('c',$c);
        $s=M('order')->where('status=3 and actiontype=2')->count();
        $this->assign('s',$s);
        $this->display();
    }
    public function index(){
        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if(!empty($_GET['status'])&&$_GET['status']==1){
            $map['status']=array('eq',$_GET['status']);
        }elseif(!empty($_GET['status'])&&$_GET['status']==2){
            $map['status']=array('not in','0,1,3');
        }elseif(!empty($_GET['status'])&&$_GET['status']==3){
            $map['status']=array('not in','0,1,2');
        }else{
            $map['status']=array('eq',1);
        }
        if(!empty($_GET['date1'])){
            $map['time']=array('gt',strtotime($_GET['date1']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date2'])){
            $map['time']=array('lt',strtotime($_GET['date2']));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
            if(!empty($_GET['status'])){
                $map['status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['name'])){
            $map['name']=array('eq',$_GET['name']);
        }
       
        $model    = M('tixian');
        //$_REQUEST = array();
        $list = $this->lists($model,$map,'id desc');
        int_to_string($list);
        foreach($list as $k=>$v){
            if($v[utype]==1){
                $m=M('usermember');
                $list[$k][usertype]='普通用户';
            }elseif($v[utype]==2){
                $m=M('staff');
                $list[$k][usertype]='律师';
            }
            $user=$m->find($v[uid]);

      
            $list[$k][bank]=$user[bank];
        }
        $this->assign('_list', $list);

        $d=M('tixian')->where('status=1')->count();
        $this->assign('d',$d);
        $c=M('tixian')->where('status=2')->count();
        $this->assign('c',$c);
        $s=M('tixian')->where('status=3')->count();
        $this->assign('s',$s);
        $this->display();
    }
    //回复
    public function edit(){
        if(IS_POST){
            $pid = I('request.tid');

            $_POST[type] = 'discuz';
            $_POST[uid] = 999999999;
            $_POST[time] = time();

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
            //$map[pid] = array('eq',0);
            $editdata = D('discuz')->where($map)->find();
            $this->assign('vo',$editdata);
            $this->display();
        }



    }

    //删除
    public function del(){
        $id = I('request.id');
        if(is_array($id)){
            $where[id] = array('in',implode(',',$id));
            $map[pid] = array('in',implode(',',$id));
        }else{
            $where[id] = array('eq',$id);
            $map[pid] = array('eq',$id);
        }
        $delResult = M('order')->where($where)->delete();
        $delResults = M('discuz')->where($map)->delete();
        if($delResult){
            $this->success('删除成功！',U('index'));
        }
    }
    public function state(){
        $id['id']=array('eq',$_GET['id']);
        $save['status']=$_GET['status'];
        if($_GET['status'] ==3){
            $save['reason']=$_GET['reason'];
        }
        $article_subgrade=M('tixian')->where($id)->save($save);
        if($article_subgrade){
            $order=M('tixian')->where("id=".$_GET['id'])->find();
            if($_GET['status'] ==2 ){
                if ($order[utype] == 1) {
                    $m=M('usermember');
                } elseif ($order[utype] == 2) {
                    $m=M('staff');
                }
               // $m->where("id=".$order['uid'])->setDec('balance',$order['money']);
			   
			   //添加消息
				$message = array(
					'user_id' =>$order['uid'],
					'user_type'=>$order[utype],
					'message'=>"您发起的提现已打款成功",
					'add_time'=>time(),
					'type'=>1,  //提现成功
					'c_type1'=>4,  //咨询订单
					'return_id'=>$order['id']
				);

                $w['orderno']=orderNumber();
                $w['type']=13;
                $w['uid']=$order['uid'];
                $w['utype']=$order['utype'];

                $w['pay_price']=$order['money'];
                $w['create_time']=time();
                $w['date_time']=date('Y-m');
                $w['status']=1;
                $addwater=M('order')->add($w);
				M('mymessage')->add($message);

                //AliyunUsertxsuc($order[phone],'SMS_150571220',$order[name],date('Y-m-d h:i',$order[time]),$order['money'],$order['orderno'],$bankno);

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
