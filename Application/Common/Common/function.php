<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.0.131218';
const ONETHINK_ADDON_PATH = './Addons/';



function wx_share_init()
{

    $wxconfig = array();
    Vendor('Wxshare.class#jssdk');
    $jssdk    = new JSSDK('wx46fbc7e73f15a373', '44aa58acf67e8c595d2ba21c0190cc08');
    $wxconfig = $jssdk->getSignPackage();

    return $wxconfig;
}

/*批量修改
     *  @param $saveWhere ：想要更新主键ID数组
     *  @param $saveData    ：想要更新的ID数组所对应的数据
     *  @param $tableName  : 想要更新的表明
     *  @param $saveWhere  : 返回更新成功后的主键ID数组
     * */
function saveAll($saveWhere,&$saveData,$tableName){
    if($saveWhere==null||$tableName==null)
        return false;
    //获取更新的主键id名称
    $key = array_keys($saveWhere)[0];
    //获取更新列表的长度
    $len = count($saveWhere[$key]);
    $flag=true;
    $model = isset($model)?$model:M($tableName);
    //开启事务处理机制
    $model->startTrans();
    //记录更新失败ID
    $error=[];
    for($i=0;$i<$len;$i++){
        //预处理sql语句
        $isRight=$model->where($key.'='.$saveWhere[$key][$i])->save($saveData[$i]);
        if($isRight==0){
            //将更新失败的记录下来
            $error[]=$i;
            $flag=false;
        }
        //$flag=$flag&&$isRight;
    }
    if($flag ){
        //如果都成立就提交
        $model->commit();
        return $saveWhere;
    }elseif(count($error)>0&count($error)<$len){
        //先将原先的预处理进行回滚
        $model->rollback();
        for($i=0;$i<count($error);$i++){
            //删除更新失败的ID和Data
            unset($saveWhere[$key][$error[$i]]);
            unset($saveData[$error[$i]]);
        }
        //重新将数组下标进行排序
        $saveWhere[$key]=array_merge($saveWhere[$key]);
        $saveData=array_merge($saveData);
        //进行第二次递归更新
        saveAll($saveWhere,$saveData,$tableName);
        return $saveWhere;
    }
    else{
        //如果都更新就回滚
        $model->rollback();
        return false;
    }
}

function askStatus($status){
	
	switch($status){
		case 0:
			return "待付款";break;
		case 1:
			return "待接单";break;
		case 2:
			return "已接单";break;	
		case 4:
			return "待评价";break;
		case 5:
			return "已完成";break;
		case 6:
			return "售后中";break;
		case 7:
			return "已取消";break;
		case 9:
			return "已退款";break;
	}
	
}

//消息状态
function messageStatus($c_type1,$type){
	
	switch($c_type1){
		
		case 1://咨询订单
			if($type == 1){
				return "律师回复咨询";break;
			}elseif($type == 2){
				return "律师已接单";break;
			}elseif($type == 3){
				return "订单已确认完成";break;
			}elseif($type == 4){
				return "售后处理成功";break;
			}elseif($type == 5){
				return "驳回售后";break;
			}elseif($type == 6){
				return "送心意";break;
			}elseif($type == 7){
				return "律师有新订单";break;
			}elseif($type == 8){
				return "律师拒单";break;
			}elseif($type == 9){
				return "用户评价订单";break;
			}elseif($type == 10){
				return "律师回复评价";break;
			}elseif($type == 11){
				return "图文咨询订单回复";break;
			}elseif($type == 12){
				return "订单已确认完成";break;
			}
		case 2://众筹订单
		case 3:
			if($type == 1){
				return "返还咨询费";break;
			}elseif($type == 2){
				return "咨询下单";break;
			}
		case 4: //其他
			if($type == 1){//提现成功
				
				return "提现成功";break;
				
			}
		case 5://委托订单
			if($type == 1){
				return "律师竞标";break;
			}elseif($type == 2){
				return "采纳律师";break;
			}elseif($type == 3){
				return "委托到期自动取消";break;
			}
		
	}
	
}

