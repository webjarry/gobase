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
class AdviceController extends AdminController {
	

	public function index(){
        //未审核数
		$MessagesCounts = M('advice')->where('status=1')->count();
		$this->assign('MessagesCounts',$MessagesCounts);

		//分页
		$listsCount = M('Advice')->count();
		$Page       = new \Think\Page($listsCount,15);
		$show       = $Page->show();
		
		$lists = M('advice')->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($lists as $k=>$v ){
			if($v[utype]==1){

				$lists[$k][nickname]=M('usermember')->find($v[uid])[phone];
				$lists[$k][phone]=M('usermember')->find($v[uid])[phone];
			}else{
				$lists[$k][nickname]=M('staff')->find($v[uid])[nickname];
				$lists[$k][phone]=M('staff')->find($v[uid])[phone];
			}

		}
		$this->assign('list',$lists);
		$this->assign('get',$_GET);
		$this->assign('_page',$show);
		$this->display();

	}


	//回复
	public function edit(){
            if(IS_POST){
				$pid = I('request.pid');
				$_POST[create_time] = time();
				//$result = D('advice')->add($_POST);

					$mdrt = M('advice')->where('id='.$pid)->save(array('status'=>2,'back'=>$_POST['back']));
					if($mdrt){
						$this->success('回复成功！',U('index'));
					}else{
						$this->error('回复失败！',U('index'));
					}

			}else{
				$id = I('request.id');
				$map[id] = $id;
				//$map[pid] = array('eq',0);
				$result = M('advice')->where($map)->find();

				if(strpos($result[icon],',')){
					$color=explode(',',$result[icon]);
				}else{
					$color=array($result[icon]);
				}
				$result[icon]=$color;
				$result[name]=modelField($result[uid],'usermember','nickname');
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

		}
		$delResult = M('advice')->where($where)->delete();

		if($delResult){
			$this->success('删除成功！',U('index'));
		}
	}


	
	

	public function _filter(&$sqlWhere){
		
		if(!empty($_GET['mid'])){
			//查找改用户对应的id
			$tempwhr['nickname']=array('like','%'.$_GET['mid'].'%');
			$member=M('member')->where($tempwhr)->select();
			$temp='';
			foreach ($member as $k=>$v ){
				if($k==0){
					
					$temp=$v['uid'];
					
				}else{
					
					$temp.=','.$v['uid'];
					
				}
			}
			
			$sqlWhere['mid'] = array('in',$temp);
			
		}	
		if(!empty($_GET['title'])){
			
			$sqlWhere['title'] = array('like','%' . $_GET['title'] . '%');
		}	
		
		return $sqlWhere;
		
	}
	public function unlimitforlevel($cate,$html='------',$pid=0,$level=0,$topid=0){
		//$cate  查询出来的数据
		//$html   标识符
		//$pid    上级ID
		//$level   层级
		$arr = array();  //定义返回的数组
		foreach($cate as $v){
			if($v['pid'] == $pid){  //等于顶级分离
				$v['topid'] = 0;
				if($level == 0){
					$topid = $v['id'];
				}
				if($level != 0){
					$v['topid'] = $topid;
				}
	
				$v['level'] = $level + 1;     //层级加1
				if($level > 1){
					$html = str_replace('|' , '' , $html);
				}
	
				$v['html'] = str_repeat($html,$level);   //str_repeat  字符串重复次数
				//var_dump($v['id']);
				$lis = M('column')->where('pid=' . $v['id'])->select();
				if($lis){
					$v['num'] = 1;
				}else{
					$v['num'] = 0;
				}
				$arr[]=$v;
				//array_merge  把多个数组合并为一个数组
				$arr = array_merge($arr,$this->unlimitforlevel($cate,$html,$v['id'],$level+1,$topid));
	
			}
		}
		return $arr;
	
	}
	//新闻回收站
	
	public function recycle(){
		
		$map['status']  =   -1;
		
		$list = $this->lists(M('article'),$map,'update_time desc');	
		
		$this -> assign('list',$list);
		$this -> display();
		
	}
	
	public function _before_add(){
		$_POST['mid']=UID;
		$_POST['create_time']=strtotime($_POST['create_time']);
		
	}
	public function _before_edit(){
		$_POST['create_time']=strtotime($_POST['create_time']);
		
		$_POST['mid']=$_POST['mid'];
		
		
	}

}
