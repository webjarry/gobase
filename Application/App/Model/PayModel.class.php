<?php

namespace App\Model;
use Think\Model;

/**
 * 用户个人中心模型
 */
class PayModel extends Model {

    public function refund($order) {
	  date_default_timezone_set("Asia/Shanghai");
	  $date = date("YmdHis");
	  if($order['source'] == 1){
		$appid = "wx46fbc7e73f15a373";
		$mch_id = "1512985241";
		$key = "SHANGHAISHANGHAISHANGHAISHANGHAI"; 
		  
	  }else{
		  
		$appid = "wx4dc49bc6ce410719";
		$mch_id = "1522603151";
		$key = "SHANGHAISHANGHAISHANGHAISHANGHAI"; 
		  
	  }
	  
	  if($order['type'] == 8){
		  $map = array();$map['orderno'] = array('eq',$order['orderno']);
		  $wtinfo = M('wt')->where($map)->find();
		  if($wtinfo['payed'] == 1 && $wtinfo['twopay'] == 1){
			  
			  $prifix = '001fyb';
			  
		  }else{
			  
			 $prifix = ''; 
			  
		  }
	  }else{
		  
		  $prifix = '';
	  }
	  
	 // $prifix = $order['type'] == 8?'001fyb':'';
	  
	  $out_trade_no = $order['orderno'].$prifix;
	  $op_user_id = $order['uid'];
	  $out_refund_no = $date;
	  $total_fee = $order['pay_price']*100;
	  $refund_fee = $order['pay_price']*100;
	//  $transaction_id = "4009542001201706206596667604";
	  
	  $useCert = true;
	  $nonce_str = $this -> nonceStr();
	  $ref = strtoupper(md5("appid=$appid&mch_id=$mch_id&nonce_str=$nonce_str&op_user_id=$op_user_id"
			  . "&out_refund_no=$out_refund_no&out_trade_no=$out_trade_no&refund_fee=$refund_fee&total_fee=$total_fee"
			  . "&key=$key")); //sign加密MD5
	  $refund = array(
	  'appid' =>$appid, //应用ID，固定
	  'mch_id' => $mch_id, //商户号，固定
	  'nonce_str' => $nonce_str, //随机字符串
	  'op_user_id' => $op_user_id, //操作员
	  'out_refund_no' => $out_refund_no, //商户内部唯一退款单号
	  'out_trade_no' => $out_trade_no, //商户订单号,pay_sn码 1.1二选一,微信生成的订单号，在支付通知中有返回
	  // 'transaction_id'=>'1',//微信订单号 1.2二选一,商户侧传给微信的订单号
	  'refund_fee' => $refund_fee, //退款金额
	  'total_fee' => $total_fee, //总金额
	  'sign' => $ref//签名
	  );
	  file_put_contents('order.txt',json_encode($refund));
	//  var_dump($refund);
	  $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
	  ; //微信退款地址，post请求
	  $xml = $this -> arrayToXml($refund);
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_HEADER, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); //证书检查
	  if ($useCert == true) {
		 // echo dirname(__FILE__) . '/wxpayb/kfcert/apiclient_cert.pem';
		// 设置证书
		
		if($order['source'] == 1){
			
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
			curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . '/wxpayb/cert/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
			curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . '/wxpayb/cert/apiclient_key.pem');
			
		}else{
			
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
			curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . '/wxpayb/kfcert/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
			curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . '/wxpayb/kfcert/apiclient_key.pem');
			
		}
		
		
	  }
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	  $xml = curl_exec($ch);
	  // 返回结果0的时候能只能表明程序是正常返回不一定说明退款成功而已
	  
	//  var_dump($xml);
	  if ($xml) {
		curl_close($ch);
		// 把xml转化成数组
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	//    var_dump($xmlstring);
		$result['errNum'] = 0;
		$result['info'] = $this -> object_to_array($xmlstring);
	//    var_dump($result);
		return $result;
	  } else {
		$error = curl_errno($ch);
		curl_close($ch);
		// 错误的时候返回错误码。
		$result['errNum'] = $error;
		return $result;
	  }
	}
	
	public function arrayToXml($arr) {
	  $xml = "<root>";
	  foreach ($arr as $key => $val) {
		if (is_array($val)) {
		  $xml .= "<" . $key . ">" . $this->arrayToXml($val) . "</" . $key . ">";
		} else {
		  $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
		}
	  }
	  $xml .= "</root>";
	  return $xml;
	}
	public function object_to_array($obj) {
	  $obj = (array) $obj;
	  foreach ($obj as $k => $v) {
		if (gettype($v) == 'resource') {
		  return;
		}
		if (gettype($v) == 'object' || gettype($v) == 'array') {
		  $obj[$k] = (array) $this -> object_to_array($v);
		}
	  }
	  return $obj;
	}
	public function nonceStr() {
	  $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	  $str = "";
	  $length = 32;
	  for ($i = 0; $i < $length; $i++) {
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	  }
	  // 随机字符串
	  return $str;
	}

}

?>
