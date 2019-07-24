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
class TaskController extends HomeController {

   //免费咨询3天未回复的钱返还账户
	public function return_price(){

        $list = M('fanswer')->field('tid')->group('tid')->select();
		if(!empty($list)){
			$map['id'] = array('not in',implode(',',array_column($list,'tid')));
		}
		$map['create_time'] = array('lt',time()-3*60*60*24);
		$map['_string'] = '(private=1 or reward_price>0) and is_pay=1';
		
		$list = M('fask')->where($map)->select();
		
		if(!empty($list)){
			
			foreach($list as $k => $v){
				
				$user = M('usermember')->find($v['uid']);
				
				$water[$k] = array(
				
					'uid'=>$v['uid'],
					'user_type'=>1,
					'type'=>1,
					'water_type'=>1,
					'new_price'=> $v['price'],
					'create_time'=>time(),
					'remark'=>'免费咨询返还',
					'return_id'=>$v['id'],
					'user_money'=>$user['balance']+$v['price']
				
				);
				
				//添加消息
				$message[$k] = array(
					'user_id' =>$v['uid'],
					'user_type'=>1,
					'message'=>"您发布的咨询3天内都无律师回复，已返还",
					'add_time'=>time(),
					'type'=>1,  //返还咨询费
					'c_type1'=>3,  //咨询订单
					'return_id'=>$v['id']
				);
				
				$whid[$k] = $v['uid'];
				$savelist[$k] = array(
					'balance' => $user['balance'] + $v['price'],
					'update_time'=>time()
				);
				
				$whorder_id[$k] = $v['id'];
				$whorder_save[$k] = array(
					
					'status'=>4,
					'update_time'=>time()
				
				);
				
			}
			
			$where['ID'] = $whid;
			$saves = $savelist;
			
			$whord['ID'] = $whorder_id;
			$whordsave = $whorder_save;
			
			M('mymessage')->addAll($message);
			M('flowater')->addAll($water);
			
			saveAll($where,$saves,'usermember');
			saveAll($whord,$whordsave,'fask');
			
		}

	}

	//免费咨询已回复3天未采纳的自动采纳
	public function fask_accept(){
		
		$map['sid'] = array('eq',0);
		$map['status'] = array('eq',1);
		$list = M('fask')->where($map)->select();
		
		if(!empty($list)){
			$messageList = array();$waterList = array();
			foreach($list as $k => $v){
				
				$map = array();$map['tid'] = array('eq',$v['id']);
				
				$fanswer = M('fanswer')->where($map)->order('create_time asc')->find();
				
				if(!empty($fanswer)){
					
					$whid[$k] = $v['id'];
                    $savelist[$k] = array(
                        'sid'=> $fanswer['sid']
                    );
					
					$fanwhid[$k] = $fanswer['id'];
                    $fansavelist[$k] = array(
                        'choose'=> 1
                    );
					
					if($v['private'] == 1 || $v['reward_price']>0){
				
						$map['orderno'] = array('eq',$v['orderno']);
						$map['status'] = array('eq',1);
						$order = M('order')->where($map)->find();
						
						if($order){
							$staff=sinfo($fanswer['sid']);
							$stawhid[] = $fanswer['sid'];
							$stasavelist[] = array(
								'balance'=> $staff['balance']+ ($order['pay_price']*0.7)
							);
							
							$ordwhid[] = $order['id'];
							$ordsavelist[] = array(
								's_price'=> $order['pay_price']*0.7,
								'sid' => $fanswer['sid']
							);
							
							//添加流水
							$water = array(
								'uid'=>$fanswer['sid'],
								'type'=>1,
								'user_type'=>2,
								'water_type'=>21,//采纳咨询费
								'new_price'=> $order['pay_price']*0.7,
								'create_time'=>time(),
								'remark'=>'采纳咨询费',
								'return_id'=>$order['id'],
								'user_money'=>$staff['balance']+ ($order['pay_price']*0.7)
							);
							
							$waterList[] = $water;
							
						}
						
					}
					
					//添加消息
					$message = array(
						'user_id' =>$fanswer['sid'],
						'user_type'=>2,
						'message'=>"您的意见已被用户采纳 ！",
						'add_time'=>time(),
						'type'=>12,  //采纳律师
						'c_type1'=>1,  //
						'return_id'=>$v['id']
					);
					
					$messageList[] = $message;

				}
				
			}
			$where = array();$saves = array();
			if(!empty($whid)){
				$where['ID'] = $whid;
                $saves = $savelist;
				saveAll($where,$saves,'fask');
			}
			$where = array();$saves = array();
			if(!empty($fanwhid)){
				$where['ID'] = $fanwhid;
                $saves = $fansavelist;
				saveAll($where,$saves,'fask');
			}
			$where = array();$saves = array();
			if(!empty($stawhid)){
				$where['ID'] = $stawhid;
                $saves = $stasavelist;
				saveAll($where,$saves,'fask');
			}
			$where = array();$saves = array();
			if(!empty($ordwhid)){
				$where['ID'] = $ordwhid;
                $saves = $ordsavelist;
				saveAll($where,$saves,'fask');
			}
			$where = array();$saves = array();
			if(!empty($waterList)){
				M('flowater')->addAll($waterList);
			}
			if(!empty($messageList)){
				M('mymessage')->addAll($messageList);
			}
			
		}
		
	}
	
