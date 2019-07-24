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
class NewsController extends AdminController {
	
	public function _initialize(){
        
	    $this->assign("cate",M('category')->where("status=1 and pid =2")->order('id asc')->select());

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
    public function index1(){

        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if(!empty($_GET['cid'])){
            $map['c.cid']=array('eq',$_GET['cid']);
        }
        if(!empty($_GET['bid'])){
            $map['c.bid']=array('eq',$_GET['bid']);
        }
        if(!empty($_GET['sid'])){
            $map['c.sid']=array('eq',$_GET['sid']);
        }
        if(!empty($_GET['date1'])){
            $map['c.create_time']=array('gt',strtotime($_GET['date1']));
            if(!empty($_GET['status'])){
                $map['c.status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date2'])){
            $map['c.create_time']=array('lt',strtotime($_GET['date2']));
            if(!empty($_GET['status'])){
                $map['c.status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['c.create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
            if(!empty($_GET['status'])){
                $map['c.status']=array('eq',$_GET['status']);
            }
        }
        if(!empty($_GET['nickname'])){
            $map['u.nickname']=array('like','%'.$_GET['nickname'].'%');
        }

        //dump($map);
        $listsCount = M('news')->alias('c')->join("bhy_price u on c.uid=u.id")->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('news')->alias('c')->field("u.nickname,u.icon,c.*")
            ->join("bhy_price u on c.uid=u.id")->where($map)
            ->order('c.create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('fxcontent')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){
            $bankcard=M('bankcard')->where('id='.$v[cardid])->find();
            $list[$k][bankname]=M('bank')->where('id='.$bankcard[bankid])->find()[name];
            $list[$k][cardno]=$bankcard[cardno];
        }
        int_to_string($list);
        $this->assign('list', $list);


        $this->display();
    }
    public function index(){
        if(!empty($_GET['status'])){
            $this->assign('status',$_GET['status']);
        }
        if(!empty($_GET['typeid'])){
            $map['typeid']=array('eq',$_GET['typeid']);
        }


        if(!empty($_GET['name'])){
            $map['name']=array('like','%'.$_GET['name'].'%');
        }

        //dump($map);
        $listsCount = M('news')->where($map)->count();
        $Page       = new \Think\Page($listsCount,10);
        $show       = $Page->show();

        $list = M('news')->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('news')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);

        foreach($list as $k=>$v){

        }
        int_to_string($list);
        $this->assign('list', $list);


        $this->display();
    }
    public function add(){
        if(IS_POST){
            $pid = I('request.tid');

            if(is_array(I('request.color'))){
                $a=implode(',',I('request.color'));
                $_POST[color] = $a;
            }
            $_POST[create_time] = time();


            $result =D('news')->add($_POST);
            if($result){
                $this->success('添加成功！',U('index'));
            }else{
                $this->error('添加失败！',U('index'));
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

    public function edit(){

        if(IS_POST){
            $id = I('request.id');
            if(is_array(I('request.color'))){
                $a=implode(',',I('request.color'));
                $_POST[color] = $a;
            }
            $result =M('news')->where("id=".$id)->save($_POST);
            if($result){

                $this->success('编辑成功！',U('index'));

            }else{
                $this->error('编辑失败！',U('index'));
            }
        }else{
            $id = I('request.id');
            $map[id] = $id;
            //$map[pid] = array('eq',0);
            $editdata = D('news')->where($map)->find();

            $c=M('color')->where("status=1")->select();
            foreach($c as $k=>$v){
                if(strpos($editdata[color],',')){
                    $arr=explode(',',$editdata[color]);
                }else{
                    $arr=array($editdata[color]);
                }
           
                if(in_array($v[id],$arr)){
                    $c[$k][choose]=1;
                }else{
                    $c[$k][choose]=0;
                }

            }
            $this->assign('c',$c);
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
        $re=M('tixian')->find($_GET['id']);

        $result=M('tixian')->where("id=".$_GET['id'])->save(array('status'=>$_GET['status']));
        if($result){

            if($_GET['status'] ==2 ){
                $user= M('usermember')->where("id=".$re['uid'])->find();
                M('usermember')->where("id=".$re['uid'])
                    ->save(array('balance'=>$user[balance]- $re['money'],'tixian'=>$user[tixian]+ $re['money']));

                /*$w['orderno']=$order['orderno'];
                $w['uid']=$order['uid'];
                $w['type']=2;
                //$w['payment']=$order['type'];
                $w['income']= 2;
                $w['money']=$order['money'];
                $w['create_time']=time();
                $addwater=M('water')->add($w);*/

            }
            $this->success('更新成功!',U('index'));
        }else{
            $this->error('更新失败');
        }
    }

	
    public function recommend(){
        $id=$_GET['id'];
        $recommend = M('news')->where("id=".$id)->find()['recommend'];
        $tj=$recommend==1?0:1;
        $re=M('news')->where("id=".$id)->save(array('recommend'=>$tj));
        if($re){

            $this->success('修改成功','/Admin/news/index.html');
        }else{
            $this->error('修改失败');
        }
    }
    public function cheap(){
        $id=$_GET['id'];
        $recommend = M('news')->where("id=".$id)->find()['cheap'];
        $tj=$recommend==1?0:1;
        $re=M('news')->where("id=".$id)->save(array('cheap'=>$tj));
        if($re){

            $this->success('修改成功','/Admin/news/index.html');
        }else{
            $this->error('修改失败');
        }
    }
    public function alatsList(){
        if(IS_POST){
            $data['id'] = $_POST['id'];
            $data['imgarr'] = $_POST['pics'];
            $data['update_time'] = time();
            $vo = M('news')->save($data);
            if($vo){
                $this->ajaxReturn('200');
            }else{
                $this->ajaxReturn('201');
            }
        }else{
            $this -> assign('get',$_GET);
            $user = M('news')->find($_GET['id']);
            $this -> assign('user',$user);
            $at['id'] = array('in',$user['imgarr']);
            $at['status'] = array('eq',1);
            $picture = M('picture')->where($at)->select();
            $this -> assign('picture',$picture);
            $this -> display();
        }
    }

}
