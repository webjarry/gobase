<?php

namespace App\Controller;

class IndexController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
    }
    
 
    //首页数据
    public function index(){
        $post = $this->param;
        if(!$post['city_id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('Index')->index($post);
        $this->appReturn($result);
    }
    
    
}
