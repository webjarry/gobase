<?php

namespace App\Model;
use Think\Model;

/**
 * 用户个人中心模型
 */
class ZongcouModel extends Model {

    //人员关系列表
    public function person_type(){
        $list = M('category')->where(array('status'=>1,'pid'=>153))->order('sort asc')->select();
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }

    //添加证明
    public function add_msg($post){
        if(empty($post['zc_id']) || empty($post['relation_id']) || empty($post['username']) || empty($post['uid']) || empty($post['content']) || empty($post['code']) ){
            return array('code'=>201,'msg'=>'缺少参数');
        }
		
		$map = array();
		$map['zc_id'] = array('eq',$post['zc_id']);
		$map['uid'] = array('eq',$post['uid']);
		
		$result = M('zongcou_msg')->where($map)->find();
		
		if($result){
			return array('code'=>201,'msg'=>'您已经提交过证明留言，不能重复留言！');
			
		}
		
        $post['create_time'] = time();
        $res = M('zongcou_msg')->add($post);
        if($res){
            return array('code'=>200,'msg'=>'提交成功');
        }else{
            return array('code'=>201,'msg'=>'提交失败');
        }
    }

    //统计已筹金额
    public function count_price($zc_id,$money=''){
        $where['tid'] = array('eq',$zc_id);
        $where['type'] = array('eq',15);
        $where['status'] = array('eq',1);
        $vo = M('order')->where($where)->field('sum(pay_price) as total_price')->find();
        $result['total_price'] = $vo['total_price'] >0? $vo['total_price']:0; //已筹金额
        $scale= sprintf("%.2f", (($result['total_price']/$money)*100)).'%';
        $result['scale'] = $result['total_price']>=$money ? '100%' : $scale;
        return $result;
    }


