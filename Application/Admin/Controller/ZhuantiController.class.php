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
class ZhuantiController extends AdminController {
	
	public function _initialize(){
        
	    $this->assign("typelist",M('ajcate')->where("status=1 and pid >0")->order('id asc')->select());

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
        if(!empty($_GET['typeid'])){
            $map['typeid']=array('eq',$_GET['typeid']);
        }


        if(!empty($_GET['name'])){
            $map['name']=array('like','%'.$_GET['name'].'%');
        }

        //dump($map);
        $listsCount = M('zhuanti')->where($map)->count();
        $Page       = new \Think\Page($listsCount,10);
        $show       = $Page->show();

        $list = M('zhuanti')->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('zhuanti')->getLastSql();
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


            $result =D('zhuanti')->add($_POST);
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
            $result =M('zhuanti')->where("id=".$id)->save($_POST);
            if($result){

                $this->success('编辑成功！',U('index'));

            }else{
                $this->error('编辑失败！',U('index'));
            }
        }else{
            $id = I('request.id');
            $map[id] = $id;
            //$map[pid] = array('eq',0);
            $editdata = D('zhuanti')->where($map)->find();




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


	


}
