<?php
/**
*
* example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
* 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
* 请勿直接直接使用样例对外提供服务
* 
**/

require_once dirname(dirname(__FILE__))."/lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';

class Native{

    public function pay($post){

        //初始化日志
        $logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
        $log = Log::Init($logHandler, 15);

        $notify = new NativePay();

        //模式二
        /**
         * 流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $input = new WxPayUnifiedOrder();
        $input->SetBody($post['body']);
       // $input->SetAttach($post['attach']);
        $input->SetOut_trade_no($post['out_trade_no']);
        $input->SetTotal_fee($post['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time()+120));
       // $input->SetGoods_tag($post['goods_tag']);
        $input->SetNotify_url($post['notify_url']);
        $input->SetTrade_type('NATIVE');
        $input->SetProduct_id($post['product_id']);
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        return $url2;
    }

}

?>