function otype($type){
    if($type==1){
        $ordertype='咨询费';
    }elseif($type==2){
        $ordertype='图文咨询';
    }else if($type==3){
        $ordertype='电话咨询';
    }else if($type==4){
        $ordertype='见面咨询';
    }else if($type==5){
        $ordertype='代写文书';
    }else if($type==6){
        $ordertype='律师函';
    }else if($type==7){
        $ordertype='律师费';
    }else if($type==8){
        $ordertype='案件委托';
    }else if($type==9){
        $ordertype='余额充值';
    }else if($type==10){
        $ordertype='送心意';
    }else if($type==11){
        $ordertype='购买普通VIP';
    }else if($type==12){
        $ordertype='购买高级VIP';
    }else if($type==13){
        $ordertype='提现';
    }else if($type==14){
        $ordertype='购买合同';
    }else if($type==15){
        $ordertype='众筹捐款';
    }else if($type==16){
        $ordertype='法律告知函';
    }else if($type==17){
        $ordertype='升级为刑事互助';
    }else if($type==18){
        $ordertype='律师分析报告';
    }else if($type==19){
        $ordertype='为家人互助';
    }else if($type==20){
        $ordertype='互助订单';
    }else if($type==21){
        $ordertype='众筹订单';
    }else if($type==22){
        $ordertype='补齐众筹';
    }
    return $ordertype;
}
//流水状态
function waterStatus($status,$type){
	
	switch($status){
		case 1:
			if($type == 1){
				return "咨询费返还";break;
			}else{
				return "咨询费";break;
			}
			
		case 2:
			return "余额充值";break;	
		case 3:
			return "余额提现";break;
		case 4:
			return "送心意";break;
		case 5:
			return "咨询订单支付";break;
		case 6:
			return "委托订单支付";break;
		case 7:
			return "咨询订单拒单退款";break;
		case 8:
			return "关闭委托退回保证金";break;
		case 9:
			return "二次支付律师费";break;
		case 10:
			return "购买合同";break;
		case 11:
			return "众筹捐款";break;
		case 12:
			return "法律告知函";break;
		case 13:
			return "升级普通VIP";break;
		case 14:
			return "升级高级VIP";break;
		case 15:
			return "互助缴费";break;
		case 16:
			return "众筹委托律师费";break;
		case 17:
			return "充值互助金";break;
		case 18:
			return "取消订单退款";break;
		case 19:
			return "订单到账";break;
		case 20:
			return "售后退款";break;
		case 21:
			return "采纳咨询费";break;
		case 22:
			return "返还委托保证金";break;
	}
	
}

function askPayPrices($order){

	if($order['type'] == 8){
		
		$map = array();$map['orderno'] = array('eq',$order['orderno']);
		
		$wt = M('wt')->where($map)->find();
		
		if($wt['payed'] == 1 && $wt['status'] == 2 && $wt['twopay'] == 0 && $order['sid']!=0){
			
			return "保证金：￥50  二次支付：￥".($order['pay_price']-50);
			
		}else{
			
			return '￥'.$order['pay_price'];
		}
		
	}else{
		
		return '￥'.$order['pay_price'];
		
	}
	
}

function orderInfoStatus($type,$status,$orderno,$sid){
	
	$map = array();$map['orderno'] = array('eq',$orderno);
	if($type == 1){
		
		$fask = M('fask')->where($map)->find();
		if($fask['is_pay'] == 0){
			
			return "待支付";
			
		}elseif($fask['is_pay'] == 1 && $fask['status'] !=4 && $fask['sid'] == 0){
			
			return "未采纳律师";
			
		}elseif($fask['is_pay'] == 1 && !empty($fask['sid'])){
			
			return "已采纳律师";
			
		}elseif($fask['status'] == 4){
			
			return "已退款";
			
		}
		
		
	}elseif($type > 1 && $type < 8){
		
		$ask = M('ask')->where($map)->find();
		
		return askStatus($ask['status']);
		
	}elseif($type == 8){
		
		$wt = M('wt')->where($map)->find();
		
		if($status == 5){
			
			return "已退款";
			
		}elseif($wt['payed'] == 0){
			
			return "待支付保证金";
			
		}elseif($wt['payed'] == 1 && $wt['status'] != 2){
			
			return "竞标中";
		}elseif($wt['payed'] == 1 && $wt['status'] == 2 && $wt['twopay'] == 0 && $sid==0){
			
			return "竞标中";
		}elseif($wt['payed'] == 1 && $wt['status'] == 2 && $wt['twopay'] == 0 && $sid!=0){
			
			return "待二次支付(<font style='color:red'>已支付保证金</font>)";
		}elseif($wt['payed'] == 1 && $wt['twopay'] == 1){
			
			return "已支付完成";
		}
		
	}else{
		
		if($status == 0){
			
			return '待支付';
			
		}elseif($status == 1){
			
			return '已支付';
			
		}elseif($status == 5){
			
			return '已关闭退款';
			
		}elseif($status == -1){
			
			return '已取消';
			
		}
		
	}
	
}
function pay_title($type){
	
	switch($type){
		
		case 1:
			return "支付宝";break;
		case 2:
			return "微信";break;
		case 3:
			return "余额";break;
		
		
	}
	
}

