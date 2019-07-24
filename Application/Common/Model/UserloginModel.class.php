<?php

namespace Common\Model;
use Think\Verify;
/**
 * 用户登录注册模型
 */
class UserloginModel {

    //登录
    public function login1($post)
    {
        $arr['phone'] = array('eq', $post['phone']);
        if($post['password']){
            $arr['password'] = array('eq',$post['password']);
        }
        /*if($post['yzm']){
            if(S($post['phone'])!=$post['yzm']){
                return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data);
            }
        }*/

        if ($post['type'] == 1) {

            if ($result = M('usermember')->where($arr)->find()) {
                $token = md5($result['id'] . noncestr());
                $result['type'] = 1;
                $result['token'] = $token;
                if(strpos($result[icon],'http') !== false){

                }else{
                    $result[icon]=picture($result[icon]);
                }
                $result['webphone'] = C('WEB_SITE_MOBILE');

                $save = M('usermember')->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
                return array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result);
            }else{
                return array('status' => true, 'msg' => '账号密码错误', 'code' => 201, 'data' => $result);
            }

        }elseif ($post['type'] == 2) {
            if ($result = M('staff')->where($arr)->find()) {
                $token = md5($result['id'] . noncestr());
                $result['type'] = 2;
                $result['token'] = $token;
                if(strpos($result[icon],'http') !== false){

                }else{
                    $result[icon]=picture($result[icon]);
                }
                $result['webphone'] = C('WEB_SITE_MOBILE');
                $save = M('staff')->where(array('id' => $result['id']))->save(array('token' => $token));
                return array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result);
            } else {
                $add['phone'] = $post['phone'];
                $add['nickname'] = $post['phone'];
                $add['idno'] = str_shuffle(time());
                $add['create_time'] = time();
                $add['date_time'] = date('Y-m');
                $re = M('staff')->add($add);
                if ($re) {
                    $result = M('staff')->find($re);
                    $token = md5($re . noncestr());
                    $result['type'] = 2;
                    $result['token'] = $token;
                    $result['icon'] = picture($result['icon']);
                    $result['webphone'] = C('WEB_SITE_MOBILE');
                    $save = M('staff')->where(array('id' => $re))->save(array('token' => $token));
                    return array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result);
                } else {
                    return array('status' => true, 'msg' => '新增用户失败', 'code' => 201, 'data' => $result);
                }
            }

        }


    }
    public function login($post)
    {
        $arr['phone'] = array('eq', $post['phone']);

        if(!empty($post['password'])){
            $arr['password'] = array('eq',$post['password']);
        }
        if($post['yzm']){
            if(S($post['phone'])!=$post['yzm']){
                return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$data);
            }
        }

        if ($post['type'] == 1) {

            $result = M('usermember')->where($arr)->find();
            $sql = M('usermember')->getLastSql();
            if ($result) {
                $token = md5($result['id'] . noncestr());
                $result['type'] = 1;
                $result['token'] = $token;
                $result['icon'] = picture($result['icon']);
                
                $result['webphone'] = C('WEB_SITE_MOBILE');
                $result['workday'] = C('WEB_WORKDAY');
                $result['wxkefu'] = C('WEB_WX_KEFU');

                $save = M('usermember')->where(array('id' => $result['id']))->save(array('token' => $token, 'update_time' => time()));
                return array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result);
            }else{

                if(!empty($post['password'])){

                    return array('status' => true, 'msg' => '账号密码错误', 'code' => 201, 'data' => $result,'sql'=>$sql);

                }else{

                    return array('status' => true, 'msg' => '当前手机号未注册，注册后即可用短信验证码登录!', 'code' => 201, 'data' => $result,'sql'=>$sql);

                }

            }

        }elseif ($post['type'] == 2) {
            if(!M('staff')->where("phone=".$post['phone'])->find()){
                return array('status' => true, 'msg' => '您还不是法援宝的用户,去注册 !', 'code' => 201, 'data' => $result);
            }

            if ($result = M('staff')->where($arr)->find()) {
                $token = md5($result['id'] . noncestr());
                $result['type'] = 2;
                $result['token'] = $token;
                $result['icon'] = picture($result['icon']);

                $result['webphone'] = C('WEB_SITE_MOBILE');
                $result['workday'] = C('WEB_WORKDAY');
                $result['wxkefu'] = C('WEB_WX_KEFU');
                $save = M('staff')->where(array('id' => $result['id']))->save(array('token' => $token));
                return array('status' => true, 'msg' => '登录成功', 'code' => 200, 'data' => $result);
            } else {
                return  array('status' => true, 'msg' => '密码错误!', 'code' => 201, 'data' => $result);
                
               
            }

        }


    }
    
    //验证码
    public function yzm($post){
        $phone = $post['phone'];
        $yzm = rand(1000,9999);

        if($post[type]==1){
            $smscode='SMS_156390483';
        }else if($post[type]==2){
            $smscode='SMS_156390481';//reg
        }

        $re=AliyunSendMsg($phone,$smscode,$yzm);
        if($re){
            S($phone,$yzm,300);
            return array('status'=>true,'msg'=>'验证码已发送','code'=>200,'data'=>$re);
        }else{

            return array('status'=>true,'msg'=>'验证码已发送','code'=>201,'data'=>$re);
        }

    }
    
    //配送端登录
    public function staff_login($post) {
        $yzm = $post['yzm'];
        $phone = $post['phone'];
        $sever_yzm = S($phone);
        if($yzm!=$sever_yzm){
            return array('status'=>false,'msg'=>'验证码有误','code'=>201,'data'=>$data);
        }
        $result = M('staff')->where(array('phone'=>$phone,'status'=>1))->find();
        
        if($result){
            $charts = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789";
            $max = strlen($charts);
            $noncestr = "";
            for($i = 0; $i < 16; $i++){
                $noncestr .= $charts[mt_rand(0, $max)];
            }
            $token = md5($result['id'].$noncestr);
            $data = array(
                "uid"=>$result['id'],

                "token"=>$token,
                "icon"=>$result['icon'],
                "nikename"=>$result['nikename'],
                "phone"=>$result['phone']
            );
            $save = M('staff')->where(array('id'=>$result['id']))->save(array('token'=>$token,'login_time'=>time()));
            return array('status'=>true,'msg'=>'登录成功','code'=>200,'data'=>$data);
        }else{
            return array('status'=>false,'msg'=>'登录信息有误','code'=>201,'data'=>$data);
        }
    }

    //忘记密码
    public function forget($post){
        $phone = $post['phone'];
        //$yzm = S($uid);
        //return array('status'=>false,'msg'=>check_verify1($post['yzm'],0),'code'=>202,'data'=>$post['yzm']);

        
        if(M('usermember')->where(array('phone'=>$phone))->find()){
            $m=M('usermember');
        }elseif(M('staff')->where(array('phone'=>$phone))->find()){
            $m=M('staff');
        }else{
            return array('status'=>false,'msg'=>'不存在该用户','code'=>201,'data'=>$post['yzm']);
        }

        if(!check_verify1($post['yzm'],1)){
            return array('status'=>false,'msg'=>'验证码错误','code'=>201,'data'=>$post['yzm']);
        }else{

            $save['password'] = $post['password'];
            //$post['password'] = md5(md5($post['password']));
            $re = $m->where(array('phone'=>$phone))->save($save);
            if($re){
                return array('status'=>true,'msg'=>'修改成功','code'=>200,'data'=>$data);
            }else{
                return array('status'=>false,'msg'=>'修改失败','code'=>201,'data'=>$data);
            }

        }

    }
	
	//找回密码
	public function forgeter($post){
		
		$phone = $post['phone'];
        //$yzm = S($uid);
        //return array('status'=>false,'msg'=>check_verify1($post['yzm'],0),'code'=>202,'data'=>$post['yzm']);

        
        if($post['type'] == 1){
            $m=M('usermember');
        }else{
            $m = M('staff');
        }
		
		$user = $m->where(array('phone'=>$phone))->find();

        if(!$user){
            return array('status'=>false,'msg'=>'当前手机号不存在','code'=>201);
        }
		
		$code = S($phone);
		
		if($code != $post['yzm']){
			
			return array('status'=>false,'msg'=>'短信验证码有误','code'=>201);
			
		}
		
		
		
		$save['password'] = $post['password'];
	
		$re = $m->where(array('id'=>$user['id']))->save($save);
		/*if($re){
			
		}else{
			return array('status'=>false,'msg'=>'修改失败，新密码和老密码不能一致','code'=>201,'data'=>$data);
		}*/
		return array('status'=>true,'msg'=>'修改成功','code'=>200);
		
	}
    
    //获取城市ID
    public function city_id($post){
        $city_name = $post['city_name'];
        $city = M('city')->where(array('city'=>$city_name))->find();
        if(!empty($city)){
            $data = $city;
            return array('status'=>true,'msg'=>'获取成功','code'=>200,'data'=>$data);
        }else{
            $data = $city;
            return array('status'=>true,'msg'=>'暂无此城市','code'=>200,'data'=>$data);
        }
    }
    
    //获取城市列表
    public function city(){
        $city = M('city')->where(array('status'=>1))->select();
        return array('status'=>true,'msg'=>'获取成功','code'=>200,'data'=>$city);
    }

}

?>
