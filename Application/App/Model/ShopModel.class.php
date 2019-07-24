<?php

namespace Common\Model;

/**
 * 用户个人中心模型
 */
class ShopModel {
    
    //分类首页
    public function index($post){
       $city_id = intval($post['city_id']);
       $where = " city_id = ".$city_id." and nums>0 and status=1";
       if(!empty($post['fid'])&&$post['fid']!=0){
           $fid = intval($post['fid']);
           $where = $where." and f_classify_id = ".$fid;
           $result['z_classify'] = M('classify')->where(array('fid'=>$fid))->field("id,title,icon")->order('sort asc')->select();
       }else if($post['fid']=='0'){

       }else{
           $field = "id,title,icon";
           $result['classify'] = M('classify')->where(array('fid'=>0))->field($field)->order('sort asc')->limit(9)->select();
           $fid = intval($result['classify'][0]['id']);
           $where = $where." and f_classify_id = ".$fid;
           $result['z_classify'] = M('classify')->where(array('fid'=>$fid))->field($field)->order('sort asc')->select();
       }
       
       $result['classify'] = $this->fors($result['classify']);
       
       
       if(!empty($post['zid'])){
           $where = $where." and classify_id = ".intval($post['zid']);
       }
       
       if($post['page']==''){
           $start = 0;
           $end = 14;
       }else{
           $end = intval($post['page'])*15;
           $start = $end-15;
           $end = $end-1;
       }
       
       if($post['fid']==0){
           $result['shop'] = $this->fors(M('shop')->where($where)->field("id,icon,shopname,money")->order('rand(15)')->select());
       }else{
           $result['shop'] = $this->fors(M('shop')->where($where)->field("id,icon,shopname,money")->limit($start,$end)->select());
       }
       return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
    
    //搜索商品
    public function sell($post){
        $city_id = $post['city_id'];
        $where = "bs.city_id = ".$city_id." and bs.status=1 and bs.nums>0";
        if($post['shopname']!=''){
            $shopname = $post['shopname'];
            $where = $where." and bs.shopname like '%$shopname%' ";
        }
        if($post['classify_id']!=''){
            $where = $where." and bs.f_classify_id = ".$post['classify_id'];
        }
        
        $order_by = "bs.create_time desc";
        if($post['type']=='jg_1'){
            $order_by = " bs.money asc";
        }else if($post['type']=='jg_2'){
            $order_by = " bs.money desc";
        }
        
        if($page!=''){
            $end = $page*20;
            $start = $end-20;
        }else{
            $start = 0;
            $end = 20;
        }
        
        
        
        $m = M();
        $result['shop'] = $this->fors($m->query("select bs.id,bs.icon,bs.shopname,bs.money from bhy_shop as bs  where  $where order by $order_by   limit $start,20 "));
        
        foreach($result['shop'] as $k=>$v){
            $result['shop'][$k]['sell'] = $this->yx($v['id']);
        }
        
        if($post['type']=='pl_1'){
            foreach ($result['shop'] as $key=>$value){
                
                $stars = M('comment')->where(array('project_id'=>$value['id']))->sum('stars');
                $result['shop'][$key]['stars'] = intval($stars);
                
                $id[$key] = $value['id'];
                $price[$key] = $stars;
            }
            array_multisort($price,SORT_NUMERIC,SORT_ASC,$result['shop']);
        }else if($post['type']=='pl_2'){
            foreach ($result['shop'] as $key=>$value){
                
                $stars = M('comment')->where(array('project_id'=>$value['id']))->sum('stars');
                $result['shop'][$key]['stars'] = intval($stars);
                
                $id[$key] = $value['id'];
                $price[$key] = $stars;
                
            }
            array_multisort($price,SORT_NUMERIC,SORT_DESC,$result['shop']);
        }else if($post['type']=='xl_1'){
            foreach ($result['shop'] as $key=>$value){
                $id[$key] = $value['id'];
                $price[$key] = $value['sell'];
            }
            array_multisort($price,SORT_NUMERIC,SORT_DESC,$result['shop']);
        }else if($post['type']=='xl_2'){
            foreach ($result['shop'] as $key=>$value){
                $id[$key] = $value['id'];
                $price[$key] = $value['sell'];
            }
            array_multisort($price,SORT_NUMERIC,SORT_DESC,$result['shop']);
        }

        
        return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
    //商品详情页
    public function shop($post){
        $uid = $post['uid'];
        $city_id = $post['city_id'];
        $shop_id = $post['shop_id'];
        
        
        $where = "id = $shop_id and city_id = $city_id and status = 1 and nums>0";
        
        $shop = M('shop')->where($where)->find();//商品
        $icons = explode(",", $shop['icons']);
        foreach($icons as $k=>$v){
            $nicon[$k]=  picture($v);
        }
        $shop['icons'] = $nicon;
        $shop['yx'] = $this->yx($shop_id);//月销
        $shop['comment'] = $this->comment($shop_id);
        
        if($shop){
            $shop['icon'] = picture($shop['icon']);
            $shop_attrs = M('shop_attrs')->where(array('shop_id'=>$shop_id))->select();
            $attrs = array();
            $nums = 0;
            foreach($shop_attrs as $k=>$v){//组装商品属性
                if($v['fid']==0){
                    foreach($shop_attrs as $sk=>$sv){
                        if($sv['fid']!=0&&$sv['fid']==$v['id']){
                            $attrs[$nums] = $v['title'].":".$sv['title'];
                            $nums = $nums+1;
                            break;
                        }
                    }
                }
            }

            $shop['attrs'] = $attrs;
            
            //查询同分类的推荐商品
            $classify_id = $shop['classify_id'];
            $cs = "classify_id = $classify_id and status = 1 and nums>0";
            $tj = M('shop')->where($cs)->field("id,shopname,icon,money")->limit(3)->select();
            $tj = $this->fors($tj);
            $shop['tj'] = $tj;
            
            if($uid!=''){
                $collection = M('collection')->where(array('user_id'=>$uid,'shop_id'=>$shop_id))->find();
                if($collection){
                    $shop['collection'] = 1;
                }else{
                    $shop['collection'] = 0;
                }
            }
            
            return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$shop);
            
        }else{
            return array('status'=>false,'msg'=>'获取商品信息失败','code'=>200,'data'=>$result);
        }
        
    }
   
    
    //查询商品评论
    public function comment($id){
        $field = "bu.mobile,bu.icon,c.content,c.stars,c.create_time";
        $result = $this->fors(M('comment c')->join('bhy_usermember as bu on c.uid=bu.id')->where(array('c.project_id'=>$id,'c.status'=>1))->field($field)->select());
        return $result;
    }

     //月销计算
    public function yx($id){
        $m = M();

        $id = $v['id'];
        $sell = $m->query("select count(id) as sell from bhy_order where shop_id = $id and date_sub(CURDATE(), INTERVAL 30 DAY) <= date(FROM_UNIXTIME(`order_time`));");
        if($sell!=''){
              return $sv['sell'];
        }else{
            return 0;
        }
    }
    
    
    //转换图片路径
    public function fors($array){
        
        foreach($array as $k=>$v){
            $array[$k]['icon'] = picture($v['icon']);
        }
        return $array;
    }
    
    
}

?>