function bili($type){
    if($type==2 ){
        $id=2;
    }else if($type==3){
        $id=3;
    }else if($type==4){
        $id=4;
    }else if($type==5){
        $id=5;
    }else if($type==6){
        $id=6;
    }else if($type==10){
        $id=9;
    }else if($type==8){
        $id=10;
    }
    $bili=M('feebili')->find($id)[bili];
    return $bili;

}
function cptype($type){
    if($type==1){
        $a=498;
    }else if($type==2){
        $a=499;
    }else if($type==3){
        $a=500;
    }else if($type==4){
        $a=501;
    }else if($type==5){
        $a=502;
    }
    return $a;
}
function get( $url, $_header = NULL )
{
    $curl = curl_init();
    //curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, false);
    if( stripos($url, 'https://') !==FALSE )
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if ( $_header != NULL )
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $_header);
    }
    $ret  = curl_exec($curl);
    $info  = curl_getinfo($curl);
    curl_close($curl);

    if( intval( $info["http_code"] ) == 200 )
    {
        return $ret;
    }

    return false;
}
/*
 * post method
 */
function post( $url, $param )
{
    $oCurl = curl_init ();
    curl_setopt ( $oCurl, CURLOPT_SAFE_UPLOAD, false);
    if (stripos ( $url, "https://" ) !== FALSE) {
        curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, false );
    }

    curl_setopt ( $oCurl, CURLOPT_URL, $url );
    curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $oCurl, CURLOPT_POST, true );
    curl_setopt ( $oCurl, CURLOPT_POSTFIELDS, $param );
    $sContent = curl_exec ( $oCurl );
    $aStatus = curl_getinfo ( $oCurl );
    curl_close ( $oCurl );
    if (intval ( $aStatus ["http_code"] ) == 200) {
        return $sContent;
    } else {
        return false;
    }
}
function noncestr(){
    $charts = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789";
    $max = strlen($charts);
    $noncestr = "";
    for ($i = 0; $i < 16; $i++) {
        $noncestr .= $charts[mt_rand(0, $max)];
    }
    return $noncestr;
}
function servernum($sid){
    $a=M('ask')->where("sid=".$sid)->count();
    $b=M('wtjoin')->where("uid=".$sid)->count();
    return $a+$b;
}
function score($sid){
    $w[tid]=array('in',implode(',',array_column(M('ask')->where("sid=".$sid)->select(),'id')) );
    $w[type]=array('eq',3);
    $tiao=M('pl')->where($w)->count();
    $fen=M('pl')->where($w)->sum('star');
    if($tiao){
        $score=ceil($fen/$tiao)*20;
    }else{
        $score=0;
    }

    return $score;
    //return M('pl')->getLastSql();
    //return $tiao;
}

function wt_progress($status){
	
	switch($status){
		
		case 1:
			return "联系客户";break;
		case 2:
			return "收集资料";break;
		case 3:
			return "委托方案";break;
		case 4:
			return "诉讼过程";break;
		case 0:
			return "暂无进度";break;
		
	}
	
}
function wt_result($status){
	
	switch($status){
		
		case 0:
			return "暂无结果";break;
		case 1:
			return "胜诉";break;
		case 2:
			return "败诉";break;
		case 3:
			return "其他";break;
		
	}
	
}

function tx_result($status){
	
	switch($status){
		
		case 1:
			return "审核中";break;
		case 2:
			return "提现成功";break;
		case 3:
			return "提现失败";break;
		
	}
	
}

