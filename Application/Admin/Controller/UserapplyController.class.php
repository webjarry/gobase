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
class UserapplyController extends AdminController {
	
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

        //dump($map);
        $listsCount = M('userapply')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('userapply')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('fxcontent')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){

        }
        int_to_string($list);
        $this->assign('list', $list);

        $d=M('userapply')->where('status=1')->count();
        $this->assign('d',$d);
        $c=M('userapply')->where('status=2')->count();
        $this->assign('c',$c);
        $s=M('userapply')->where('status=3 ')->count();
        $this->assign('s',$s);
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
            //$map[pid] = array('eq',0);
            $editdata = M('userapply')->where($map)->find();
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
        $re=M('userapply')->find($_GET['id']);

        $result=M('userapply')->where("id=".$_GET['id'])->save(array('status'=>$_GET['status']));
        if($result){

            if($_GET['status'] ==2 ){
                $user= M('userapply')->where("id=".$_GET['id'])->find();
                $add[nickname]=$user[phone];
                $add[username]=$user[phone];
                $add[phone]=$user[phone];
                $add[password]="123456";
                $add[addr]=$user[addr];
                $add[email]=$user[email];
                $add[create_time]=time();
                
                M('usermember')->add($add);

            }
            $this->success('更新成功!',U('index'));
        }else{
            $this->error('更新失败');
        }
    }

	public function goodstate(){
		$id=$_GET['id'];
		$tj=M('discuz')->where("id=".$id)->find()['status']==1? 0:1;

		if(M('discuz')->where("id=".$id)->save(array('status'=>$tj))){
            $this->success('修改成功','/Admin/discuz/index.html');
		}else{
			$this->error('修改失败');
		}
	}


}