	//超过3天的金额自动到账
	public function order_transfer(){

		$where['is_transfer']=array('eq',0);
        $where['is_confirm']=array('eq',1);
        $where['status']=array('eq',1);

        $result = M("order")->where($where)->select();

		//echo "<pre></pre>";
		if(!empty($result)){
			$messageList = array();$waterList = array();$ind = 0;
			foreach($result as $k => $v){

				if(($v['update_time']+3*60*60*24) <= time()){//已到达自动到账条件
					
					//添加消息
					$message = array(
						'user_id' =>$v['sid'],
						'user_type'=>2,
						'message'=>"您的咨询订单已自动确认完成",
						'add_time'=>time(),
						'type'=>12,  //自动确认订单
						'c_type1'=>1,  //咨询订单
						'return_id'=>$v['id']
					);
					
					
					
					$messageList[] = $message;
					$user=sinfo($v['sid']);
					
					$water = array(
						'uid'=>$v['sid'],
						'type'=>1,
						'user_type'=>2,
						'water_type'=>19,//订单到账
						'new_price'=> $v['s_price'],
						'create_time'=>time(),
						'remark'=>'订单到账',
						'return_id'=>$v['id'],
						'user_money'=>$user['balance']+$v['s_price']
					);
					
					$waterList[] = $water;
					
					$map = array();$map['orderno'] = array('eq',$v['orderno']);
					
					$ask = M('ask')->where($map)->find();
					
					$whid[] = $ask['id'];
                    $savelist[] = array(
                        'uover'=> 1,
						'sover'=> 1,
						'status'=> 4,
						'state'=> 3,
						'is_confirm'=>1,
                    );
					
					$ordwhid[] = $v['id'];
                    $ordsavelist[] = array(
                        'is_confirm'=> 1,
						'is_transfer'=> 1,
						'update_time'=> time()
                    );
					
					/*$stawhid[] = $v['sid'];
					$stasavelist[] = array(
						'balance'=> $water['user_money']
					);*/
					M('staff')->where('id='.$v['sid'])->save(array('balance'=>$water['user_money']));

				}
				
			}
			//var_dump($ordsavelist);die();
			if(!empty($messageList)){
				
				M('flowater')->addAll($waterList);
				M('mymessage')->addAll($messageList);
				
				$where['ID'] = $whid;
                $saves = $savelist;
				saveAll($where,$saves,'ask');
				
				$wheres['ID'] = $ordwhid;
                $savess = $ordsavelist;
				saveAll($wheres,$savess,'order');
				
				/*$where['ID'] = $stawhid;
                $saves = $stasavelist;
				saveAll($where,$saves,'staff');*/
				
			}
			
		}

	}
	
	public function fask_cn(){
		
		$list = M('fanswer')->where('choose=1')->select();
		
		foreach($list as $k => $v){
			
			M('fask')->where('id='.$v['tid'])->save(array('sid'=>$v['sid']));
			
		}
		
	}
	
	//分享二维码下载app用户端
    public function down_app(){
		
		header("Content-type: text/html; charset=utf-8");
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //分别进行判断终端
        if(strpos($agent,'iphone') || strpos($agent,'ipad')){
            //苹果IOS
            $url = 'https://itunes.apple.com/cn/app/id1451181486';
            header('Location:'.$url);
        }
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			echo '<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/><meta name="format-detection" content="telephone=no,email=no,date=no,address=no">';
			echo "<div style='width:100%; height:3rem; line-height:3rem; text-align:center; color:#f00; font-size:1.2rem;'>请在浏览器中打开此页面</div>";die();
			
		}
        if(strpos($agent,'android')){
            //安卓
           /* $where['status'] = array('eq',1);
            $where['platform'] = array('eq',1);
            $res = M('appip')->where($where)->order('vsersion_code desc')->find();

            $infos = M('file')->find($res['music_file']);
            $ser = $_SERVER['HTTP_HOST'];*/
           // $url = 'http://'.$ser.'/Uploads/'.$infos['savepath'].$infos['savename'];
		   
		    $url = 'http://www.test2.test/Public/apk/fayuanb.0.1.21.apk';
            header('Location:'.$url);
        }
    }
	
	//分享二维码下载app律师端
    public function down_app_staff(){
		
		header("Content-type: text/html; charset=utf-8");
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //分别进行判断终端
        if(strpos($agent,'iphone') || strpos($agent,'ipad')){
            //苹果IOS
            $url = 'https://itunes.apple.com/cn/app/id1451179284';
            header('Location:'.$url);
        }
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
			echo '<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/><meta name="format-detection" content="telephone=no,email=no,date=no,address=no">';
			echo "<div style='width:100%; height:3rem; line-height:3rem; text-align:center; color:#f00; font-size:1.2rem;'>请在浏览器中打开此页面</div>";die();
			
		}
        if(strpos($agent,'android')){
            //安卓
           /* $where['status'] = array('eq',1);
            $where['platform'] = array('eq',1);
            $res = M('appip')->where($where)->order('vsersion_code desc')->find();

            $infos = M('file')->find($res['music_file']);
            $ser = $_SERVER['HTTP_HOST'];*/
           // $url = 'http://'.$ser.'/Uploads/'.$infos['savepath'].$infos['savename'];
		   
		    $url = 'http://www.test2.test/Public/apk/fayuanb_staff.0.1.10.apk';
            header('Location:'.$url);
        }
    }

}