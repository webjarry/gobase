<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model;

/**
 * Description of NewsModel
 *
 * @author Administrator
 */
class NewsModel {
    
    //资讯列表
    public function news_list($post){
        $type = $post['type'];
        if($type==40 || $type==''){
            $where = "typeid=40 and status=1";
            if($page!=''){
                $end = $page*10;
                $start = $end-10;
            }else{
                $start = 0;
                $end = 9;
            }
            $result = M('article')->where($where)->field("id,title,status,icon,create_time")->order("create_time desc")->limit($start,10)->select();
            foreach($result as $k=>$v){
                $result[$k]['icon'] = picture($v['icon']);
            }
        }else if($type==39){
            $where = "typeid=39 and status=1";
            $result = M('article')->where($where)->field("title,icon,create_time,content")->order("create_time desc")->find();
            $result['icon'] = picture($result['icon']);
        } 
        
        
        if(!empty($result)){
            
            return array('status'=>true,'msg'=>'查询成功','code'=>200,'data'=>$result);
        }else{
            return array('status'=>false,'msg'=>'查询失败','code'=>201,'data'=>$result);
        }
    }
    
    //资讯详情
    public function news_detile($post){
        $id = $post['id'];
        $result = M('article')->where(array('id'=>$id,'status'=>1))->find();
        $result['icon'] = picture($result['icon']);
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
}