function uinfo($id){
    $user=M('usermember')->find($id);

    if(strpos($user[icon],'http') !== false){

    }else{
        $user[icon]=picture($user[icon]);
    }
    if(!empty($user[phone])){
		$user['uphone'] = $user[phone];
        $user[phone]=yc_phone($user[phone]);
		
    }else{
        $user[phone]=$user[nickname];
    }

    return $user;
}
function sinfo($id){
    $staff=M('staff')->find($id);

    if(strpos($staff[icon],'http')  !== false){

    }else{
        $staff[icon]=picture($staff[icon]);
    }

    if(strpos($staff[label],',')){
        $c=explode(',',$staff[label]);
    }else{
        $c=array($staff[label]);
    }
    foreach($c as $k=>$v){
        $c[$k]= M('ajcate')->find($v)[name];
    }
    $staff[label]=$c;
    return $staff;
}
function expic($icon){
    if(strlen($icon)>0){
        if(strpos($icon,',')){
            $s=explode(',',$icon);
        }else{
            $s=array($icon);
        }
        $imgarr=array();
        foreach($s as $k=>$v){
            $imgarr[$k]= picture($v);
        }
        return $imgarr;
    }else{
        return array();
    }
    if($icon=='' || $icon== null){
        return array();
    }else{
        if(strpos($icon,',')){
            $s=explode(',',$icon);
        }else{
            $s=array($icon);
        }
        $imgarr=array();
        foreach($s as $k=>$v){
            $imgarr[$k]= picture($v);
        }
        return $imgarr;
    }
}

//订单号
function orderNumber(){
    $rand = rand(100000,999999);
    $year = substr(date('Y'),-2);
    $number = $year.date('md').$rand;
    $where['orderno'] = array('eq',$number);
    $res = M('order')->where($where)->find();
    if(!empty($res)){
        orderNumber();
    }else{
        return $number;
    }
}
function ordertype($type,$service_id){
    if($type==1){
        $ordertype='图文咨询';
    }else if($type==2){
        $ordertype='电话咨询';
    }else if($type==3){
        $ordertype='见面咨询';
    }else if($type==4){
        $ordertype='代写文书';
    }else if($type==5){
        $ordertype='律师函';
    }else if($type==6){
        $ordertype='律师费';
    }else if($type==7){
		
		$title = modelField($service_id,'feept','name');
		
        $ordertype=$title;
    }
    return $ordertype;
}
function monthname($type){
    if($type=='01'){
        $ordertype='1月';
    }else if($type=='02'){
        $ordertype='2月';
    }else if($type=='03'){
        $ordertype='3月';
    }else if($type=='04'){
        $ordertype='4月';
    }else if($type=='05'){
        $ordertype='5月';
    }else if($type=='06'){
        $ordertype='6月';
    }else if($type=='07'){
        $ordertype='7月';
    }else if($type=='08'){
        $ordertype='8月';
    }else if($type=='09'){
        $ordertype='9月';
    }else if($type=='10'){
        $ordertype='10月';
    }else if($type=='11'){
        $ordertype='11月';
    }else if($type=='12'){
        $ordertype='12月';
    }
    return $ordertype;
}

function editorPath($str){
    $a = str_replace('<img src="','<img src="'.C('WEB_SITE_URL'),$str);
    return $a;
}
//阿里云短信接口
function AliyunSendMsg($mobile,$smscode,$codes){

    $sms = new \Think\Sendmsg\api_demo\Sendsns();

    $result = $sms->ddsd($mobile,$smscode,$codes);

    $res =  json_decode(json_encode( $result),true);

    return $res;
}
function AliyunHotel($mobile,$smscode,$orderno,$name,$date1,$date2,$num,$price,$date3,$addr,$tel,$hotelname){
    
    $sms = new \Think\Sendmsg\api_demo\Sendsns();

    $result = $sms->hotel($mobile,$smscode,$orderno,$name,$date1,$date2,$num,$price,$date3,$addr,$tel,$hotelname);

    $res =  json_decode(json_encode( $result),true);

    return $res;
}

function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
function check_verify1($code, $id = 1){
    $verify = new \Think\Verify1();
    return $verify->check($code, $id);
}
//字符串截取
function cutstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr"))
    {
        return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return strip_tags($slice);
}




//获取分类名称
function category($id,$field){
	
	$cate = M('category')->field($field)->find($id);
	
	return $cate[$field];
	
}

//通过日期计算年龄
function birage($date){
	
	$day = date('Y',strtotime($date));
	
	$breay = date('Y',time());
	
	$age = $breay - $day;

	if($age <= 0){
		
		return "未满一周岁";
	}else{
		
		return $age."岁";	
	}
	
}


