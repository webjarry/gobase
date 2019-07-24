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
class TuwenController extends HomeController {

   
	public function detail(){

        $vo = M('goods')->where("id=".$_GET['id'])->find();

        $icons=$vo[imgarr];
        if(strpos($icons,',')){
            $s=explode(',',$icons);
        }else{
            $s=array($icons);
        }
        $vo[icons]=$s;

        $this->assign('vo',$vo);


        $this->display();

	}


    public function help(){

        $vo = M('help')->where("id=".$_GET['id'])->find();


        $this->assign('vo',$vo);


        $this->display();

    }


}