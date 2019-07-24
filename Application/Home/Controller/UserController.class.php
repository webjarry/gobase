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
class UserController extends HomeController {
	
	public function _initialize() {
		
		
	
		$user = session('user');
		
		if(empty($user) || $user['type'] != 1){
			
			//$this -> redirect('/Home/Public/login_account');
			
		}else{
            S('usertype'.$user['id'].'-1','user');
        }
		
        
		parent::_initialize();
	}
    public function login(){
        session('usertype','user');
        S('usertype','user');
        $this->assign('usertype',session('usertype'));
        $this->display();
    }
    public function login_account(){
        session('usertype','user');
        S('usertype','user');
        $this->assign('usertype',session('usertype'));
		
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			
			$this -> assign('is_weixin',1);
			
		}
        $this->display();
    }
    public function register(){
        session('usertype','user');
        S('usertype','user');
        $this->assign('usertype',session('usertype'));
        $this->display();
    }
    public function askchat(){$this->display();}
	public function chatlist(){$this->display();}
	public function chatroom(){$this->display();}
	public function zhangdan(){$this->display();}
    public function coupon_center_use(){$this->display();}
    public function bidding_ing(){$this->display();}
    public function chakan(){$this->display();}
    public function crowd_funding_donation(){$this->display();}
    public function viewMessage(){$this->display();}
    public function crowd_funding_prove(){$this->display();}
    public function crowd_funding_movely(){$this->display();}
    public function crowd_funding_contribution(){$this->display();}
    public function fxdetail(){$this->display();}
    public function gzdetail(){$this->display();}
    public function crowd_funding_friends2(){
		
		
		
		header("Content-type:text/html;charset=utf-8");
		
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			
			$user = session('user');
			
			//var_dump($user);die();
			$user_type = session('user_type');
			if(empty($user) || $user_type != 1){
				
				$this -> getopenid();
				
			}
			
		
		}

		
		$this->display();
		
	}
	public function personal_txjl(){$this->display();}
    public function checkfile(){$this->display();}
    public function analysisreport(){$this->display();}
    public function apply_Crowd_funding(){$this->display();}
    public function apply_intellectualProperty(){$this->display();}
    public function apply_mutualAid(){$this->display();}
    public function ask_details(){$this->display();}
	public function zhuanfa_dynamic(){$this->display();}
    public function bidding(){$this->display();}
	public function bidding_all(){$this->display();}
    public function bidding_closed(){$this->display();}
    public function bidding_completed(){$this->display();}
    public function bidding_payment(){$this->display();}
    public function break_the_law(){$this->display();}
    public function buy_contract(){$this->display();}
    public function case_entrusted(){$this->display();}
    public function case_entrusted_details(){$this->display();}
    public function case_entrusted_details_originator(){$this->display();}
    public function chuan(){$this->display();}
   
    public function consult_details(){$this->display();}
    public function consult_details_other(){$this->display();}
    public function consult_picture(){$this->display();}
    public function contract(){
        if(!empty($_GET['key'])){
            $this->assign('key',$_GET[key]);
        }
        $this->display();}
    public function contract_details(){$this->display();}
    public function cost_calc (){$this->display();}
    public function cost_calc_lawyer(){$this->display();}
    public function cost_calc_litigation(){$this->display();}
    public function coupon_center(){$this->display();}
    public function coupon_center_mine(){$this->display();}
    public function crowd_funding(){$this->display();}
    public function crowd_funding_learnMutual(){$this->display();}
    public function crowd_funding_learnMutual_pay(){$this->display();}
    public function crowd_funding_learnMutual2(){$this->display();}
    public function crowd_funding_list(){$this->display();}
    public function donation(){$this->display();}
    public function donation_detail(){$this->display();}
    public function electronic_vouchers(){$this->display();}
    public function evaluate(){$this->display();}
    public function fabulous(){$this->display();}
    public function familyhelp_add(){$this->display();}
    public function find(){$this->display();}
    public function find_lawyers(){$this->display();}
    public function find_nearby_lawyers(){$this->display();}
    public function find_preservation(){$this->display();}
    public function find_preservation_apply(){$this->display();}
    public function find_ranking(){$this->display();}
    public function forward(){$this->display();}
    public function free_consultation(){$this->display();}
    public function gywm(){$this->display();}
    public function index(){
        $this->assign('aaa',session('user'));
        $this->display();}
    public function kdetail(){$this->display();}
    public function law_special(){$this->display();}
    public function law_special_details(){
        $this->assign('name',$_GET[name]);
        $this->assign('catename',$_GET[catename]);
        $this->display();}
    public function lawyer_alldynamic(){$this->display();}
    public function lawyer_details(){$this->display();}
    public function lawyer_dynamic_details(){$this->display();}
    public function lawyers(){$this->display();}
    public function lawyers_list(){$this->display();}
    public function legal_notice(){$this->display();}
    public function legal_notice_form(){$this->display();}
    public function legal_notice_form2(){$this->display();}
    public function legal_notice_form2_pay(){$this->display();}
    public function legal_pay(){$this->display();}
    
    public function mind_pay(){$this->display();}
    public function msg_detail(){$this->display();}
    public function mutual_recharge(){$this->display();}
    public function mutualAid(){$this->display();}
    public function mutualAid_apply_history(){$this->display();}
    public function mutualAid_date_details(){$this->display();}
    public function mutualAid_details_bid(){$this->display();}
    public function mutualAid_history(){$this->display();}
    public function mutualAid_notice(){$this->display();}
    public function mutualAid_personal_details(){$this->display();}
    public function news(){$this->display();}
    public function news_info(){$this->display();}
    public function news_list(){
        $this->assign('catename',$_GET['catename']);
        $this->display();}
    public function notice(){$this->display();}
    public function order_pay(){$this->display();}
    public function order_service(){
		$this -> assign('get',$_GET);
		$this->display();
	}
    public function pay_bond(){$this->display();}
    public function pay_service_cost(){
		$this -> assign('get',$_GET);
		$this->display();
	}
    public function personal(){$this->display();}
    public function personal_collect_article(){$this->display();}
    public function personal_collect_lawyer(){$this->display();}
    public function personal_distribution(){$this->display();}
    public function personal_distribution_invite(){$this->display();}
    public function personal_distribution_profit(){$this->display();}
    public function personal_info(){$this->display();}
    public function personal_mind(){
		
		$this->display();
	}
	public function personal_mind0(){$this->display();}
	public function personal_mind_switch(){$this->display();}
    public function personal_money_cash(){$this->display();}
    public function personal_money_recharge(){$this->display();}
    public function personal_myAsk(){$this->display();}
    public function personal_myContract(){$this->display();}
    public function personal_myCrowd_funding(){$this->display();}
	
	
	
	public function personal_myCrowd_funding_jk(){
		
		header("Content-type:text/html;charset=utf-8");
		
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			
			$user = session('user');
			
			//var_dump($user);die();
			$user_type = session('user_type');
			if(empty($user) || $user_type != 1){
				
				$this -> getopenid();
				
			}
			
			
			
			$wxconfig = wx_share_init();
			$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

			//var_dump($wxconfig);die();

			$zc=M('zc')->find($_GET[id]);
			$news = array(
				"title"=>$zc['name'],
				"brief"=>$zc['content'],
				"picture"=>C('WEB_SITE_URL'). expic($zc['icon'])[0],
				//"link"=> 'http://www.test2.test/home/public/register?id='.$_GET[id].'&fenxiang=1'
				"link"=> $url
			);
			//var_dump($news);die();

			$this->assign('news',$news);
		
		}

        $this->assign('wxconfig',$wxconfig);
		
		$this->display();
	}
    public function personal_myCrowd_funding_details(){
        
        header("Content-type:text/html;charset=utf-8");

        $wxconfig = wx_share_init();
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

        //var_dump($wxconfig);die();

        $zc=M('zc')->find($_GET[id]);
        $news = array(
            "title"=>$zc['name'],
            "brief"=>$zc['content'],
            "picture"=>C('WEB_SITE_URL'). expic($zc['icon'])[0],
            "link"=> 'http://www.test2.test/home/user/crowd_funding_friends2?id='.$_GET[id].'&fenxiang=1'
        );
        //var_dump($news);die();

        $this->assign('news',$news);

        $this->assign('wxconfig',$wxconfig);

        $this->display();
    }
    public function personal_myEntrust(){$this->display();}
    public function personal_myMutualAid(){$this->display();}
    public function personal_myOrder(){$this->display();}
    public function personal_myOrder_afterSale(){$this->display();}
    public function personal_myOrder_all(){$this->display();}
    public function personal_myOrder_closed(){$this->display();}
    public function personal_myOrder_evaluate_edit(){$this->display();}
    public function personal_myOrder_evaluated(){$this->display();}
    public function personal_myOrder_finished(){$this->display();}
    public function personal_myOrder_finished_afterSale(){$this->display();}
    public function personal_myOrder_missed(){$this->display();}
    public function personal_myOrder_qiye(){$this->display();}
    public function personal_myOrder_received(){$this->display();}
    public function personal_myOrder_refund(){$this->display();}
    public function personal_myOrder_reward(){$this->display();}
    public function personal_myOrder_waiting(){$this->display();}
    public function personal_notificationLetter(){$this->display();}
    public function personal_serviceOrder(){$this->display();}
    public function personal_setting(){$this->display();}
    public function personal_setting_news_system(){$this->display();}
    public function personal_setting_news_user(){$this->display();}
    public function personal_setting_question(){$this->display();}
    public function personal_setting_question_details(){$this->display();}
    public function personal_setting_suggestion(){$this->display();}
    public function property_service(){$this->display();}
    public function property_service_apply(){$this->display();}
    
    public function release_dynamic(){$this->display();}
    public function release_entrusted(){$this->display();}
    public function search_contract(){$this->display();}
    public function search_contractOrLawyer(){$this->display();}
    public function search_details(){$this->display();}
    public function search_lawyer(){$this->display();}
    public function search_lawyer_details(){
        if(!empty($_GET['key'])){
            $this->assign('key',$_GET['key']);
        }

        $this->display();}
    public function service_package(){$this->display();}
    public function service_package_pay(){$this->display();}
    public function szzfmm(){$this->display();}
    public function want_consult(){$this->display();}
    public function want_consult_release(){
		if(IS_POST){
			$list = I('post.');
			session('consultation',$list);
		}else{
			$this->display();
		}
	}
    public function want_consultation_pay(){
		
		$consultation = session('consultation');
		if(IS_POST){
			
			/*if(empty($consultation)){
				$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'未完善信息'));
			}
			
			$user = session('user');
			
			$post = I('post.');		
			$order['orderno'] = orderNumber();
			$order['type'] = 1;
			$order['uid'] = $user['id'];
			$order['utype'] = 1;
			$order['num'] = 1;
			$order['price'] = C('WEB_LTATION_SITE');
			$order['pay_price'] = C('WEB_LTATION_SITE');
			$order['pay_type'] = $post['pay_type'];
			$order['create_time'] = time();
			$order['date_time'] = date('Y-m');

			$order_id = M('order')->add($order);
			
			if(!$order_id){
				$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单生成失败！'));
			}
			
			if($consultation['lasttime']){
				$consultation['lasttime']=strtotime(substr($consultation['lasttime'],0,10));
			}
			
			$consultation['order_id'] = $order_id;
			$consultation['uid']=$user['id'];
			$consultation['utype']=1;
			$consultation['create_time']=time();
			$consultation['price'] = $order['pay_price'];
			$consultation['pay_type'] = $post['pay_type'];
			$consultation['is_pay'] = 0;
			
			
			
			$fask_id = M('fask')->add($consultation);
			if(!$fask_id){
				
				$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单生成失败！'));
				
			}
			
			$this->appReturn(array('status'=>true,'code'=>200,'msg'=>'订单生成成功！','data'=>array('orderno'=>$order['orderno'])));*/
			
			
		}else{
			
			/*if(empty($consultation)){
				$this -> redirect('/Home/user/want_consult_release');
			}*/
			
			$this->display();
		}
		
	}

    public function yiyi(){$this->display();}
    public function yepay(){$this->display();}
    
    public function detail(){
        $infoarr = session('WX_USERINFO');

        if(empty($infoarr)){

            $appid='wx4a0343cbdd37e797';
            $appsecret='3f75d26abdad48b4877fb7b3c696530c';

            $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $return_url =urlencode($return_url);

            if(!isset($_GET['code'])){
                $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$return_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
                header("Location:".$url);exit;

            }else {
                $code = $_GET['code'];
                $state = $_GET['state'];

                /*根据code获取用户openid*/
                $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";

                $abs = file_get_contents($url);
                $obj = json_decode($abs, true);

                $access_token = $obj['access_token'];
                $openid = $obj['openid'];

                session('WX_OPEN_ID', $openid);//将openid存入session

                //获取信息
                $abs_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
                $abs_url_data = file_get_contents($abs_url);
                $infoarr = json_decode($abs_url_data, true);

                //var_dump($infoarr);

                session('WX_USERINFO', $infoarr);
            }

        }
        //	var_dump($infoarr);die();

        $user= M('usermember')->where('wx_openid="'.$infoarr['unionid'].'"')->find();
        if($user){
            $this->assign('uid',$user[id]);
        }else{
            $this->assign('uid',0);
        }

        $this->assign('openid',$infoarr['unionid']);

        $wxconfig = wx_share_init();
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

        //var_dump($wxconfig);die();

        $zc=M('zc')->find($_GET[id]);
        $news = array(
            "title"=>$zc['name'],
            "brief"=>$zc['content'],
            "picture"=>C('WEB_SITE_URL'). expic($zc['icon'])[0],
            "link"=> 'http://www.test2.test/home/public/register?id='+$_GET[id]
        );

        //var_dump($news);die();

        $this->assign('news',$news);

        $this->assign('wxconfig',$wxconfig);


        $this->display();

    }
    public function wx_info(){
        $appid='wx5e3800f385f36cfa';
        $appsecret='2b67ebf4033940bf8af68c14a43abbf7';

        $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $return_url =urlencode($return_url);

        if(!isset($_GET['code'])){


            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$return_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

            header("Location:".$url);exit;

        }else{

            $code = $_GET['code'];
            $state = $_GET['state'];

            /*根据code获取用户openid*/
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";

            $abs = file_get_contents($url);
            $obj=json_decode($abs,true);

            $access_token = $obj['access_token'];
            $openid = $obj['openid'];

            session('WX_OPEN_ID',$openid);//将openid存入session


            //获取信息
            $abs_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
            $abs_url_data = file_get_contents($abs_url);
            $infoarr=json_decode($abs_url_data,true);

            session('WX_USERINFO',$infoarr);
            return $infoarr;


            $my['open_id|openb_id'] = array('eq',$openid);
            $my['status']  = array('eq',1);
            if($openid=='' || empty($openid)){

                $this->getWxInfo();exit;

            }else{

                $rety = M('usermember')->where($my)->find();
                if(!empty($rety)){
                    if($rety['usertype']==2){
                        if($rety['check_status']==1){
                            session('music_users_id',$rety['id']);
                        }
                    }else{
                        session('music_users_id',$rety['id']);
                    }
                }else{

                    $this->redirect('public/login');exit;
                }
            }
        }
    }


}