    //众筹详情
    public function details($post){
        if(empty($post['zc_id'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $vo = M('zc')->find($post['zc_id']);
        $imgarr = explode(',',$vo['icon']);
        $vo['imgurl'] = get_icon_img('',$imgarr[0]);
        $vo['iczm_url'] =  get_icon_img('',$vo['iczm']);
        $vo['icfm_url'] =  get_icon_img('',$vo['icfm']);
        $vo['icon_arr'] =  imgarr($vo['icon']);
		
		if(time()> $vo['lasttime']){
			
			$vo['status'] = 4;
			
		}
        $result['info'] = $vo;//众筹信息
        $result['counts'] = $this->count_price($post['zc_id'],$vo['money']); //众筹统计

        //委托律师
        $whes['w.tid'] = array('eq',$post['zc_id']);
        $whes['j.choose'] = array('eq',1);
        $wt = M('wt as w')->join('left join bhy_wtjoin as j on j.tid = w.id')->where($whes)->field('w.orderno,j.uid,j.money')->find();
        if(!empty($wt['uid'])){
            $result['lvshi_info']           = sinfo($wt['uid']);
            $result['lvshi_info']['oderno'] = $wt['orderno'];
            $result['lvshi_info']['money']  = $wt['money'];
        }
        return array('code'=>200,'msg'=>'成功','data'=>$result);

    }
	
	//获取众筹余额
	public function zcBalance($uid){
		
		$map = array();
		$map['uid'] = array('eq',$uid);
		$map['status'] = array('egt',2);
		
		$list = M('zc')->where($map)->order('id desc')->select();
		$total_price = 0;
		foreach($list as $k =>$v){
			
			if(time() > $v['lasttime']){
				$zong = D('Zongcou')->count_price($v['id'],$v['money']);
				$total_price+=$zong['total_price'];
			}

		}
		$map = array();
		$map['utype'] = array('eq',1);
		$map['uid'] = array('eq',$uid);
		$map['type'] = array('eq',1);
		$map['status'] = array('in','1,2');
		
		$txList = M('tixian')->where($map)->field('sum(money) as total_price')->find();
		$txList['total_price'] = empty($txList['total_price'])?0:$txList['total_price'];
		return $total_price - $txList['total_price'];
		
		
	}


    //证明留言列表
    public function msg_list($post){
        if(empty($post['zc_id'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $where['z.zc_id'] = array('eq',$post['zc_id']);
        $listcount = M('zongcou_msg as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->count();//留言数量

        $num = $post['num']>0?$post['num']:20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = M('zongcou_msg as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->field('z.*,u.phone,u.nickname,u.icon')->limit($page,$num)->order('z.id desc')->select();
        foreach ($list as $ke=>$ve){
            if(is_numeric($ve['icon'])){
                $list[$ke]['head_img'] = get_icon_img('',intval($ve['icon']));
            }else{
                $list[$ke]['head_img'] = $ve['icon'];
            }
			$list[$ke]['create_time'] = date('Y-m-d H:i',$ve['create_time']);
        }
        $result['listcount'] = $listcount; //总数量
        $result['list'] = $list;
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }

   /* //爱心帮助记录列表
    public function order_list($post){
        if(empty($post['zc_id'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $where['z.zc_id'] = array('eq',$post['zc_id']);
        $where['z.pay_status'] = array('eq',1);
        $listcount = M('zongcou_order as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->count();//捐款数量

        $num = $post['num']>0?$post['num']:20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = M('zongcou_order as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->field('z.*,u.phone,u.nickname,u.icon')->limit($page,$num)->order('z.id desc')->select();
        foreach ($list as $ke=>$ve){
            if(is_numeric($ve['icon'])){
                $list[$ke]['head_img'] = get_icon_img('',intval($ve['icon']));
            }else{
                $list[$ke]['head_img'] = $ve['icon'];
            }
        }
        $result['share_count'] = D('App/Zongcou')->share_count($post['zc_id']); //分享次数
        $result['listcount'] = $listcount; //总数量
        $result['list'] = $list;
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }*/

    //爱心帮助记录列表
    public function order_list($post){
        if(empty($post['zc_id'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $where['z.tid']  = array('eq',$post['zc_id']);
        $where['z.status'] = array('eq',1);
        $where['z.type']   = array('eq',15);
        $listcount = M('order as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->count();//捐款数量

        $num = $post['num']>0?$post['num']:20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = M('order as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->field('z.*,u.phone,u.nickname,u.icon')->limit($page,$num)->order('z.id desc')->select();
		$sql = M('order as z')->getLastSql();
        foreach ($list as $ke=>$ve){
            if(is_numeric($ve['icon'])){
                $list[$ke]['head_img'] = get_icon_img('',intval($ve['icon']));
            }else{
                $list[$ke]['head_img'] = $ve['icon'];
            }
			$list[$ke]['create_time'] = date('Y-m-d H:i',$ve['create_time']);
        }
        $result['share_count'] = D('App/Zongcou')->share_count($post['zc_id']); //分享次数
        $result['listcount'] = $listcount; //总数量
        $result['list'] = $list;
        return array('code'=>200,'msg'=>'成功','data'=>$result,'sql'=>$sql);
    }



    //分享次数
    public function share_count($zc_id){

        $count = M('zongcou_share')->where(array('zc_id'=>$zc_id))->count();
        return $count;
    }


    //支付页面信息
    public function pay_page($post){
        if(empty($post['zc_id'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $where['z.id'] = array('eq',$post['zc_id']);
        $vo = M('zc as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->field('z.*,u.phone,u.nickname,u.icon')->find();
        if(is_numeric($vo['icon'])){
            $vo['head_img'] = get_icon_img('',intval($vo['icon']));
        }else{
            $vo['head_img'] = $vo['icon'];
        }
        return array('code'=>200,'msg'=>'成功','data'=>$vo);
    }


    //分享记录接口
    public function add_share($post){
        if(empty($post['zc_id']) || empty($post['uid'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        $post['create_time'] = time();
        $res = M('zongcou_share')->add($post);
        if($res){
            return array('code'=>200,'msg'=>'添加成功');
        }else{
            return array('code'=>201,'msg'=>'添加失败');
        }
    }


    //提交支付
    public function add_order($post){
        $price = floatval($post['pay_price']);
        if(empty($post['zc_id']) || empty($post['uid']) || empty($post['pay_type'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }
        if($price<=0){
            return array('code'=>201,'msg'=>'金额必须大于0');
        }
        //添加order表(为了不影响之前同事做的，所以特地加的)
        $user = session('user');
        $order['orderno'] = orderNumber();
        $order['type']    = 15;
		$order['tid'] = $post['zc_id'];
        $order['uid']     = $post['uid'];
        $order['utype']   = $user['type'];
        $order['pay_price'] = $post['pay_price'];
        $order['pay_type'] = $post['pay_type'];
		$order['content'] = $post['content'];
        $order['create_time'] = time();
        $order['date_time'] = date('Y-m');
        $re=M('order')->add($order);


        $post['order_number'] = $order['orderno'];//D('App/Zongcou')->ordernumber('zongcou_order'); //订单号
        $post['create_time']  = time();
        $post['order_id']     = $re;
        $res = M('zongcou_order')->add($post);
        if($res){
            $result['order_number'] = $post['order_number'];
            $result['order_id']     = $res;
            return array('code'=>200,'msg'=>'订单添加成功','data'=>$result);
        }else{
            return array('code'=>201,'msg'=>'订单添加失败');
        }
    }

    //捐款订单号
    public function ordernumber($model){
        $rand = rand(100000,999999).date('YmdHis');
        $where['order_number'] = array('eq',$rand);
        $vo = M($model)->where($where)->find();
        if(!empty($vo)){
            order_number($model);
        }else{
            return $rand;
        }
    }


	//提现记录
	public function txList($post,$uid){
		
		$map = array();
		$map['type'] = array('eq',1);
		$map['uid'] = array('eq',$uid);
		$map['utype'] = array('eq',1);
		
		$list = M('tixian')->where($map)->order('create_time desc')->limit(($post['page']-1)*$limit,$limit)->select();
		
		foreach($list as $k => $v){
			
			$list[$k]['status_title'] = tx_result($v['status']);
			$list[$k]['create_time'] = date('Y-m-d H:i');
			
		}
		
		return array('code'=>200,'msg'=>'数据获取成功','data'=>$list);
		
	}





}

?>
