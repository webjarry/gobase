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
class RedController extends AdminController {
	
	public function _initialize(){
		
		//查询所有广告分类
		$type['pid'] = array('eq',7);
		$type['status'] = array('eq',1);
		$this->assign("typelist",M('category')->where($type)->order('sort asc')->select());
		parent::_initialize();
	}
    public function _filter(&$sqlWhere){

        if(!empty($_GET['date1'])){
            $sqlWhere['create_time']=array('gt',strtotime($_GET['date1']));
        }
        if(!empty($_GET['date2'])){
            $sqlWhere['create_time']=array('lt',strtotime($_GET['date2']));
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $sqlWhere['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
        }


        return $sqlWhere;

    }
    /*public function index(){
        if(!empty($_GET['state'])){
            $this->assign('state',$_GET['state']);
        }

        if(!empty($_GET['state'])&&$_GET['state']==1){
            $map['state']=array('eq',$_GET['state']);
        }elseif(!empty($_GET['state'])&&$_GET['state']==2){
            $map['state']=array('not in','0,1,3');
        }elseif(!empty($_GET['state'])&&$_GET['state']==3){
            $map['state']=array('not in','0,1,2');
        }else{
            $map['state']=array('eq',1);
        }
        if(!empty($_GET['date1'])){
            $map['create_time']=array('gt',strtotime($_GET['date1']));
        }
        if(!empty($_GET['date2'])){
            $map['create_time']=array('lt',strtotime($_GET['date2']));
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
        }
        if(!empty($_GET['name'])){
            $map['name']=array('like','%'.$_GET['name'].'%');
        }
        $list   = $this->lists('room',$map);
        int_to_string($list);
        $this->assign('_list', $list);

        $d=M('room')->where('state=1')->count();
        $this->assign('d',$d);
        $c=M('room')->where('state=2')->count();
        $this->assign('c',$c);
        $s=M('room')->where('state=3')->count();
        $this->assign('s',$s);
        $this->display();
    }*/
    public function state(){
        $id['id']=array('eq',$_GET['id']);
        $save['state']=$_GET['state'];
        $article_subgrade=M('room')->where($id)->save($save);
        if($article_subgrade){
            $this->success('更新成功!',U('index'));
        }else{
            $this->error('更新失败');
        }
    }

	public function goodstate(){
		$id=$_GET['id'];
		$recommend = M('room')->where("id=".$id)->find()['status'];
		$tj=$recommend==1?0:1;
		$re=M('room')->where("id=".$id)->save(array('status'=>$tj));
		if($re){
            $this->success('修改成功','/Admin/Room/index.html');
		}else{
			$this->error('修改失败');
		}
	}


}
