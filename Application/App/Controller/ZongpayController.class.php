<?php

namespace App\Controller;
use Think\Model;

class ZongpayController extends \Think\Controller {


    /*************************************************   支付宝支付  *******************************************/
    //唤起支付宝
    public function alipay(){
        header("Content-type:text/html;charset=utf-8");
        $where['order_number'] = array('eq',$_GET['order_number']);
        $where['pay_status']   = array('eq',0);
        $order=M('zongcou_order')->where($where)->find();
        if(empty($order)){
            echo '订单不存在！';exit;
        }
         $money=$order['pay_price']; //正式金额
        //$money = '0.01'; //测试金额，需要改成正式
        if(ismobile()){
            $pagepay = new \Org\Net\zfbwap\pagepay\Pagepay();
        }else{
            $pagepay = new \Org\Net\Alipay\pagepay\Pagepay();
        }
        $param = array(
            'WIDout_trade_no'=> $order['order_number'],
            'WIDsubject' => '法援宝众筹订单',
            'WIDtotal_amount' => $money,
            'WIDbody' => '众筹捐款'.$money.'元'
        );
        //异步通知地址
        $data['notify_url'] = "http://www.test2.test/App/Zongpay/alipay_notify_url";
		//同步跳转
		$data['return_url'] = "http://www.test2.test/App/Zongpay/alipay_return_url?zc_id=".$order['zc_id'];
        $results = $pagepay->alipay($param,$data);
        exit(var_dump($results));
    }

    //支付宝异步通知
    public function alipay_notify_url(){
        $post = $_POST;

        if($post['auth_app_id'] == '2018080760954378' && $post['trade_status'] == 'TRADE_SUCCESS'){
            $orderno=$post['out_trade_no'];
            $map['order_number'] = array('eq',$orderno);
            $res=M('zongcou_order')->where($map)->save(array('pay_status'=>1,'pay_time'=>time(),'pay_type'=>1));
			
			$map = array();$map['orderno'] = array('eq',$orderno);
			$res1 = M('order')->where($map)->save(array('pay_status'=>1,'status'=>1,'update_time'=>time()));
			
			
			
            if($res && $res1){
                echo "success";
            }else{
                echo "failure";
            }
        }
    }

    //支付宝同步跳转
    public function alipay_return_url(){
		$get = $_REQUEST;

        header('Location:http://www.test2.test/home/user/personal_myCrowd_funding_details.html?id='.$get['zc_id']);
    }


    /*************************************************   微信支付  *******************************************/

    //唤起微信支付
    public function weixinpay(){
        $where['order_number'] = array('eq',$_GET['order_number']);
        $where['pay_status']   = array('eq',0);
        $order=M('zongcou_order')->where($where)->find();
        if(empty($order)){
            echo '订单不存在！';exit;
        }
        $notify_url = 'http://www.test2.test/App/Zongpay/wxnotify';
        header("Content-type:text/html;charset=utf-8");
        $arr=array(
            'body'=> '法援宝-下单',
            //'total_fee'=>1, //支付金额，单位分
            'total_fee'=>$order['pay_price']*100, //支付金额，单位分
            'out_trade_no'=>$order['order_number'],//订单号
            'product_id'=>str_shuffle(time()),
            'notify_url'=>$notify_url
        );
		//var_dump($arr);
        require_once 'wxpayb/example/Jsapi.php';
        $weixinpay = new \Jsapi();
        $jsApiparamter = $weixinpay->tel_pay($arr);
        $this->assign('jsApiparamter', $jsApiparamter);
        $this->assign('orderinfo',$order);
        $this->display();
    }


    //非微信浏览器
    public function wxh5pay(){
        $where['order_number'] = array('eq',$_GET['order_number']);
        $where['pay_status']   = array('eq',0);
        $order=M('zongcou_order')->where($where)->find();
        if(empty($order)){
            echo '订单不存在！';exit;
        }
		$money=$order['pay_price']*100;//正式金额
        //$money= 1;//测试金额

        $userip = get_client_ip(); //获得用户设备IP 自己网上百度去
        $appid = "wx46fbc7e73f15a373";//微信给的
        $mch_id = "1512985241";//微信官方的
        $key = "SHANGHAISHANGHAISHANGHAISHANGHAI";//自己设置的微信商家key

        $rand = rand(00000,99999);
        $out_trade_no = $order['order_number'];//平台内部订单号
        $nonce_str=MD5($out_trade_no);//随机字符串
        $body = "众筹订单支付";//内容
        $total_fee = $money; //金额
        $spbill_create_ip = $userip; //IP
        $notify_url = "http://www.test2.test/App/Zongpay/wxnotify"; //回调地址
        $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://www.baidu.com","wap_name":"支付"}}';//场景信息 必要参数
        $signA ="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        $strSignTmp = $signA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        $post_data = "<xml>
                        <appid>$appid</appid>
                        <body>$body</body>
                        <mch_id>$mch_id</mch_id>
                        <nonce_str>$nonce_str</nonce_str>
                        <notify_url>$notify_url</notify_url>
                        <out_trade_no>$out_trade_no</out_trade_no>
                        <scene_info>$scene_info</scene_info>
                        <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                        <total_fee>$total_fee</total_fee>
                        <trade_type>$trade_type</trade_type>
                        <sign>$sign</sign>
                    </xml>";
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
        $dataxml = D('App/Zongpay')->http_post($url,$post_data); //后台POST微信传参地址  同时取得微信返回的参数    POST 方法我写下面了

        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        file_put_contents('d1234.txt',$objectxml);
        $this -> assign('objectxml',$objectxml);
        $this -> assign('money',$order['pay_price']);
        $this -> assign('order',$order);
        $this -> display();
    }

    //微信异步通知
    public function wxnotify(){
        $postdata = file_get_contents("php://input");
        $postObj = simplexml_load_string( $postdata, 'SimpleXMLElement', LIBXML_NOCDATA );
        file_put_contents('d123.txt',$postdata);
        $orderno = $postObj->out_trade_no;
        if($postObj->result_code=='SUCCESS'){
            $orderno=str_replace('001fyb','',$orderno);
            $map['order_number'] = array('eq',$orderno);
			
			$zcOrder = M('zongcou_order')->where($map)->find();
			
            $res=M('zongcou_order')->where($map)->save(array('pay_status'=>1,'pay_time'=>time(),'pay_type'=>2));
			$map = array();$map['orderno'] = array('eq',$orderno);
			$res1 = M('order')->where($map)->save(array('pay_status'=>1,'status'=>1,'update_time'=>time()));
			
			$zcInfo = M('zc')->find($zcOrder['zc_id']);
			
			//添加消息
			$ad = array(
				'user_id' =>$zcInfo[uid],
				'user_type'=>1,
				'message'=>"您发布的众筹已有人捐款",
				'add_time'=>time(),
				'type'=>2,  //众筹捐款
				'c_type1'=>2,  //众筹
				'return_id'=>$zcOrder['zc_id']
			);

			$res2 = M("mymessage")->add($ad);
			
			file_put_contents('txt1.txt',M('order')->getLastSql());
            if($res && $res1){
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                return sprintf('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    //订单是否合法,是否已经支付完成
    public function checkorder(){
        if(IS_POST){
            $list=I('post.');
            $orderno=$list['orderno'];
            $order=M('order')->where('order_number='.$orderno)->find();
            if($order){
                if($order['pay_status']==1){
                    exit(json_encode(array('code'=>200)));
                }else{
                    exit(json_encode(array('code'=>201)));
                }
            }else{
                exit(json_encode(array('code'=>400)));
            }

        }

    }


}