//取出汉字首字母
function getFirstCharter($str){
	if(empty($str)){return '';}
	$fchar=ord($str{0});
	if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
	$s1=iconv('UTF-8','gb2312',$str);
	$s2=iconv('gb2312','UTF-8',$s1);
	$s=$s2==$str?$s1:$str;
	$asc=ord($s{0})*256+ord($s{1})-65536;
	if($asc>=-20319&&$asc<=-20284) return 'A';
	if($asc>=-20283&&$asc<=-19776) return 'B';
	if($asc>=-19775&&$asc<=-19219) return 'C';
	if($asc>=-19218&&$asc<=-18711) return 'D';
	if($asc>=-18710&&$asc<=-18527) return 'E';
	if($asc>=-18526&&$asc<=-18240) return 'F';
	if($asc>=-18239&&$asc<=-17923) return 'G';
	if($asc>=-17922&&$asc<=-17418) return 'H';
	if($asc>=-17417&&$asc<=-16475) return 'J';
	if($asc>=-16474&&$asc<=-16213) return 'K';
	if($asc>=-16212&&$asc<=-15641) return 'L';
	if($asc>=-15640&&$asc<=-15166) return 'M';
	if($asc>=-15165&&$asc<=-14923) return 'N';
	if($asc>=-14922&&$asc<=-14915) return 'O';
	if($asc>=-14914&&$asc<=-14631) return 'P';
	if($asc>=-14630&&$asc<=-14150) return 'Q';
	if($asc>=-14149&&$asc<=-14091) return 'R';
	if($asc>=-14090&&$asc<=-13319) return 'S';
	if($asc>=-13318&&$asc<=-12839) return 'T';
	if($asc>=-12838&&$asc<=-12557) return 'W';
	if($asc>=-12556&&$asc<=-11848) return 'X';
	if($asc>=-11847&&$asc<=-11056) return 'Y';
	if($asc>=-11055&&$asc<=-10247) return 'Z';
	return null;
}

//按字母进行排序
function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_ASC",$SortType1 = "SORT_REGULAR") { 
	if(!is_array($ArrayData)) 
	{ 
	return $ArrayData; 
	} 
	$ArgCount = func_num_args(); 
	for($I = 1;$I < $ArgCount;$I ++) 
	{ 
	$Arg = func_get_arg($I); 
	if(!eregi("SORT",$Arg)) 
	{ 
	$KeyNameList[] = $Arg; 
	$SortRule[] = '$'.$Arg; 
	} 
	else 
	{ 
	$SortRule[] = $Arg; 
	} 
	} 
	foreach($ArrayData AS $Key => $Info) 
	{ 
	foreach($KeyNameList AS $KeyName) 
	{ 
	${$KeyName}[$Key] = $Info[$KeyName]; 
	} 
	} 
	$EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);'; 
	eval ($EvalString); 
	return $ArrayData; 
} 


function getgroup($id=UID){
    $auth_group_access=M('auth_group_access')->where('uid='.$id)->find();
    $auth_group=M('auth_group')->find($auth_group_access['group_id']);
    return $auth_group['title'];
}
function getgroupid($id){
    $auth_group_access=M('auth_group_access')->where('uid='.$id)->find();
    return $auth_group_access['group_id'];
}
//返回头像地址

function picture($id){
	
	$picture = M('picture')->find($id);
	
	if(empty($picture['path'])){
		
		return '/Public/Home/img/user_tx.png';
		
	}else{
       return $picture['path']; 
    }
	
	
	
}

//日期转换	
function toDate($time, $format = 'Y-m-d') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}

//返回某表字段

function modelField($id,$model,$field){
	
	$vo = M($model)->find($id);
	
	return $vo[$field];
	
}

//返回下载文件的路径
function fileurl($id){
    $files = M('file')->where('id='.$id)->find();
    if($files){
        $str = '/Uploads/Download/'.$files[savepath].$files[savename];
        return $str;
    }else{
        return '';
    }
}
function filename($id){
    $files = M('file')->where('id='.$id)->find();
    if($files){
        $str = $files[name];
        return $str;
    }else{
        return '';
    }
}

