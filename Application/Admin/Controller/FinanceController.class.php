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
class FinanceController extends AdminController {
	
	public function _initialize(){

		parent::_initialize();
	}

	public function index(){
		
		$get = I('get.');
		
		$map = array();
		if(!empty($get['title'])){
			$map['orderno'] = array('like','%'.$get['title'].'%');
		}
		if(!empty($get['name'])){
            $use['nickname|phone']=array('like','%'.$get['name'].'%');
			$useList = M('usermember')->where($use)->field('id')->select();
			
			$map['uid'] = array('in',implode(',',array_column($useList,'id')));
        }
		 if(!empty($get['staffname'])){
            $use['nickname|phone']=array('like','%'.$get['staffname'].'%');
			$useList = M('staff')->where($use)->field('id')->select();
			
			$map['sid'] = array('in',implode(',',array_column($useList,'id')));
        }
		if(isset($get['status'])){
			
			if($get['status'] == 1){
				
				$map['status']=array('in','1,3');
				
			}else{
				
				$map['status']=array('eq',$get['status']);
			}
			
            
        }
		if(!empty($get['date1'])){
            $map['create_time']=array('gt',strtotime($get['date1'].' 00:00:00'));
        }
        if(!empty($get['date2'])){
            $map['create_time']=array('lt',strtotime($get['date2'].' 23:59:59'));
        }
        if(!empty($get['date1']) && !empty($get['date2'])){
            $map['create_time']=array(array('gt',strtotime($get['date1'].' 00:00:00')),array('lt',strtotime($get['date2'].' 23:59:59')));
        }
		if(!empty($get['pay_type'])){
			$map['pay_type'] = array('eq',$get['pay_type']);
		}
		if(!empty($get['type'])){
			$map['type'] = array('eq',$get['type']);
		}
		
		//$map['status'] = array('neq',0);
        $listsCount = M('order')->where($map)->count();
        $Page       = new \Think\Page($listsCount,20);
        $show       = $Page->show();

        $list = M('order')->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$qb_total = M('order')->where($map)->field('sum(pay_price) as total_price')->find();
		
		
		//echo M('order')->getLastSql();
		
		$map['status'] = array('in','1,3');
		
		$total = M('order')->where($map)->field('orderno,type,pay_price')->select();
		$wt_price = 0;
		foreach($total as $k => $v){
			
			if($v['type'] == 8){
				
				$wtmap['orderno'] = array('eq',$v['orderno']);
				
				$wt = M('wt')->where($wtmap)->find();
				
				if($wt['payed'] == 1 && $wt['twopay'] == 0){
					$wt_price = $wt_price + ($v['pay_price']-50);
				}
				
			}
			
		}
		
		$total_prices = array_sum(array_column($total,'pay_price')) - $wt_price;
		
		$map['status'] = array('eq',5);
		
		$total_num = M('order')->where($map)->field('pay_price')->select();
		
		$this -> assign('qb_total',$qb_total);
		
		$this -> assign('total',$total_prices);
		
		$this -> assign('total_num',empty($total_num)?0:count($total_num));
		
		$this -> assign('refund_price',empty($total_num)?0:array_sum(array_column($total_num,'pay_price')));
		

        $this->assign('get',$_GET);
        $this->assign('_page',$show);
        foreach($list as $k=>$v){

			$user=M('usermember')->where('id='.$v[uid])->find();
			$list[$k][name]=$user[nickname];
			
			if($v['type']>=2 && $v['type']<=7){
				$map = array();$map['orderno'] = array('eq',$v['orderno']);
				$ask = M('ask')->where($map)->find();
				$list[$k]['phone'] = $ask['phone'];
			}elseif($v['type'] == 8){
				$map = array();$map['orderno'] = array('eq',$v['orderno']);
				$wt = M('wt')->where($map)->find();
				$list[$k]['phone'] = $wt['mobile'];
			}elseif($v['type'] == 16){
				$map = array();$map['orderno'] = array('eq',$v['orderno']);
				$legalpaper = M('legalpaper')->where($map)->find();
				$list[$k]['phone'] = $legalpaper['phone'];
			}elseif($v['type'] == 21){
				$map = array();$map['orderno'] = array('eq',$v['orderno']);
				$zc = M('zc')->where($map)->find();
				$list[$k]['phone'] = $zc['mobile'];
			}
			
			$staff=M('staff')->where('id='.$v['sid'])->find();
			$list[$k]['staff'] = empty($staff[nickname])?$staff[phone]:$staff[nickname];
	
        }
        int_to_string($list);
        $this->assign('list', $list);
		
		
		
		$this -> display();
		
	}

}
