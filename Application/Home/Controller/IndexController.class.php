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
class IndexController extends HomeController {



    public function register(){
        if(IS_POST){
            $list=I('post.');


            if(session($list['phone'])!=$list['regphonecode']){
                exit(json_encode(array('code'=>403,'msg'=>'验证码错误!')));
            }
            $add['username']=$list['username'];
            $add['password']=$list['password'];
            //$add['password']=md5(md5($list['password']));
            $add['nickname']    = $list['nickname'];
            $add['wx_openid']    = $list['wx_openid'];
            $add['pid']    = $list['pid'];
            $add['create_time']=time();

            $useradd= M('usermember')->add($add);
            if($useradd){
                session('user',$useradd);
                session('user_info',M('usermember')->where("id=".$useradd)->find());
                exit(json_encode(array('code'=>200,'msg'=>'注册成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'注册失败')));
            }

        }else{
            $this->display();
        }
    }

    public function index(){

        $this->redirect('user/index');

    }
   
	public function detail(){
        //$info=$this->wx_info();

        //根据openid找到了人,才去记录浏览时间
        if(1 || $info){
            $user= M('usermember')->where("wx_openid=".$info['openid'])->find();
            if($user){
                $this->assign('uid',$user[id]);
            }else{
                $this->assign('uid',0);
            }
        }

        $fx = M('fxcontent')->where("id=".$_GET['id'])->find();
        if($fx[icon] !=0){
            if(strpos($fx[icon],',')){
                $a=explode(',',$fx[icon]);
                $fx[icon] =$a ;
            }else{
                $fx[icon] =array($fx[icon]);
            }
        }


        //dump($fx);
        $this->assign('fx',$fx);
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