/**
 *
 * 通过 Thinkphp 的魔术方法 判断 推荐码是否已经存在返回 未被占用的随机码
 *
 * 先生成指定长度的随机码然后 递归判断
 * @data 数据（表数据）
 * @len 生成随机码长度
 * @tcode 要验证的随机码 生成随机码 后 递归的时候 传回函数 进行验证
 * return 未被 占用的 随机码
 *
 * */
function digCode($data,$len){
	$code = create_code($len);

	$row = $data->getByRecomendUser($code);

	if($row > 0){
		$code = digCode($data, $len);
	}

	return $code;

}
/**
 * 生成指定长度 的随机码中间加了判断，如果生成的
 * 码少于指定长度 就递归生成直至成功
 * 测试阶段没发现 有多于指定长度字符串的出现
 *
 *
 *
 * **/
function create_code($length = 4) {

	$length = intval($length);

	$length = $length-1;
	$min = pow(10,($length - 1));
	$max = pow(10, ($length + 1));

	$ret = rand($min, $max);
	//return $length+1;
	if(strlen($ret) != $length+1){
		$ret = create_code($length+1);
			
	}

	return $ret;
}
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

function  getagebybirthday($birthday){
	$temparr=explode('-',$birthday);
	$nowyear= date('Y',time());
	if(empty($birthday))return 0;
	return $nowyear-$temparr[0];
}
/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null){
	if(empty($cate_id)){
		return '未填写';
	}
	$cate   =   M('Category')->where('id='.$cate_id)->getField('title');
	
	return $cate;
}
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}


//后台会员加密
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
	return '' === $str ? '' : md5(sha1($str) . $key);
}


