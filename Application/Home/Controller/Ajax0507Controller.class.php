<?php


namespace Home\Controller;
use OT\DataDictionary;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");

class AjaxController extends  \Think\Controller {
    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config);

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
    }
    public function logout(){
        session('user',null);
        session('user_info',null);
        session('usertype',null);
        exit(json_encode(array('code'=>200,'msg'=>'退出成功')));
        //$this->redirect('/Home/Public/login_account');
    }
    //会员免费
    public function freeorder(){
        if(IS_POST){
            $post=I('post.');
            if(empty(session('user'))){
                exit(json_encode(array('code'=>400,'msg'=>'您还未登录,请先登录！')));
            }
            if(!empty($post[orderno])){
                $orderno=$post[orderno];
            }else{
                $orderno=orderNumber();
            }

            if(0){

            }else{
                $user = session('user');

                if(!empty($post['id'])){
                    $order['tid'] = $post['id'];
                }
                $order['orderno'] = $orderno;
                $order['type'] = $post[type];
                $order['uid'] = $user['id'];
                $order['utype'] = $user['type'];
                if(!empty($post['sid'])){
                    $order['sid'] = $post['sid'];
                }
                $order['status'] = 2;
                $order['pay_price'] = 0;
                $order['pay_type'] = 0;
                $order['create_time'] = time();
                $order['date_time'] = date('Y-m');

                $re=M('order')->add($order);

                if($re){
                    $order=M('order')->find($re);
                    $hetong=M('hetong')->find($post[id]);
                    if($post['type']==14){
                        $add['orderno'] = $order[orderno];
                        $add['uid'] = $user['id'];
                        $add['type'] = $hetong['type'];
                        $add['tid'] = $post['id'];
                        $add['money'] = 0;
                        $add['create_time'] = time();
                        $add['date_time'] = date('Y-m');
                        $re=M('hetonguser')->add($add);
                    }else if($post['type']==2 || $post['type']==3){
                        $add['order_id'] = $re;
                        $add['orderno'] = $orderno;
                        $add['uid'] = $user['id'];
                        $add['sid'] = $post['sid'];
                        $add['type'] = $post['type']-1;
                        $add['money'] = $order['pay_price'];
                        $add['create_time'] = time();
                        $add['payed'] = 1;
                        $add['status'] = 1;
                        $re=M('ask')->add($add);
                    }
                    exit(json_encode(array('code'=>200,'msg'=>$orderno)));
                }else{
                    exit(json_encode(array('code'=>400,'msg'=>'生成订单失败')));
                }
            }


        }

    }
    //下订单
    public function order(){
        if(IS_POST){
            $post=I('post.');
			$type=1;
			if(!$post['token']){
				
				if(empty(session('user'))){
					exit(json_encode(array('code'=>400,'msg'=>'您还未登录,请先登录！')));
				}
			}else{
				$map['token'] = array('eq',$post['token']);
				$userInfo = M('usermember')->where($map)->find();
				
				if(!$userInfo){
					
					$map['token'] = array('eq',$post['token']);
					$userInfo = M('staff')->where($map)->find();
					$type=2;
					if(!$userInfo){
						
						exit(json_encode(array('code'=>400,'msg'=>'您还未登录,请先登录！')));
					}
					
					
					
				}
				 
				
			}
			
            if(!empty($post[orderno])){
                $orderno=$post[orderno];
            }else{
                $orderno=orderNumber();
            }
           
           if(0){

           }else{
				if($post['token']){
					
					$user = $userInfo;
					
				}else{
					
					$user = session('user');
					
				}
               $map = array();$map['orderno'] = array('eq',$orderno);
				$order=M('order')->where($map)->find();
				
				if(!$order){
					
					if(!empty($post['id'])){
					   $order['tid'] = $post['id'];
				   }
				   $order['orderno'] = $orderno;
				   $order['type'] = $post[type];
				   $order['uid'] = $user['id'];
				   $order['utype'] = $type;
				   $order['content'] = $post['content']!='' ? $post['content'] : '';
				   if(!empty($post['sid'])){
					   $order['sid'] = $post['sid'];  
				   }
				   if($post[type]==1){
					   
					   if(empty($post[money])){
						   
						   $order['pay_price'] = C('WEB_LTATION_SITE');
						   
					   }else{
						   
						   $order['pay_price'] = $post[money];
					   }
					   
					   
					}else{
					   $order['pay_price'] = $post[money];
				   }
				   
				   
				   
				   if($post[type]==2 || $post[type]==3 || $post[type]==4 || $post[type]==5 || $post[type]==6 || $post[type]==7 || $post[type]==8){
					   $bili=bili($post[type]);
					   $s_price=$post[money]*(100-$bili)/100;
					   $order['s_price'] = $s_price;
				   }
				   $order['pay_type'] = $post['pay_type'];
				   $order['create_time'] = time();
				   $order['date_time'] = date('Y-m');
				   if(!empty($post['status'])){
					   $order['status'] = $post['status'];
				   }
				   $re=M('order')->add($order);
					
				}else{
					
					$re = $order['id'];
					
				}
				
               

               if($re){
                   $order=M('order')->find($re);
                   if($post['type']==15){
                       $add['order_id'] = $re;
                       $add['orderno'] = $orderno;
                       $add['uid'] = $user['id'];
                       $add['utype'] = $user['type'];
                       $add['tid'] = $post['id'];
                       $add['money'] = $post['money'];
                       $add['create_time'] = time();
                       $add['date_time'] = date('Y-m');
                       $re=M('zcjoin')->add($add);
                   }else if($post['type']==2 || $post['type']==3 || $post['type']==7){
                       
                       $add['order_id'] = $re;
                       $add['orderno'] = $orderno;
                       $add['uid'] = $user['id'];
                       $add['sid'] = $post['sid'];
                       $add['type'] = $post['type']-1;
                       $add['money'] = $order['pay_price'];
                       $add['create_time'] = time();
                       $re=M('ask')->add($add);
                   }else if($post['type']==14){
                       if($post['status']==1){
                           if($user[type]==1){
                               $m=M('usermember');
                           }else{
                               $m=M('staff');
                           }
                           $info=$m->find($user[id]);
                           $m->where("id=".$user[id])->setDec('htnum');

                       }
                       //$add['order_id'] = $re;
                      /* $add['orderno'] = $orderno;
                       $add['uid'] = $user['id'];
                       $add['sid'] = $post['sid'];
                       $add['type'] = $post['type'];
                       $add['money'] = $order['pay_price'];
                       $add['create_time'] = time();
                       $re=M('hetonguser')->add($add);*/
                   }else if($post['type']==16){
                       if($post['status']==1){
                           if($user[type]==1){
                               $m=M('usermember');
                           }else{
                               $m=M('staff');
                           }
                           $info=$m->find($user[id]);
                           $m->where("id=".$user[id])->setDec('gznum');

                       }
                       
                   }else if($post['type']==18){
                       if($post['status']==1){
                           if($user[type]==1){
                               $m=M('usermember');
                           }else{
                               $m=M('staff');
                           }
                           $info=$m->find($user[id]);
                           $m->where("id=".$user[id])->setDec('fxnum');

                       }

                   }
                   exit(json_encode(array('code'=>200,'msg'=>$orderno)));
               }else{
                   exit(json_encode(array('code'=>400,'msg'=>'生成订单失败')));
               }
           }

        }

    }
    //订单是否合法,是否已经支付完成
    public function checkorder(){
        if(IS_POST){
            $list=I('post.');
            $orderno=$list['orderno'];
            $order=M('order')->where('orderno='.$orderno)->find();
            if($order){
                if($order['status']==1){
                    exit(json_encode(array('code'=>200)));
                }else{
                    exit(json_encode(array('code'=>201)));
                }
            }else{
                exit(json_encode(array('code'=>400)));
            }

        }

    }
   
    //清空购物车
    public function clearcart(){
        if(setcookie("mycart", null, time() - 3600,'/')){
            exit(json_encode(array('code'=>200,'msg'=>'清空成功')));
        }else{
            exit(json_encode(array('code'=>400,'msg'=>'清空失败')));
        }
    }
    //加入购物车
    public function addtocart(){
        if(IS_POST){
            $list=I('post.');
            $id=$list['id'];
            $type=$list['type'];
            if($type=='jia'){
                $arr=unserialize($_COOKIE['mycart']);
                $arr[$id]=$arr[$id]+1;
                setcookie('mycart',serialize($arr),0,'/');
            }else if($type=='jian'){
                $arr=unserialize($_COOKIE['mycart']);
                $arr[$id]=$arr[$id]-1;
                if($arr[$id] ==0){
                    unset($arr[$id]);
                }
                setcookie('mycart',serialize($arr),0,'/');
            }else{
                if(isset($_COOKIE['mycart'])){
                    $arr=unserialize($_COOKIE['mycart']);
                    if(in_array($id,array_keys($arr))){
                        $arr[$id]=$arr[$id]+1;
                    }else{
                        $arr[$id]=1;
                    }
                    setcookie('mycart',serialize($arr),0,'/');
                }else{
                    $arr=array($id=>1);
                    setcookie('mycart',serialize($arr),0,'/');
                }
            }


            $li='';
            $num=0;
            $total=0;
            foreach ($arr as $k=>$v) {

                $re=M('room')->where("id=".$k)->find();
                $num++;
                $money=$v*$re['price'];
                if(ismobile()){

                    $li.='<li><em>'.$re['name'].'</em><i>￥'.$money.'</i><div class="buy_blus"><div class="shop_num"><p class="jian" data-id="'.$k.'"></p><input type="text" value="'.$v.'"><p class="jia"  data-id="'.$k.'"></p></div></div></li>';
                }else{
                    $li.='<li><i>'.$re['name'].'</i><div class="adds-dis"><p class="jian" data-id="'.$k.'">-</p><input type="text" value="'.$v.'"><p class="jia"  data-id="'.$k.'">+</p></div><em>￥'.$money.'</em></li>';
                }


                $total+=$money;
            }
            exit(json_encode(array('code'=>200,'msg'=>$li,'num'=>$num,'total'=>$total)));
            if($li){
                exit(json_encode(array('code'=>200,'msg'=>$li,'num'=>$num,'total'=>$total)));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'提交失败')));
            }
        }

    }
    // 加载购物车
    public function loadcart(){
        if(IS_POST) {
            $list = I('post.');
            //$type = $list['type'];
            if (isset($_COOKIE['mycart'])) {
                $arr = unserialize($_COOKIE['mycart']);
                $li = '';
                $num = 0;
                $total = 0;
                foreach ($arr as $k => $v) {

                    $re = M('room')->where("id=" . $k)->find();
                    $num++;
                    $money = $v * $re['price'];
                    $li .= '<li><i>' . $re['name'] . '</i><div class="adds-dis"><p class="jian" data-id="'.$k.'">-</p><input type="text" value="' . $v . '"><p class="jia" data-id="'.$k.'">+</p></div><em>￥' . $money . '</em></li>';

                    $total += $money;
                }
                if ($li) {
                    exit(json_encode(array('code' => 200, 'msg' => $li, 'num' => $num, 'total' => $total)));
                } else {
                    exit(json_encode(array('code' => 400, 'msg' => '加载失败')));
                }
            }else{
                    exit(json_encode(array('code' => 201, 'msg' => '购物车为空')));
            }
        }
    }

    /*
    * 发帖
    */
    public function fatie(){
        if(IS_POST){
            $list=I('post.');
            $add['uid']=session('user');
            $add['name']=$list['name'];
            $add['detail']=$list['detail'];

            $add['create_time']=time();

            $re=M('discuz')->add($add);
            if($re){
                exit(json_encode(array('code'=>200,'msg'=>'发布成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'发布失败')));
            }
        }

    }
    /*
   * 开关帖
   */
    public function onoff(){
        if(IS_POST){
            $list=I('post.');
            $kg=$list['onoff'];
            $find=M('discuz')->where("id=".$list['id'])->find()['onoff'];
            
                if($kg==$find){
                    if($kg==1){
                        exit(json_encode(array('code'=>200,'msg'=>'已开启')));
                    }else{
                        exit(json_encode(array('code'=>200,'msg'=>'已关闭')));
                    }
                   
                }
           
            $onoff=M('discuz')->where("id=".$list['id'])->find()['onoff']==1? 0:1;
            $re=M('discuz')->where("id=".$list['id'])->save(array('onoff'=>$onoff));
            if($re){
                exit(json_encode(array('code'=>200,'msg'=>'操作成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'操作失败')));
            }
        }
    }
    /*
  * 删除帖
  */
    public function deltiezi(){
        if(IS_POST){
            $list=I('post.');
            $re=M('discuz')->where("id=".$list['id'])->delete();
            if($re){
                exit(json_encode(array('code'=>200,'msg'=>'删除成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'删除失败')));
            }
        }
    }


    // 收藏
    public function collection(){
        if(IS_POST){
            $list=I('post.');
            if(empty(session('user'))){
                exit(json_encode(array('code'=>400,'msg'=>'请先登录!')));
            }
            
            $where['uid']=array('eq',session('user'));
            $where['tid']=array('eq',$list['id']);
            $where['type']=array('eq',$list['type']);
            $collection=M('collection')->where($where)->find();
            if(!empty($collection)) {
                $whe['id'] = array('eq', $collection['id']);
                $collectiondele = M('collection')->where($whe)->delete();
                if (!empty($collectiondele)) {
                    exit(json_encode(array('code' => 201, 'msg' => '取消成功')));
                } else {
                    exit(json_encode(array('code' => 400, 'msg' => '取消失败')));
                }
            }else{
                $add['tid']=$list['id'];
                $add['type']=$list['type'];
                $add['uid']=session('user');
                $add['time']=time();
                $collectionadd=M('collection')->add($add);
                if($collectionadd){
                    exit(json_encode(array('code'=>200,'msg'=>'收藏成功')));
                }else{
                    exit(json_encode(array('code'=>400,'msg'=>'收藏失败')));
                }
            }
        }

    }
    // 举报
    public function jubao(){
        if(IS_POST){
            $list=I('post.');
            $commentid=intval($list['id']);
            $ip=getip();
            $whe['commentid']=array('eq',$commentid);
            $whe['ip']=array('eq',$ip);
            if(M('jubao')->where($whe)->find()){
                exit(json_encode(array('code' => 201, 'msg' => '已举报 !')));
            }
            $com=M('comment')->where("id=".$commentid)->find();
            $add['ip']=$ip;
            $add['content']=$com['content'];
            $add['commentid']=$commentid;
            $add['comuid']=$com['uid'];
            //$add['comtime']=$com['time'];
            $add['time']=time();

            if(M('jubao')->add($add)) {
                exit(json_encode(array('code' => 200, 'msg' => '举报成功 !')));
            }else{
                exit(json_encode(array('code' => 400, 'msg' => '举报失败 !')));
            }
        }
    }
    /*
   * 建议
   */
    public function advice(){
        if(IS_POST){
            $list=I('post.');
            if(empty(session('user'))){
                $add['uid']=0;
            }else{
                $add['uid']=session('user');
            }
            $add['advicer']=$list['advicer'];
            $add['tel']=$list['tel'];
            $add['email']=$list['email'];
            $add['tid']=$list['id'];
            $add['name']=$list['name'];
            $add['content']=$list['content'];
            $add['create_time']=time();
            $add['icon']=$list['icon'];

            $re=M('advice')->add($add);
            if($re){
                exit(json_encode(array('code'=>200,'msg'=>'提交成功')));
            }else{
                exit(json_encode(array('code'=>400,'msg'=>'提交失败')));
            }
        }

    }
    public function pictureUploade(){

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','pdf','docx','rar','doc');// 设置附件上传类型
        $upload->rootPath  =     '/Uploads'; // 设置附件上传根目录
        $upload->savePath  =     '/Picture/'; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();
        //存入图片
        $picture[path] = '/Uploads'.$info[icon][savepath].$info[icon][savename];
        $picture[md5] = $info[icon][md5];
        $picture[sha1] = $info[icon][sha1];
        $picture[create_time] = time();
//判断是否有图片
        $wherea['md5'] = $info[icon][md5];
        $wherea['sha1'] = $info[icon][sha1];
        $picFind = M('picture')->where($wherea)->find();

        if($picFind){
            $_POST['icon'] = $picFind['id'];

            unlink($picture[path]);
        }else{
            $pic = M('picture')->add($picture);
            $_POST['icon'] = $pic;

        }


        //存入文件
        $file[savename] = $info[files][savename];
        $file[savepath] = $info[files][savepath];
        $file[size] = $info[files][size];
        $file[sha1] = $info[files][sha1];
        $file[sha1] = $info[files][sha1];
        $file[name] = $info[files][name];
        $file[ext] = $info[files][ext];
        $file[md5] = $info[files][md5];
        $file[create_time] = time();
        //判断是否有文件
        $whereb['md5'] = $info[files][md5];
        $whereb['sha1'] = $info[files][sha1];
        $filesFind = M('file')->where($whereb)->find();
        if($filesFind){
            $_POST['files'] = $filesFind['id'];
            $fileurl = 'Uploads/'.$file[savepath].$file[savename];
            unlink($fileurl);
        }else{
            $files = M('file')->add($file);
            $_POST['files'] = $files;
        }



    }

    public function sendLatLog(){

        if(IS_POST){

            $address = I('post.address');
            $address = $this -> trimall($address);
            $result = file_get_contents('http://restapi.amap.com/v3/geocode/geo?key=8bd55b21a9232dc24e65f66c1cde9148&s=rsv3&city=35&address='.$address);

            $res = json_decode($result,true);

            if($res['status'] == 1 && $res['info'] == 'OK' && !empty($res['geocodes'][0]['location'])){

                $jwd = explode(',',$res['geocodes'][0]['location']);

                $code['code'] = 200;
                $code['log'] = $jwd[0]; //经度
                $code['lat'] = $jwd[1];//纬度

                exit(json_encode($code));

            }else{

                $code['code'] = 500;
                $code['content'] = '经纬度解析失败，请手动填写经纬度！';
                exit(json_encode($code));

            }

        }

    }

    public function trimall($str)//删除空格
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
}