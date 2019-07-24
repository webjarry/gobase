<?php

namespace App\Controller;

class NewsController extends HomeController {

    public $param;

    public function _initialize() {

        parent::_initialize();

        $list = file_get_contents('php://input');
        $param = json_decode($list, true);
        $this->param = $param;
    }
    
    //资讯列表
    public function news_list(){
        $post = $this->param;
        
        if( !$post['type']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('News')->news_list($post);
        $this->appReturn($result);
    }
    
    //资讯详情
    public function news_detile(){
        $post = $this->param;
        
        if( !$post['id']){
            $this->appReturn(array('status'=>FALSE,'code'=>201,'msg'=>'缺少参数'));
        }
        $result = D('News')->news_detile($post);
        $this->appReturn($result);
    }

    
    

}
