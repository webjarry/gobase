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
class OrderController extends AdminController {
	
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
            $map['status']=array('eq',1);
        }elseif(!empty($_GET['status'])&&$_GET['status']==2){
            $map['status']=array('eq',2);
        }elseif(!empty($_GET['status'])&&$_GET['status']==3){
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
            $map['nickname']=array('like','%'.$_GET['name'].'%');
        }
        //$map['status']=array('lt',5);
        //dump($map);
        $listsCount = M('order')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('order')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//echo M('order')->getLastSql();
        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){
            if($v[type]==1 || $v[type]==0){
                $user=M('usermember')->where('id='.$v[uid])->find();
                $list[$k][name]=$user[nickname];
                $list[$k][phone]=$user[phone];
            }else{
                $user=M('staff')->where('id='.$v[uid])->find();
                $list[$k][name]=$user[nickname];
                $list[$k][phone]=$user[phone];
            }

        }
        int_to_string($list);
        $this->assign('list', $list);

        $d=M('order')->where('status=1')->count();
        $this->assign('d',$d);
        $c=M('order')->where('status=2')->count();
        $this->assign('c',$c);
        $s=M('order')->where('status=3')->count();
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
            $result = M('order')->where($map)->find();

            if(strpos($result[icon],',')){
                $color=explode(',',$result[icon]);
            }else{
                $color=array($result[icon]);
            }
            $result[icon]=$color;
            $result[color]=M('color')->where("id=".$result[color])->find()[name];
            $result[goodsname]=M('goods')->where("id=".$result[goodsid])->find()[name];
            if($result[type]==2){
                $result[name]=modelField($result[uid],'staff','nickname');
            }else{
                $result[name]=modelField($result[uid],'usermember','nickname');
            }

            $goods=M('orderdetail')->where("orderno=".$result[orderno])->select();
            foreach ($goods as $k=>$v) {
                $goods[$k][name]=M("goods")->find($v[goodsid])[name];
            }


            $addr=M('addr')->where("id=".$result[addrid])->find();
            
            $this->assign('vo',$result);
            $this->assign('goods',$goods);
            $this->assign('addr',$addr[city].$addr[addr]);
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
        $delResult = M('order')->where($where)->delete();

        if($delResult){
            $this->success('删除成功！',U('index'));
        }
    }
    public function state(){
        $order=M('order')->find($_GET['id']);

        $result=M('order')->where("id=".$_GET['id'])->save(array('status'=>$_GET['status']));
        if($result){

            if($_GET['status'] ==2 ){

                $goods=M('goods')->find($order['goodsid']);
                M('goods')->where("id=".$order['goodsid'])->save(array('salenum'=>$goods[salenum]+$order[num]));

                if($order['type']==2){
                    $tic=$order['money']*C("WEB_BILI")/100;
                    if(M('order')->where("id=".$_GET['id'])->save(array('tic'=>$tic))){
                        $staff= M('staff')->where("id=".$order['uid'])->find();

                        M('staff')->where("id=".$order['uid'])
                            ->save(array('balance'=>$staff[balance]+$order['money'],'tic'=>$staff[tic]+$tic));
                    }

                }

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
