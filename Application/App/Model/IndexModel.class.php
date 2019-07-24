<?php

namespace Common\Model;

/**
 * 用户个人中心模型
 */
class IndexModel {
    
    //首页数据
    public function index($post){
       $city_id = intval($post['city_id']);
       $result['banner'] = $this->banner($city_id);
       $result['shop'] = $this->shop($city_id);
       $result['news'] = $this->news();
       $result['hot_search'] = $this->hot_search();
       return array('status'=>true,'msg'=>'获取数据成功','code'=>200,'data'=>$result);
    }
    
    //广告
    public function banner($city_id){
        $field = "name,icon,type,type_id,sort";
        $banner['banner'] = $this->fors(M('ad')->where(array('typeid'=>41,'city_id'=>$city_id))->field($field)->limit(3)->order('sort asc')->select());//banner 3张
        $banner['ad'] = $this->fors(M('ad')->where(array('typeid'=>42,'city_id'=>$city_id))->field($field)->limit(4)->order('sort asc')->select());//首页广告4张      
        return $banner;
    }
    
    //首页推荐商品
    public function shop($city_id){
        $field = "id,shopname,icon,money";
        
        //精选商品
        $shop['jx'] = $this->fors(M('shop')->where(array('jx_shop'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(2)->select());
        $shop['jx'] = $this->sell($shop['jx']);
        //热销商品
        $shop['hot'] = $this->fors(M('shop')->where(array('hot'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(8)->select());
        $shop['hot'] = $this->sell($shop['hot']);
        //热销固定商品
        $shop['hot_gd'] = $this->fors(M('shop')->where(array('hot_gd'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(3)->select());
        $shop['hot_gd'] = $this->sell($shop['hot_gd']);
        //爆款商品
        $shop['bk'] = $this->fors(M('shop')->where(array('bk'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(8)->select());
        $shop['bk'] = $this->sell($shop['bk']);
        //爆款固定
        $shop['bk_gd'] = $this->fors(M('shop')->where(array('bk_gd'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(3)->select());
        $shop['bk_gd'] = $this->sell($shop['bk_gd']);
        //底部自定义商品
        $shop['div'] = $this->fors(M('shop')->where(array('div'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(8)->select());
        $shop['div'] = $this->sell($shop['div']);
        //底部固定商品
        $shop['div_gd'] = $this->fors(M('shop')->where(array('div_gd'=>1,'status'=>1,'city_id'=>$city_id))->field($field)->limit(4)->select());
        $shop['div_gd'] = $this->sell($shop['div_gd']);
        
        return $shop;
        
    }
    
    //商城快报（资讯攻略）
    public function news(){
        $field = "id,title";
        $news = M('article')->where(array('status'=>1))->field($field)->limit(10)->select();
        
        return $news;
    }
    
    //热搜
    public function hot_search(){
        $field = "hot_name";
        $hot_search = M('hot_search')->field($field)->order('sort asc')->limit(5)->select();
        return $hot_search;
    }
    
    //月销计算
    public function sell($shop){
        $m = M();
        foreach($shop as $k=>$v){
            $id = $v['id'];
            $sell = $m->query("select count(id) as sell from bhy_order where shop_id = $id and date_sub(CURDATE(), INTERVAL 30 DAY) <= date(FROM_UNIXTIME(`order_time`));");
            if($sell!=''){
                foreach($sell as $sv){
                    $shop[$k]['sell'] = $sv['sell'];
                }
            }else{
                $shop[$k]['sell']=0;
            }
        }
        return $shop;
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
