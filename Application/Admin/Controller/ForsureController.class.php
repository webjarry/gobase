<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 */
class ForsureController extends AdminController {

    public function index(){
        $get = $_GET;
        $this->assign('get',$get);

        $time = timeCondition($get['date1'],$get['date2']);
        if($time!=0){
            $where['z.create_time']=$time;
        }
        if($get['zc_id']>0){
            $where['z.zc_id'] = array('eq',$get['zc_id']);
        }
        $where['z.status'] = array('gt',-1);
        //dump($map);
        $listsCount = M('zongcou_msg as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->count();
        $Page       = new \Think\Page($listsCount,10);
        $show       = $Page->show();

        $list = M('zongcou_msg as z')->join('bhy_usermember as u on u.id = z.uid')->where($where)->field('z.*,u.phone,u.nickname,u.icon')->limit($Page->firstRow.','.$Page->listRows)->order('z.id desc')->select();

        $this->assign('_page',$show);
        $this->assign('list', $list);

        $this->display();
    }


}
