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
class MindController extends AdminController {
	
	public function _initialize(){

		parent::_initialize();
	}

    public function index(){

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
            $use['nickname|phone']=array('like','%'.$_GET['name'].'%');
			$useList = M('usermember')->where($use)->field('id')->select();
			
			$map['uid'] = array('in',implode(',',array_column($useList,'id')));
        }
		 if(!empty($_GET['staffname'])){
            $use['nickname|phone']=array('like','%'.$_GET['staffname'].'%');
			$useList = M('staff')->where($use)->field('id')->select();
			
			$map['sid'] = array('in',implode(',',array_column($useList,'id')));
        }
        //$map['status']=array('lt',5);
        //dump($map);
		$map['type'] = array('eq',10);$map['status'] = array('eq',1);
        $listsCount = M('order')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('order')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('wt')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){

			$user=M('usermember')->where('id='.$v[uid])->find();
			$list[$k][name]=$user[nickname];
			$list[$k][phone]=empty($v['mobile'])?$user[phone]:$v['mobile'];
			$staff=M('staff')->where('id='.$v['sid'])->find();
			$list[$k][staff]=empty($staff[nickname])?$staff[phone]:$staff[nickname];
	
        }
        int_to_string($list);
        $this->assign('list', $list);

      
        $this->display();
    }
   
    //删除
    public function del(){
        $id = I('request.id');
        if(is_array($id)){
            $where[id] = array('in',implode(',',$id));

        }else{
            $where[id] = array('eq',$id);

        }
        $delResult = M('order')->where($where)->delete();
		
		//echo M('order')->getLastSql();

        if($delResult){
            $this->success('删除成功！',U('index'));
        }
    }
 

}
