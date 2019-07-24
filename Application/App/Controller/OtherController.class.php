<?php

namespace App\Controller;

class OtherController extends HomeController {

    public function _initialize() {
		
		//$this -> checkIndividual();
        parent::_initialize();

    }
	
	public function xcx_pay(){
		
		$post = $this->param;
		
		if(!$post['type']){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
			
		}
		
		$map['orderno'] = array('eq',$post['orderno']);
		$map['status'] = array('eq',0);
		
		$order = M('order')->where($map)->find();
		
		if(!$order){
			
			$this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'订单支付异常','post'=>$post));
			
		}
		
		$notify_url = 'http://www.test2.test/home/pay/wxnotify';
		
		if($post['type'] == 1){
			
			$appid =        "wx10786ed5823a7b38";//如果是公众号 就是公众号的appid
			$body =         '法援宝';
			$mch_id =      	"1512985241";
			$nonce_str =    $this->nonce_str();//随机字符串
			$openid =       $post['openid'];
			
		}else{
			
			$appid =        "wxb41cbfb32d23bf38";//如果是公众号 就是公众号的appid
			$body =         '法援宝';
			$mch_id =      	"1512985241";
			$nonce_str =    $this->nonce_str();//随机字符串
			$openid =       $post['openid'];
			
		}
		
		$out_trade_no = $post['orderno'];//充值订单号
		$spbill_create_ip = get_client_ip();
		$total_fee = $order['pay_price']*100;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
		//$total_fee = 1*10;
		$trade_type = 'JSAPI';//交易类型 默认

		//这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
		$postData['appid'] = $appid;
		$postData['body'] = $body;
		$postData['mch_id'] = $mch_id;
		$postData['nonce_str'] = $nonce_str;//随机字符串
		$postData['notify_url'] = $notify_url;
		$postData['openid'] = $openid;
		$postData['out_trade_no'] = $out_trade_no;
		$postData['spbill_create_ip'] = $spbill_create_ip;//终端的ip
		$postData['total_fee'] = $total_fee;//总金额 最低为一块钱 必须是整数
		$postData['trade_type'] = $trade_type;
		$sign = $this->sign($postData);//签名
		$post_xml = '<xml>
			   <appid>'.$appid.'</appid>
			   <body>'.$body.'</body>
			   <mch_id>'.$mch_id.'</mch_id>
			   <nonce_str>'.$nonce_str.'</nonce_str>
			   <notify_url>'.$notify_url.'</notify_url>
			   <openid>'.$openid.'</openid>
			   <out_trade_no>'.$out_trade_no.'</out_trade_no>
			   <spbill_create_ip>'.$spbill_create_ip.'</spbill_create_ip>
			   <total_fee>'.$total_fee.'</total_fee>
			   <trade_type>'.$trade_type.'</trade_type>
			   <sign>'.$sign.'</sign>
			</xml> ';
			
			
			
		//统一接口prepay_id
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$xml = $this->http_request($url,$post_xml);
		$array = $this->xml($xml);//全要大写
		
		
		if($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS'){
			$time = time();
			$tmp='';//临时数组用于签名
			$tmp['appId'] = $appid;
			$tmp['nonceStr'] = $nonce_str;
			$tmp['package'] = 'prepay_id='.$array['PREPAY_ID'];
			$tmp['signType'] = 'MD5';
			$tmp['timeStamp'] = "$time";

			$data['state'] = 1;
			$data['timeStamp'] = "$time";//时间戳
			$data['nonceStr'] = $nonce_str;//随机字符串
			$data['signType'] = 'MD5';//签名算法，暂支持 MD5
			$data['package'] = 'prepay_id='.$array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
			$data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
			$data['out_trade_no'] = $out_trade_no;

		}else{
			$data['state'] = 0;
			$data['text'] = "错误";
			$data['RETURN_CODE'] = $array['RETURN_CODE'];
			$data['RETURN_MSG'] = $array['RETURN_MSG'];
		}
		
		echo json_encode($data);
		
	}
	
	//随机32位字符串
	public function nonce_str(){
		$result = '';
		$str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
		for ($i=0;$i<32;$i++){
			$result .= $str[rand(0,48)];
		}
		return $result;
	}
	

	//签名 $data要先排好顺序
	public function sign($data){
		$stringA = '';
		foreach ($data as $key=>$value){
			if(!$value) continue;
			if($stringA) $stringA .= '&'.$key."=".$value;
			else $stringA = $key."=".$value;
		}
		$wx_key = 'SHANGHAISHANGHAISHANGHAISHANGHAI';//申请支付后有给予一个商户账号和密码，登陆后自己设置key

		$stringSignTemp = $stringA.'&key='.$wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key 

		return strtoupper(md5($stringSignTemp));
	}
	
	//获取xml
	public function xml($xml){
		$p = xml_parser_create();
		xml_parse_into_struct($p, $xml, $vals, $index);
		xml_parser_free($p);
		$data = "";
		foreach ($index as $key=>$value) {
			if($key == 'xml' || $key == 'XML') continue;
			$tag = $vals[$value[0]]['tag'];
			$value = $vals[$value[0]]['value'];
			$data[$tag] = $value;
		}
		return $data;
	}
	
	//curl请求啊
	public function http_request($url,$data = null,$headers=array())
	{
		$curl = curl_init();
		if( count($headers) >= 1 ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_URL, $url);

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
	
	public function testpays(){
	  
	  $post = $this->param;
	  
	  if(!$post['type']){
		  
		  exit(json_encode(array('code'=>201,'msg'=>'缺少参数','status'=>false)));
		  
	  }
	  
	  
	  if($post['type'] == 1){
		  
		  $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx10786ed5823a7b38&secret=9f4546aec20679f68137cd6271a5bcf6&js_code='.$post['code']. '&grant_type=authorization_code';
		  
	  }else{
		  
		  $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wxb41cbfb32d23bf38&secret=a000b3a849f6527a300da86864fc6df3&js_code='.$post['code']. '&grant_type=authorization_code';
		  
	  }
	  
	  
	  $result = file_get_contents($url);
	  
	  
	  $res['result'] = json_decode($result,true);
	  $res['post'] = $post;
	  
	  exit(json_encode($res));
	  
	}
	
	public function threeStatus(){
		
		exit(json_encode(array('log_status'=>0)));
		
	}
	
    
    

}
