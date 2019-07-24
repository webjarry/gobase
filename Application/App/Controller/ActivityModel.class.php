<?php

namespace App\Model;
/**
 * 用户个人中心模型
 */
class ActivityModel {

    //我参加的活动
    public function my_activity($uid){
        $where['b.uid'] = array('eq',$uid);
        $where['a.status'] = array('eq',1);
        $list = M('activity_baoming as b')->join('bhy_activity as a on a.id = b.activity_id')->where($where)->field('a.*')->order('a.id desc')->group('b.activity_id')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl']   = get_icon_img('',$ve['icon']);
            $list[$ke]['create_time'] = formatTime(date('Y-m-d H:i:s',$ve['create_time']));
            $list[$ke]['activity_status']   = strip_tags(get_activity_status($ve)); //活动状态
        }
        return $list;
    }

    //活动城市
    public function hd_city($arr=array()){
        $where['status'] = array('eq',1);
        if(!empty($arr)){
            $where = array_merge($where,$arr);
        }
        $list = M('zone')->where($where)->order('id desc')->select();
        return $list;
    }

    //获取报名序号
    public function get_number($activity_id,$type=''){

        $vo = M('activity_baoming')->where(array('activity_id'=>$activity_id,'type'=>$type))->order('number desc')->find();
        return intval($vo['number'])+1;
    }


    //添加报名
    public function add_apply($post){
        //获取活动信息
        $hd = M('activity')->find($post['activity_id']);

        $where['uid'] = array('eq',$post['uid']);
        $where['activity_id'] = array('eq',$post['activity_id']);
        $vo = M('activity_baoming')->where($where)->order('id desc')->find();
        if(empty($vo)){
            $post['create_time'] = time();
            $post['number'] = D('Activity')->get_number($post['activity_id'],1);
            $res = M('activity_baoming')->add($post);
        }else{
            if($vo['type']==1 && $hd['bstype']==2){
                $post['create_time'] = time();
                $post['number'] = D('Activity')->get_number($post['activity_id'],2);
                $post['type'] = 2; //复赛阶段
                //添加复赛报名
                $baoming = M('activity_baoming');
                $order = M('order');
                $baoming->startTrans(); $order->startTrans();  // 开启事务
                $res = $baoming->add($post);

                //添加订单
                $datat['order_number'] = order_number('order');
                $datat['uid'] = $post['uid'];
                $datat['activity_id'] = $post['activity_id'];
                $datat['baoming_id']  = $res;
                $datat['total_price'] = $hd['price'];
                $datat['pay_price']   = $hd['price'];
                $datat['pay_status']  = $hd['price']>0 ? 0 :1; //价格为0则免费，默认支付成功
                $datat['create_time'] = time();
                $res2 = $order->add($datat);
                if($res && $res2){
                    $baoming->commit(); $order->commit();  // 开启事务
                    $result['order_number'] = $datat['order_number'];
                    $result['pay_status']   = $datat['pay_status'];
                    $result['share_url']    ='/Home/Share/money_page?ordernumber='.$datat['order_number']; //支付成功界面分享链接
                }else{
                    $baoming->rollback(); $order->rollback();  // 开启事务
                    return array('code'=>201,'msg'=>'报名失败！');
                }

            }else {
                return array('code'=>201,'msg'=>'不能重复报名！');
            }
        }

        if($res){
            //获取优惠券
            $data['uid'] = $post['uid'];
            $data['activity_id'] = $post['activity_id'];
            D('Activity')->get_coupon($data);
            $result['baoming_id'] =  $res; //报名id
            $result['activity_id'] =  $post['activity_id']; //活动id
            return array('code'=>200,'msg'=>'报名成功！','data'=>$result);
        }else{
            return array('code'=>201,'msg'=>'报名失败！');
        }
    }

    //编辑报名
    public function edit_apply($post){

        $post['update_time'] = time();
        $res = M('activity_baoming')->save($post);

        if($res){
            return array('code'=>200,'msg'=>'编辑成功！');
        }else{
            return array('code'=>201,'msg'=>'编辑失败！');
        }
    }


    //添加优惠券
    public function get_coupon($data=array()){
        $month_date = date('Y-m');
        $whe['month_date'] = array('eq',$month_date);
        $whe['end_time']   = array('gt',time());
        $whe['status']     = array('eq',1);
        $vo = M('coupon')->where($whe)->find();
        if(!empty($vo)){
            $data['coupon_id'] = $vo['id'];
            $data['get_time']  = time();
            $data['coupon_code'] = D('Activity')->coupon_code();
            $res = M('coupon_numcode')->add($data);
            if($res){
                return array('code'=>200,'msg'=>'领取成功！');
            }else{
                return array('code'=>201,'msg'=>'领取失败！');
            }
        }
    }

    //递归生成优惠券码
    public function coupon_code(){
        $coupon_code = rand(1000,9999).time();
        $whe['coupon_code']   = array('eq',$coupon_code);
        $vo = M('coupon_numcode')->where($whe)->find();
        if(!empty($vo)){
            coupon_code();
        }else{
            return $coupon_code;
        }
    }

    //第二次参赛时判断是否支付成功
    public function check_paystatus($uid,$activity_id='',$baoming_id=''){
        $where['uid'] = array('eq',$uid);
        $where['activity_id'] = array('eq',$activity_id);
        $where['baoming_id'] = array('eq',$baoming_id);
        $vo = M('order')->where($where)->find();
        if($vo['pay_status']==1){
            return 200;
        }else{
            return 201;
        }
    }


    //上传作品
    public function add_works($post){

        if($post['type']==2){
           $retu = $this->check_paystatus($post['uid'],$post['activity_id'],$post['baoming_id']);
           if($retu==201){
               return array('code'=>201,'msg'=>'必须支付成功才可以上传作品！');
           }
        }

        $where['type'] = array('eq',$post['type']);
        $where['uid']  = array('eq',$post['uid']);
        $where['activity_id']  = array('eq',$post['activity_id']);
        $where['baoming_id']  = array('eq',$post['baoming_id']);
        $vo = M('activity_baoming')->where($where)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'请先报名！');
        }

         $post['update_time'] = time();
        /* $post['imgarr']      = $post['imgarr'];
         $post['videoarr']    = $post['videoarr'];*/
         $post['id']          = $post['baoming_id'];
         $res = M('activity_baoming')->save($post);
        if($res){
            return array('code'=>200,'msg'=>'提交成功！');
        }else{
            return array('code'=>201,'msg'=>'提交失败！');
        }
    }

   //投票
    public function add_nums($post){
        $baoming = M('activity_baoming')->find($post['baoming_id']);//报名表信息
        if($post['uid']==$baoming['uid']){
            return array('code'=>201,'msg'=>'不能给自己投票！');
        }
        $whe['uid'] = array('eq',$post['uid']);
        $whe['province_id'] = array('eq',$baoming['province_id']);
        $whe['daydate'] = array('eq',date('Ymd'));
        $count = M('addnum_recode')->where($whe)->count(); //投票数量


        $where['id'] = array('eq',$post['uid']);
        $user = M('usermember')->where($where)->find();
        if($user['teyue_type']==2){
            if($count>=C('WEB_YOUXUAN_NUMS')){
                return array('code'=>201,'msg'=>'今天投票次数已用完，请明天再来！');
            }
        }else{
            if($count>=C('WEB_PUTONG_ADDNUM')){
                return array('code'=>201,'msg'=>'今天投票次数已用完，请明天再来！');
            }
        }

        $where['id'] = array('eq',$post['baoming_id']);
        $where['activity_id'] = array('eq',$post['activity_id']);
        if($user['teyue_type']==2){
            $res1 = M('activity_baoming')->where($where)->setInc('zj_num',C('WEB_DIYONG_NUMS')); //专家票
        }else{
            $res1 = M('activity_baoming')->where($where)->setInc('pu_num',1);//普通票
        }

        //添加投票记录
        $data['baoming_id']  = $post['baoming_id'];
        $data['activity_id'] = $post['activity_id'];
        $data['uid']         = $post['uid'];
        $data['btp_uid']     = $baoming['uid'];
        $data['province_id'] = $baoming['province_id'];
        $data['create_time'] = time();
        $data['daydate']     = date('Ymd');
        $res2 = M('addnum_recode')->add($data);

        if($res1){
            return array('code'=>200,'msg'=>'投票成功！');
        }else{
            return array('code'=>201,'msg'=>'投票失败！');
        }

    }

    //我的优惠券
    public function coupon_list($post){
        $post['type'] = $post['type']>0 ? $post['type'] : 1;
        $where['n.is_use'] = array('eq',0);
        if($post['type']==1){
            $where['c.end_time'] = array('gt',time());
        }else if($post['type']==2){
            $where['c.end_time'] = array('elt',time());
        }
        $where['n.uid'] = array('eq',$post['uid']);
        $list = M('coupon as c')->join('bhy_coupon_numcode as n on c.id = n.coupon_id')->where($where)->field('c.*,n.coupon_code')->order('id desc')->select();

        return array('code'=>200,'msg'=>'成功！','data'=>$list,'count'=>count($list));
    }


    //作品列表
    public function works_list($post){
        if($post['sc_type']==1){
            $where['type'] = array('eq', 1);
        }else if($post['sc_type']==4 || $post['sc_type']==5){
            $where['type'] = array('eq', 2);
            $type=2;
        }else{
            return array('code'=>201,'msg'=>'缺少参数！');
        }

        if(trim($post['keywords'])!=''){
            $where['username|mobile'] = array('like','%'.trim($post['keywords']).'%');
        }
        if($post['province_id']>0){
            $where['province_id'] = array('eq',$post['province_id']);
        }
        $where['status'] = array('eq', 1);
        $where['activity_id'] = array('eq', $post['activity_id']);
        $where['_string'] = "(imgarr!='' or videoarr!='')";

        $num = $post['num']>0 ? $post['num'] :20;
        $page = $post['page']>1?($post['page']-1)*$num:0;

        if($type==2){
            //复赛
            $where['ranking'] = array('gt', 0);
            $sort = 'ranking asc';
            $lista = M('activity_baoming')->where($where)->field('*,(pu_num+zj_num) as total_num')->order($sort)->select();
            if(!empty($lista)){
                $idarr = implode(',',array_column($lista,'id'));
                $where['id'] = array('not in',$idarr);//查询未排名的数据
            }
            unset($where['ranking']);
            $listb = M('activity_baoming')->where($where)->field('*,(pu_num+zj_num) as total_num')->order('id desc')->limit($page,$num)->select();
			
			if(!empty($lista) && empty($listb)){
				
				$list = $lista;
				
			}elseif(!empty($lista) && empty($listb)){
				
				$list = $listb;
				
			}else{
				
				 $list = array_merge($lista,$listb);
				
			}

           if($page > 1){
			   
			   $list = '';
		   }

        }else{
            $sort = 'id desc';
            $list = M('activity_baoming')->where($where)->field('*,(pu_num+zj_num) as total_num')->order($sort)->limit($page,$num)->select();
        }
       foreach ($list as $ke=>$ve){
           $list[$ke]['iconarr'] = imgarr($ve['iconarr']);
           $list[$ke]['imgarr']  = imgarr($ve['imgarr']);
       }
        return array('code'=>200,'msg'=>'成功！','data'=>$list);
    }

