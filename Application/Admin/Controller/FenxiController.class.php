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
class FenxiController extends AdminController {
	
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
            $sqlWhere['create_create_time'] = array('gt',$date1);
        }
        if(!empty($_GET['date2'])){
            $date2=strtotime($_GET['date2']);
            $sqlWhere['create_create_time'] = array('lt',$date2);
        }

        return $sqlWhere;*/

    }
    public function index(){
        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if($_GET['status']==1){
            $map['status']=array('eq',1);
        }elseif($_GET['status']==2){
            $map['status']=array('eq',2);
        }elseif($_GET['status']==3){
            $map['status']=array('eq',3);
        }else{
            $map['status']=array('eq',1);
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
            $map['name']=array('like','%'.$_GET['name'].'%');
        }
        //$map['payed']=array('eq',1);
        //dump($map);
        $listsCount = M('fenxi')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('fenxi')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach($list as $k => $v){
			
			$map['type'] = array('eq',18);
			$map['orderno'] = array('eq',$v['orderno']);
			$map['status'] = array('eq',1);
			$order = M('order')->where($map)->find();
			
			$list[$k]['pay_price'] = $order['pay_price'] == 0?'VIP购买':$order['pay_price'];
			
		}
		
//echo M('fenxi')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){


        }
        int_to_string($list);
        $this->assign('list', $list);

        $d=M('fenxi')->where('status=1')->count();
        $this->assign('d',$d);
        $c=M('fenxi')->where('status=2')->count();
        $this->assign('c',$c);
        $s=M('fenxi')->where('status=3')->count();
        $this->assign('s',$s);
      
        $this->display();
    }
    //回复
    public function edit(){
        if(IS_POST){
            $pid = I('request.tid');



        }else{
            $id = I('request.id');
            $map[id] = $id;
            $result = M('fenxi')->where($map)->find();

            if(strpos($result[icon],',')){
                $color=explode(',',$result[icon]);
            }else{
                $color=array($result[icon]);
            }
            $result[icon]=$color;
            if(strpos($result[file],',')){
                $color=explode(',',$result[file]);
            }else{
                $color=array($result[file]);
            }
            $result[file]=$color;

            
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
        $delResult = M('fenxi')->where($where)->delete();

        if($delResult){
            $this->success('删除成功！',U('index'));
        }
    }
    public function state(){
        $a=M('fenxi')->find($_GET['id']);

        $result=M('fenxi')->where("id=".$_GET['id'])->save(array('status'=>$_GET['status']));
        
        if($_GET['status']==3){
            $order=M('order')->where("orderno=".$a['orderno'])->find();
            $user=M('usermember')->where("id=".$order['uid'])->find();
            M('usermember')->where("id=".$order['uid'])->save(array('balance'=>$user[balance]+$order[pay_price]));
        }
        if($result){
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
