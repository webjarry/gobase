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
class HetongController extends AdminController {
	
	public function _initialize(){  

		
		$this -> assign('mid',is_login());  //当前登陆用户ID
		$this->assign('lists',M('category')->where('pid=114  and status=1')->order('id asc')->select());
		
		parent::_initialize();
	}

    public function index(){
	   
		$type = I('request.type');
		$title = I('request.name');
		if($type>0){
			$where['type'] =array('eq',$type);
		}
		if($title!=''){
			$where['name'] = array('like','%'.$title.'%');
		}

		$lists = M('hetong')->where($where)->order('id desc')->select();
		$this->assign('list',$lists);
		$this->display();
    }
	
	//编辑
	public function edit(){
		$id = I('request.id');
		if(IS_POST){
			$Hetong = D('Hetong');
		    if($Hetong->create()){
				$_POST[flag] = implode(',',$_POST[flag]);
				$resu = M('hetong')->save($_POST);
				if($resu){
					$this->success('修改成功！',U('Hetong/index'));
				}else{
					$this->error('修改失败！');
				}
			}else{
				
				$this->error($Hetong->getError());
			}	
			
			
		}else{
			$vo = M('hetong')->find($id);
			$this->assign('vo',$vo);

			$map['id'] = array('in',$vo['file']);
			$this -> assign('fileList',M('file')->where($map)->select());


			$this->display();
		}
		
	}
	
	//新增
	public function add(){
		if(IS_POST){
			$hetong = D('Hetong');
		    if($hetong->create()){
				$_POST[flag] = implode(',',$_POST[flag]);
				$resu = M('hetong')->add($_POST);
				if($resu){
					$this->success('新增成功！',U('Hetong/index'));
				}else{
					$this->error('新增失败！');
				}
			}else{
				
				$this->error($hetong->getError());
			}	
			
			
		}else{
			
			$this->display();
		}
		
	}
	
	

     //删除
	public function del(){
		$id = I('request.id');

		if($id>0){
		  $lists = M('hetong')->where('id='.$id)->save(array('status'=>-1));
		  if($lists){
			  $this->success('删除成功');
		  }
		}else{

				$this->error('参数错误');


		}

		$this->display();

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

		if(!empty($_GET['type'])){

			$sqlWhere['type'] = array(array('eq',$_GET['type']),array('like','%'.$_GET['type'].',%'), 'or');

		}
		
		return $sqlWhere;
		
	}


}
