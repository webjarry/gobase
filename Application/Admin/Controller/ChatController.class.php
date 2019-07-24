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
class ChatController extends AdminController {
	
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
        if(!empty($_GET['state'])){
            $this->assign('state',$_GET['state']);
        }
        if(!empty($_GET['state'])){
            $this->assign('state',$_GET['state']);
        }
        if(!empty($_GET['state'])&&$_GET['state']==1){
            $map['p.state']=array('eq',$_GET['state']);
        }elseif(!empty($_GET['state'])&&$_GET['state']==2){
            $map['p.state']=array('eq',$_GET['state']);
        }else{
            $map['p.state']=array('eq',1);
        }
        if(!empty($_GET['date1'])){
            $map['p.create_time']=array('gt',strtotime($_GET['date1']));
            if(!empty($_GET['state'])){
                $map['p.state']=array('eq',$_GET['state']);
            }
        }
        if(!empty($_GET['date2'])){
            $map['p.create_time']=array('lt',strtotime($_GET['date2']));
            if(!empty($_GET['state'])){
                $map['p.state']=array('eq',$_GET['state']);
            }
        }
        if(!empty($_GET['date1']) && !empty($_GET['date2'])){
            $map['p.create_time']=array(array('gt',strtotime($_GET['date1'])),array('lt',strtotime($_GET['date2'])));
            if(!empty($_GET['state'])){
                $map['p.state']=array('eq',$_GET['state']);
            }
        }
        if(!empty($_GET['nickname'])){
            $map['u.nickname']=array('like','%'.$_GET['nickname'].'%');
        }

        //$map['status']=array('eq',5);
        //dump($map);
        $listsCount = M('chat p')->join('bhy_usermember u on p.uid=u.id')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('chat p')->field("u.nickname,u.phone,u.vip,p.*")
            ->join('bhy_usermember u on p.uid=u.id')->where($map)->order('p.create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('order')->getLastSql();

        foreach ($list as $k=>$v){
            if($v[type]==2){
                $list[$k][vip]=2;
                $list[$k][nickname]=modelField($v[uid],'staff','nickname');
            }


        }

        $this->assign('get',$_GET);
        $this->assign('_page',$show);
       
        int_to_string($list);
        $this->assign('list', $list);


        $d=M('chat')->where('state=1')->count();
        $this->assign('d',$d);
        $c=M('chat')->where('state=2')->count();
        $this->assign('c',$c);

        $this->display();
    }
    //回复
    public function edit(){
        if(IS_POST){
            $id = I('request.id');

            $mdrt = M('chat')->where('id='.$id)->save(array('state'=>2,'back'=>$_POST['back']));
            if($mdrt){
                $this->success('回复成功！',U('index'));
            }else{
                $this->error('回复失败！',U('index'));
            }
        }else{
            $id = I('request.id');
            $map[id] = $id;
            $result = M('chat')->where($map)->find();

           if($result[type]==2){
               $result[name]=modelField($result[uid],'staff','nickname');
           }else{
               $result[name]=modelField($result[uid],'usermember','nickname');
           }

           

            $this->assign('vo',$result);
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
        $order=M('order')->find($_GET['id']);

        $result=M('order')->where("id=".$_GET['id'])->save(array('state'=>$_GET['state']));
        if($result){

            if($_GET['state'] ==2 ){
                $goods=M('goods')->find($order['goodsid']);
                M('goods')->where("id=".$order['goodsid'])->save(array('salenum'=>$goods[salenum]-$order[num]));

                

            }
            $this->success('更新成功!',U('index'));
        }else{
            $this->error('更新失败');
        }
    }

	


}