//作品详情
    public function works_details($post){
        $where['id'] = array('eq', $post['baoming_id']);
        $vo = M('activity_baoming')->where($where)->field('*,(pu_num+zj_num) as total_num')->find();
        $vo['province'] = modelField($vo['province_id'],'zone','name');
        $vo['city']     = modelField($vo['city_id'],'zone','name');
        $vo['iconarr']     = imgarr($vo['iconarr']); //个人照
        $vo['imgarr']     = imgarr($vo['imgarr']); //作品照片
        $videoarr     = videoarr($vo['videoarr']);//作品视频
        $video_imgarr     = imgarr($vo['video_imgarr']); //作品视频图片
        foreach ($videoarr as $ke=>$ve){
            $videoarr[$ke]['video_imgarr'] = $video_imgarr[$ke]['imgurl'];
        }
        $vo['videoarr'] = $videoarr;
        $vo['share_url']  = '/Home/Fenxiao/details?visit_uid='.$post['uid'].'&bao_id='.$vo['id']; //分享链接
        return $vo;
    }

    //每关对应的选项,$passlist_id:关卡id,$gktmid:每关分配的题目记录表id
    public function pass_xuanxiang($passlist_id,$gktmid=''){
        $where['p.passlist_id'] = array('eq',$passlist_id);
        if($gktmid>0){
            $where['p.id'] = array('eq',$gktmid);
            $list = M('passlist_topic as p')->join('bhy_topic as t on t.id = p.topic_id')->where($where)->field('t.*,p.id as gktmid')->find();
        }else{
            $list = M('passlist_topic as p')->join('bhy_topic as t on t.id = p.topic_id')->where($where)->field('t.*,p.id as gktmid')->order('p.id asc')->select();
        }
        return $list;
    }

    //获取当前人需要答的题目，$passlist_id:关卡id
    public function get_topic($passlist_id,$uid='',$activity_id=''){
        $where['p.passlist_id'] = array('eq',$passlist_id);
        $where['p.uid'] = array('eq',$uid);
        $where['p.activity_id'] = array('eq',$activity_id);
       // $where['p.answer'] = array('exp','is null');
        $where['_string'] = '(p.answer is null or p.answer_status=2)';
        $vo = M('person_answer as p')->join('bhy_passlist_topic as a on a.id = p.pass_topicid')->join('bhy_topic as t on t.id = p.topic_id')->where($where)->field('t.title,p.pass_topicid,p.topic_id,a.xuhao,p.id as person_listid,p.activity_id,p.passlist_id')->order('a.xuhao asc')->find();
        //return array('code'=>200,'msg'=>'获取题目成功！','data'=>M('person_answer as p')->getLastSql());
        if(empty($vo)){
            return array('code'=>201,'msg'=>'题目不存在！');
        }
        M('person_answer')->where(array('id'=>$vo['person_listid']))->save(array('timea'=>time())); //更新开始进入答题时间

        $whe['topic_id'] = array('eq',$vo['topic_id']);
        $vo['son_list'] = M('topic_xuanxiang')->where($whe)->order('xx_zimu asc')->select();
        return array('code'=>200,'msg'=>'获取题目成功！','data'=>$vo);
    }

    //用户第一次进入时添加关卡
    public function add_user_pass($list=array(),$uid='',$activity_id=''){
        $where['uid']         = array('eq', $uid);
        $where['activity_id'] = array('eq',$activity_id);
        $vo = M('person_pass')->where($where)->find();
        if(empty($vo)){
            foreach ($list as $ke=>$ve){
                $data[$ke]['activity_id'] = $activity_id;
                $data[$ke]['passlist_id'] = $ve['id'];
                $data[$ke]['uid']         = $uid;
                $data[$ke]['create_time'] = time();
            }
            $res = M('person_pass')->addAll($data);
            return 1;
        }else{
            return 1;
        }

    }



    //进入关卡
    public function enter_pass($post){
        $where['passlist_id'] = array('eq', $post['passlist_id']);
        $where['uid']         = array('eq', $post['uid']);
        $where['activity_id'] = array('eq', $post['activity_id']);
        $vo = M('person_pass')->where($where)->find();

        if(!empty($vo)){
            if(empty($vo['starttime']) || $vo['starttime']==0){
                $person_pass =  M('person_pass');
                $person_answer =  M('person_answer');
                $person_pass->startTrans(); $person_answer->startTrans();  // 开启事务

                $where['activity_id'] = $post['activity_id'];
                $where['passlist_id'] = $post['passlist_id'];
                $where['uid']         = $post['uid'];
                $data['starttime']   = time();
                $res1 = $person_pass->where($where)->save($data);

                //在当前人下添加当前关卡对应的题目
                $xxlist = $this->pass_xuanxiang($post['passlist_id']);
                foreach ($xxlist as $ke=>$ve){
                    $datat[$ke]['person_passid'] = $res1;
                    $datat[$ke]['uid']           = $post['uid'];
                    $datat[$ke]['passlist_id']   = $post['passlist_id'];
                    $datat[$ke]['pass_topicid']  = $ve['gktmid'];
                    $datat[$ke]['topic_id']      = $ve['id'];
                    $datat[$ke]['activity_id']   = $post['activity_id'];
                    $datat[$ke]['timea']         = $ke==0 ? time() : 0;
                }
                $res2 = $person_answer->addAll($datat);
                if($res1 && $res2){
                    $person_pass->commit(); $person_answer->commit();  // 开启事务
                    $vot = $this->get_topic($post['passlist_id'],$post['uid'],$post['activity_id']); //将第一题返回
                    return array('code'=>200,'msg'=>'成功！','data'=>$vot['data']);
                }else{
                    $person_pass->rollback(); $person_answer->rollback();  // 开启事务
                    return array('code'=>201,'msg'=>'未知错误！');
                }
            }else{
                $vot = $this->get_topic($post['passlist_id'],$post['uid'],$post['activity_id']); //将第一题返回

                return array('code'=>200,'msg'=>'成功！','data'=>$vot['data']);
            }

        }else{

            return array('code'=>201,'msg'=>'未知错误！');
        }
    }

    //提交答案
    public function add_answer($post){
        $whe['t.id'] = array('eq',$post['topic_id']);
        $whe['p.id'] = array('eq',$post['person_listid']);
        $whe['p.uid'] = array('eq',$post['uid']);
        $topic = M('person_answer as p')->join('bhy_topic as t on t.id = p.topic_id')->field('p.*,t.answer as zqdan')->where($whe)->find();

        if($topic['zqdan']!=$post['answer']){
            $data['answer_status'] = 2;
            //查找正确答案的选项标题
            $whert['xx_zimu'] = array('eq',$topic['zqdan']);
            $whert['topic_id'] = array('eq',$topic['topic_id']);
            $xx = M('topic_xuanxiang')->where($whert)->find();
            $result['sure_answer']   = $topic['zqdan'];
            $result['sure_title']    = $xx['xuanxiang'];
        }else{
            $data['answer_status'] = 1;
        }
        $data['answer'] = $post['answer'];
        $data['timeb']         = time();
        $data['longtimeb']     = time()-$topic['timea']; //时长
        $where['id'] = array('eq',$post['person_listid']);
        $where['uid'] = array('eq',$post['uid']);
        $res = M('person_answer')->where($where)->save($data);

        if($res){
            $result['answer_status'] = $data['answer_status']; //1正确，2错误
            $result['longtime']      = $data['longtimeb'];
           // $result['passlist_id']      = $topic['passlist_id'];
            //$result['activity_id']      = $topic['activity_id'];
            if($topic['zqdan']==$post['answer']){
                //判断是否是最后一题
                $code = $this->check_last($post['passlist_id'],$post['uid'],$post['activity_id']);
                if( $code['code']==1){
                    $result['longtime'] = $this->my_total_time($post['uid'],$post['activity_id'],$post['passlist_id']); //本关时长
                }
                $result['end_status']      = $code['code'];
                $result['next_passlist_id'] = $code['next_passlist_id'];
                $result['activity_id']      = $post['activity_id'];
                $result['ad_url']          = $code['ad_url'];
             }
            return array('code'=>200,'msg'=>'提交成功！','data'=>$result);
        }else{
            return array('code'=>201,'msg'=>'提交失败！');
        }
    }

    //查询全部关卡是否答完
    public function end_allpass($activity_id,$uid){
        $whe['activity_id'] = array('eq',$activity_id);
        $whe['uid']         = array('eq',$uid);
        $whe['endtime']     = array(array('exp','is null'),array('eq',0),'or');
        $countb = M('person_pass')->where($whe)->order('id asc')->find();
        return $countb;
    }

    //判断关卡是否解锁
    public function check_lock($activity_id,$passlist_id,$uid){
        $whest['b.activity_id'] = array('eq',$activity_id);
        $whest['b.passlist_id'] = array('eq',$passlist_id);
        $whest['b.uid']         = array('eq',$uid);
        $vo = M('passlist as p')->join('bhy_person_pass as b on b.passlist_id = p.id')->field('p.*,b.endtime')->where($whest)->find();

        if(!empty($vo['endtime']) && $vo['endtime']>0){
            $lock = 2; //本关已做完
        }else{
            $whestt['p.passlist_id'] = array('eq',$passlist_id);
            $whestt['p.activity_id'] = array('eq',$activity_id);
            $whestt['p.uid']         = array('eq',$uid);
            $vot = M('person_pass as p')->join('bhy_person_answer as b on b.person_passid = p.id')->where($whestt)->find();
            if(empty($vot)){
                $wheste['b.activity_id'] = array('eq',$activity_id);
                $wheste['p.numb']        = array('lt',$vo['numb']);
                $wheste['b.uid']         = array('eq',$uid);
                $vtt = M('passlist as p')->join('bhy_person_pass as b on b.passlist_id = p.id')->where($wheste)->order('numb desc')->field('b.endtime')->find();
                if(!empty($vtt['endtime'])){
                    $lock = 1; //已解锁
                }else{
                    $lock = 0; //未解锁
                }
            }else{
                $lock = 1; //本关未做完
            }
        }
        return $lock;
    }


    //判断本关的题目是否答完
    public function check_last($passlist_id,$uid,$activity_id=''){

        $where['passlist_id'] = array('eq',$passlist_id);
        $where['uid']         = array('eq',$uid);
        $where['_string']      = '(answer is null or answer="" or answer_status=2)';
        $vo = M('person_answer')->where($where)->find();
        if(empty($vo)){
            $whe['uid'] = array('eq',$uid);
            $whe['passlist_id'] = array('eq',$passlist_id);
            $whe['activity_id'] = array('eq',$activity_id);
            M('person_pass')->where($whe)->save(array('endtime'=>time()));//修改结束时间

            $ada = M('passlist')->find($passlist_id);
            $data['ad_url'] = get_icon_img('',$ada['icon2']);
            //判断是否全部关卡都答完
            $countb = $this->end_allpass($activity_id,$uid);
            //$data['code'] = $countb;//全部关卡已完
            //return $data;
            if(!empty($countb)){
                $data['code'] = 1;//本关已答完
                $data['next_passlist_id'] = $countb['passlist_id'];//下一关的关卡id
            }else{
                $total_long = $this->my_total_time($uid,$activity_id); //我的总时长
                M('activity_baoming')->where(array('activity_id'=>$activity_id,'uid'=>$uid))->save(array('total_long'=>$total_long));//更改报名表的时长值
                $data['code'] = 3;//全部关卡已完
            }
        }else{
            $ada = M('passlist')->find($passlist_id);
            $data['ad_url'] = get_icon_img('',$ada['icon']);
            $data['code'] = 2;//未答完
        }
        return $data;
    }

    //知识闯关排行榜
    public function pass_sort($post){
        $num = $post['num']>0 ? $post['num'] :5;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $result['my_sort'] = $this->my_sort('',$post['activity_id']); //我的名次

        $list = $this->my_sort($post['uid'],$post['activity_id'],$page,$num);
        foreach ($list as $ke=>$ve){
            $iconimg = explode(',',$ve['iconarr']);
            $list[$ke]['head_imgurl'] = get_icon_img('/Public/Home/toux.png',$iconimg[0]);
        }
        $result['list'] = $list;
        return array('code'=>200,'msg'=>'成功！','data'=>$result);
    }

    //查询自己的排名
    public function my_sort($uid,$activity_id='',$page='',$num=''){
        $where['a.activity_id'] = array('eq',$activity_id);
        $where['p.endtime']     = array('neq','');
        $where['p.end_type']    = array('eq',1);
        if($uid>0){
            $list = M('activity_baoming as b')->join('bhy_person_pass as p on p.uid = b.uid')->join('bhy_person_answer as a on p.id = a.person_passid')->where($where)->field('sum(a.longtimeb) as longtimeb,p.uid')->order('longtimeb asc')->group('a.uid')->select();
            foreach ($list as $ke=>$ve){
                if($uid==$ve['uid']){
                    return ($ke+1);exit; //返回我的名次
                }
            }
        }else{
            $list = M('activity_baoming as b')->join('bhy_person_pass as p on p.uid = b.uid')->join('bhy_person_answer as a on p.id = a.person_passid')->where($where)->field('sum(a.longtimeb) as longtimeb,p.uid,b.username,b.iconarr')->order('longtimeb asc')->group('a.uid')->limit($page,$num)->select();
            return $list;
        }
    }

    //我的总时长
    public function my_total_time($uid,$activity_id='',$passlist_id=''){
        if($passlist_id>0){
            $where['a.passlist_id'] = array('eq',$passlist_id); //查询本关的时长
        }
        $where['a.activity_id'] = array('eq',$activity_id);
        $where['p.endtime']     = array('neq','');
        $where['a.uid']         = array('eq',$uid);
        $vo = M('person_answer as a')->join('bhy_person_pass as p on p.id = a.person_passid')->where($where)->field('sum(a.longtimeb) as longtimeb')->find();
        return $vo['longtimeb'] > 0 ? $vo['longtimeb'] : 0;
    }


    //星星时间
    public function star_num($uid,$activity_id='',$passlist_id=''){
        $where['activity_id'] = array('eq',$activity_id);
        $where['passlist_id']     = array('eq',$passlist_id);
        $where['uid']         = array('eq',$uid);
        $vo = M('person_answer')->where($where)->find();
        $longtimeb = $vo['longtimeb'];
        if($longtimeb>0 && $longtimeb<=180){
            return 3;
        }else if($longtimeb>180 && $longtimeb<=600){
            return 2;
        }else if($longtimeb>600){
            return 1;
        }else{
            return 0;
        }
    }



}

?>