/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url){
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url(){
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name){
    $class = get_addon_class($name);
    if(class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    }else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()){
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if(isset($url['query'])){
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i:s'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if($info && isset($info[1])){
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if($info !== false && $info['nickname'] ){
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}
//去掉字符串中的标签
function miaoshu($str,$field){
		for($i=0;$i<count($str);$i++){
			$str[$i][$field]=strip_tags($str[$i][$field]);
		}
	return $str;
}
/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null){
    static $list;

    /* 非法分类ID */
    if(empty($id) || !is_numeric($id)){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if(!isset($list[$id])){
        $cate = M('Category')->find($id);
        if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id){
    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id){
    return get_category($id, 'title');
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null){
    static $list;

    /* 非法分类ID */
    if(!(is_numeric($id) || is_null($id))){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if(empty($list)){
        $map   = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if(is_null($id)){
        return $list;
    } elseif(is_null($field)){
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data){
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null){

    //参数检查
    if(empty($action) || empty($model) || empty($record_id)){
        return '参数不能为空';
    }
    if(empty($user_id)){
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);


    if($action_info['status'] != 1){
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id']      =   $action_info['id'];
    $data['user_id']        =   $user_id;
    $data['action_ip']      =   ip2long(get_client_ip());
    $data['model']          =   $model;
    $data['record_id']      =   $record_id;
    $data['create_time']    =   NOW_TIME;

    //解析日志规则,生成日志备注
    if(!empty($action_info['log'])){
        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
            $log['user']    =   $user_id;
            $log['record']  =   $record_id;
            $log['model']   =   $model;
            $log['time']    =   NOW_TIME;
            $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);
            foreach ($match[1] as $value){
                $param = explode('|', $value);
                if(isset($param[1])){
                    $replace[] = call_user_func($param[1],$log[$param[0]]);
                }else{
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
        }else{
            $data['remark'] =   $action_info['log'];
        }
    }else{
        //未定义日志规则，记录操作url
        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if(!empty($action_info['rule'])){
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self){
    if(empty($action)){
        return false;
    }

    //参数支持id或者name
    if(is_numeric($action)){
        $map = array('id'=>$action);
    }else{
        $map = array('name'=>$action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if(!$info || $info['status'] != 1){
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key=>&$rule){
        $rule = explode('|', $rule);
        foreach ($rule as $k=>$fields){
            $field = empty($fields) ? array() : explode(':', $fields);
            if(!empty($field)){
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
            unset($return[$key]['cycle'],$return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null){
    if(!$rules || empty($action_id) || empty($user_id)){
        return false;
    }

    $return = true;
    foreach ($rules as $rule){

        //检查执行周期
        $map = array('action_id'=>$action_id, 'user_id'=>$user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if($exec_count > $rule['max']){
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

        if(!$res){
            $return = false;
        }
    }
    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files){
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/'){
            mkdir($value);
        }else{
            @file_put_contents($value, '');
        }
    }
}

if(!function_exists('array_column')){
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null){
    if(empty($model_id)){
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if($info['extend'] != 0){
        $name = $Model->getFieldById($info['extend'], 'name').'_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true){
    static $list;

    /* 非法ID */
    if(empty($model_id) || !is_numeric($model_id)){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('attribute_list');
    }

    /* 获取属性 */
    if(!isset($list[$model_id])){
        $map = array('model_id'=>$model_id);
        $extend = M('Model')->getFieldById($model_id,'extend');

        if($extend){
            $map = array('model_id'=> array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->select();
        $list[$model_id] = $info;
        //S('attribute_list', $list); //更新缓存
    }

    $attr = array();
    foreach ($list[$model_id] as $value) {
        $attr[$value['id']] = $value;
    }

    if($group){
        $sort  = M('Model')->getFieldById($model_id,'field_sort');

        if(empty($sort)){	//未排序
            $group = array(1=>array_merge($attr));
        }else{
            $group = json_decode($sort, true);

            $keys  = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if(!empty($attr)){
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        $attr = $group;
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null){
    if(empty($value) || empty($table)){
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if(empty($field)){
        $info = $info->field(true)->find();
    }else{
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url'){
    $link = '';
    if(empty($link_id)){
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if(empty($field)){
        return $link;
    }else{
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null){
    if(empty($cover_id)){
        return false;
    }
    $picture = M('Picture')->where(array('status'=>1))->getById($cover_id);
    return empty($field) ? $picture : $picture[$field];
}


/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0){
    if(empty($pos) || empty($contain)){
        return false;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if($res !== 0){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */

function get_stemma($pids,Model &$model, $field='id'){
    $collection = array();

    //非空判断
    if(empty($pids)){
        return $collection;
    }

    if( is_array($pids) ){
        $pids = trim(implode(',',$pids),',');
    }
    $result     = $model->field($field)->where(array('pid'=>array('IN',(string)$pids)))->select();
    $child_ids  = array_column ((array)$result,'id');

    while( !empty($child_ids) ){
        $collection = array_merge($collection,$result);
        $result     = $model->field($field)->where( array( 'pid'=>array( 'IN', $child_ids ) ) )->select();
        $child_ids  = array_column((array)$result,'id');
    }
    return $collection;
}

/*
*
*返回时间的差距
*/
function ReTime($time){
	if(date('Y-m-d',time()) == date('Y-m-d',$time)){
		if(date('h',time())== date('h',$time)){
			if((date('i',time()) - date('i',$time)) == 0){
				return '刚才';
			}else{
				$FomartDate = date('i',time()) - date('i',$time);
				return $FomartDate.'分钟';
			}
		}else{
			$FomartDate = date('h',time()) - date('h',$time);
			return $FomartDate.'小时';
		}
	}else{
		return date('Y-m-d',$time);
	}
}

function yc_phone($str){
    $str = $str;
    $resstr = substr_replace($str, '****', 3, 4);
    return $resstr;
}
function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    if (0 == floor($t/60)) {
        return '刚刚';
    }
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}
function format_date1($time){
    $t=$time-time();
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'后';
        }
    }
}

//分页样式
function getpage($count,$pagesize=10){

    $p=new Think\Page($count,$pagesize);
    $p->lastSuffix=false;
    //$p->setConfig('header');
      $p->setConfig('prev','<');
      $p->setConfig('next','>');
  //$p->setConfig('first','');
   // $p->setConfig('END','末页');
   $p->setConfig('theme',' %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% ');

    return $p;
}
function weixinpay($order){
    $order['trade_type']='NATIVE';
    Vendor('Weixinpay.Weixinpay');
    $weixinpay=new \Weixinpay();
    return $weixinpay->pay($order);
}
//微信支付二维码
function wxpay_qrcode($data,$path,$imgname,$level=3,$size=4){
    Vendor('Phpqrcode.phpqrcode');
    // $size 点的大小：1到10,用于手机端4就可以了
    if(!file_exists($path))
    {
        mkdir($path, 0777);
    }
    //  生成的文件名
    $fileName = $path.$imgname.'.png';
    ob_end_clean();//清空缓冲区
    $object = new \QRcode();
    $object->png($data,$fileName,$level, $size);
}