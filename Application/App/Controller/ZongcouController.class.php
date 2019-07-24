<?php

namespace App\Controller;

class ZongcouController extends HomeController {

    //public $param;

    public function _initialize() {

        parent::_initialize();

//        $list = file_get_contents('php://input');
//        $param = json_decode($list, true);
//        $this->param = $param;
         // $this->checkIndividual();
        //$this->checkUid($param);
    }

    //人员关系列表
    public function person_type(){
        $result = D('Zongcou')->person_type();
        $this->appReturn($result);
    }

    //添加证明
    public function add_msg(){
        $post = $this->param;
        $result = D('Zongcou')->add_msg($post);
        $this->appReturn($result);
    }

    //众筹详情
    public function details(){
        $post = $this->param;
        $result = D('Zongcou')->details($post);
        $this->appReturn($result);
    }

    //证明留言列表
    public function msg_list(){
        $post = $this->param;
        $result = D('Zongcou')->msg_list($post);
        $this->appReturn($result);
    }

    //爱心帮助记录列表
    public function order_list(){
        $post = $this->param;
        $result = D('Zongcou')->order_list($post);
        $this->appReturn($result);
    }

    //支付页面信息
    public function pay_page(){
        $post = $this->param;
        $result = D('Zongcou')->pay_page($post);
        $this->appReturn($result);
    }

    //分享记录接口
    public function add_share(){
        $post = $this->param;
        $result = D('Zongcou')->add_share($post);
        $this->appReturn($result);
    }


    //提交支付
    public function add_order(){
        $post = $this->param;
        $result = D('Zongcou')->add_order($post);
        $this->appReturn($result);
    }


	//众筹提现记录
	public function txList(){
		
		$post = $this->param;
		$result = D('Zongcou')->txList($post,$this->uid);
        $this->appReturn($result);
		
	}
	//众筹说明
	public function zcInfo(){
		
		$this -> appReturn(array('code'=>200,'msg'=>'数据获取成功','data'=>array('sqyq'=>C('WEB_ZC_SQYQ'),'wcn'=>C('WEB_ZC_WCN'))));
		
	}



}
