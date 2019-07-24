<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class LawyerController extends HomeController {
	
	public function _initialize() {
	
		$user = session('user');
		$act_name = ACTION_NAME;
		
		
		$actNameList = array('login','login_account','register','workbench','news','news_list','news_info','find','find_chargeAnalysis','find_cost','find_cost_lawyer','find_cost_litigation','find_preservation','find_preservation_apply','find_ranking');
		
		if(!in_array($act_name,$actNameList)){
			
			if(empty($user) || $user['type'] != 2){
			
				$this -> redirect('/Home/lawyer/login');
				
			}
			
			S('usertype'.$user['id'].'-2','user');
			
		}
		
		
        
		parent::_initialize();
	}
	public function askchat(){$this->display();}
	public function chatlist(){$this->display();}
	public function chatroom(){$this->display();}
	
	public function zhangdan(){$this->display();}
	public function personal_mind_switch(){$this->display();}
	public function personal_money_budget0(){$this->display();}
    public function chakan(){$this->display();}
    public function viewmessage(){$this->display();}
    public function chuan(){$this->display();}
    public function checkfile(){$this->display();}
    public function login(){
        S('usertype','lawyer');

        if(!empty(session('password')) || !empty(session('password')) ){
            $this->assign('phone',session('phone'));
            $this->assign('password',session('password'));
        }
        $this->display();}
    public function login_account(){
        S('usertype','lawyer');

        if(!empty(session('password')) || !empty(session('password')) ){
            $this->assign('phone',session('phone'));
            $this->assign('password',session('password'));
        }
        $this->display();}
    public function register(){
        S('usertype','lawyer');

        $this->display();
    }
	
	public function zhuanfa_dynamic(){$this->display();}
    public function addarticle(){$this->display();}
    public function addpl(){$this->display();}
    public function advice(){$this->display();}
    public function anyoucate(){$this->display();}
    public function anyoudetail(){$this->display();}
    public function anyoulist(){$this->display();}
    public function askorder(){$this->display();}
    public function bid_dynamic(){$this->display();}
    public function bid_dynamic_details(){$this->display();}
    public function buy_contract(){$this->display();}
    public function contract(){$this->display();}
    public function contract_details(){$this->display();}
    public function faskanswer(){$this->display();}
    public function fask(){$this->display();}
    public function faskdetail(){$this->display();}
    public function find(){$this->display();}
    public function find_chargeAnalysis(){$this->display();}
    public function find_cost(){$this->display();}
    public function find_cost_lawyer(){$this->display();}
    public function find_cost_litigation(){$this->display();}
    public function find_preservation(){$this->display();}
    public function find_preservation_apply(){$this->display();}
    public function find_ranking(){$this->display();}
    public function gywm(){$this->display();}
    public function lawyer_details(){$this->display();}
    public function lawyer_dynamic_details(){$this->display();}
    public function lawyerquan(){$this->display();}
    public function msg_detail(){$this->display();}
    public function mutualAid_details_bid(){$this->display();}
    public function myAnswer(){$this->display();}
    public function mycollection(){$this->display();}
    public function mypl(){$this->display();}
    public function news(){$this->display();}
    public function news_info(){$this->display();}
    public function news_list(){
        $this->assign('catename',$_GET['catename']);
        $this->display();}
    public function personal(){$this->display();}
    public function personal_distribution(){$this->display();}
    public function personal_distribution_invite(){$this->display();}
    public function personal_distribution_profit(){$this->display();}
    public function personal_info(){$this->display();}
    public function personal_info_bankcard(){$this->display();}
    public function personal_mind(){$this->display();}
    public function personal_money(){$this->display();}
    public function personal_money_budget(){$this->display();}
    public function personal_money_cash(){$this->display();}
    public function personal_money_income(){$this->display();}
    public function personal_money_recharge(){$this->display();}
    public function personal_myContract(){$this->display();}
    public function personal_myLawyer_cooperation(){$this->display();}
    public function personal_myOrder(){$this->display();}
    public function personal_setting(){$this->display();}
    public function personal_setting_news_system(){$this->display();}
    public function personal_setting_news_user(){$this->display();}
    public function personal_user_talk(){$this->display();}
    public function quedetail(){$this->display();}
    public function question(){$this->display();}
    public function release_contract(){$this->display();}
    public function release_cooperation(){$this->display();}
    public function service_package(){$this->display();}
    public function service_package_pay(){$this->display();}
    
    public function szzfmm(){$this->display();}
    public function wdz(){$this->display();}
    public function workbench(){$this->display();}
    public function workbench_chargeSet(){$this->display();}
    public function workbench_couponSet(){$this->display();}
    public function workbench_picture_finished(){$this->display();}
    public function workbench_picture_finishing(){$this->display();}
    public function workbench_picture_newsOrder(){$this->display();}
    public function wt(){$this->display();}
    public function wtdetail(){$this->display();}
    public function wtmy(){$this->display();}
    public function wtmyjoin(){$this->display();}
    public function yhxy(){$this->display();}
    public function yepay(){$this->display();}
    public function zuimdetail(){$this->display();}
    public function zuimlist(){$this->display();}

    public function erweim(){
        header("Content-type: text/html; charset=utf-8");

        $path    = 'Uploads/qrcode/'.date('Ymd').'/';
        $imgname = date('YmdHis');
        $url     = C('WEB_SITE_URL').'/home/lawyer/register.html';

        return $this->qrcode($url,$path,$imgname);

        $imgpath = '/'.$path.$imgname.'.png';

        return $imgpath;
    }
    public function qrcode($data,$path,$imgname,$level=3,$size=4){

        Vendor('Phpqrcode.phpqrcode');

        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 15;

        if(!file_exists($path))
        {
            mkdir($path, 0777);
        }
        //  生成的文件名
        $fileName = $path.$imgname.'.png';
        //ob_end_clean();//清空缓冲区
        $object = new \QRcode();
        $object->png($data,$fileName,$level, $size);

    }

}