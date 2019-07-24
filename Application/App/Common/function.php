<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19 0019
 * Time: 上午 10:09
 */
/*
 * 获取header信息
 *
 * */
function getAllHeader(){
    foreach ($_SERVER as $name => $value)
    {
        if (substr($name, 0, 5) == 'HTTP_')
        {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

//验证TOKEN

function check_token($token){

    $arr['token'] = array('eq',$token);
    $value = M('token_value')->where($arr)->find();
    if(!$value){
        $data['code'] = 500;
        $data['msg'] = 'token验证失败';
        exit(json_encode($data));
    }
    if($value['invalid_time'] < time()){
        $data['code'] = 501;
        $data['msg'] = 'token已过期';
        exit(json_encode($data));
    }
    return true;
}


function headerInfo(){
    $header = apache_request_headers();
    $authorization = explode('_',$header['Authorization']);
    return $authorization;
}

//缩略图
function get_icon_img($icon_url,$icon){
    if($icon>0){
        return picture($icon);
    }else{
        if($icon_url!=''){
            return $icon_url;
        }else{
            return picture($icon);
        }
    }
}

//获取图集
function imgarr($imgarr) {
    if (!empty(trim($imgarr))) {
        $imgarrse = explode(',', $imgarr);
        foreach ($imgarrse as $ks => $vs) {
            $img[$ks] = picture($vs);
        }
        return $img;
    } else {
        return array();
    }
}





//判断是否是手机登录
function ismobile(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");

    $is_mobile = false;

    foreach ($mobile_agents as $device) {

        if (stristr($user_agent, $device)) {

            $is_mobile = true;

            break;

        }

    }

    return $is_mobile;

}

