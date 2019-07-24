<?php

namespace Admin\Controller;

use User\Api\UserApi;

class ProductController extends AdminController {

    public function _initialize() {

        //分类列表
        $typelist = M('producttype')->where('status=1')->order('sort asc')->select();
        $typelist = $this->unlimitforlevel($typelist, '|--');
        $this->assign("typelist",$typelist);
        //城市列表
        $this->assign("citylist",M('city')->where(array('status'=>1,'pid'=>0))->select());
        //商家列表
        $this->assign("shoplist",M('shop')->where(array('status'=>1))->order('sort asc,id asc')->select());
        //产品属性
        $this->assign("shuxing_list",M('category')->where(array('status'=>1,'pid'=>38))->order('sort asc')->select());

        parent::_initialize();
    }


    public function _filter(&$sqlWhere) {

        if (!empty($_GET['title'])) {
            $sqlWhere['title'] = array('like', '%' . $_GET['title'] . '%');
        }
        if (!empty($_GET['typeid'])) {
            $sqlWhere['typeid'] = array('eq',$_GET['typeid']);
        }


        $sqlWhere['status'] = array('gt', 0);
        $sqlWhere['goodstype'] = array('eq', 1);

        return $sqlWhere;
    }

    //新增
    public function add() {
        if(IS_POST){
            $post = $_POST;
            $model = D('Product');
            if($model->create()){
                $voa = M('producttype')->find($post['typeid']);
                $post['type1'] = empty($voa['pid']) ? $post['typeid'] : $voa['pid'];
                $post['type2'] = empty($voa['pid']) ? 0 : $post['typeid'];
                $post['shuxing'] = implode(',',$post['shuxing']);
                $post['flag'] = implode(',',$post['flag']);
                $post['create_time'] = time();
                $res = $model->add($post);
                if($res){
                    if(!empty($post['cp_title'])){
                        $ii=0;
                        foreach ($post['cp_title'] as $ke=>$ve){
                            if(trim($ve)!=''){
                                $data[$ii]['title'] = $ve;
                                $data[$ii]['product_id'] = $res;
                                $ii++;
                            }
                        }
                        M('product_attrs')->addAll($data);//添加规格
                    }
                    $this->success('添加成功！',U('index'));
                }else{
                    $this->error('添加失败！');
                }
            }else{
                $this->error($model->getError());
            }
        }else{
            $this->display();
        }
    }
    //编辑
    public function edit() {
        if(IS_POST){
            $post = $_POST;
            $model = D('Product');
            if($model->create()){
                $voa = M('producttype')->find($post['typeid']);
                $post['type1'] = empty($voa['pid']) ? $post['typeid'] : $voa['pid'];
                $post['type2'] = empty($voa['pid']) ? 0 : $post['typeid'];
                $post['shuxing'] = implode(',',$post['shuxing']);
                $post['flag']    = implode(',',$post['flag']);
                $post['update_time'] = time();
                $res = $model->save($post);

                //编辑规格
                $this->edit_gg($post);

                if($res){
                    $this->success('编辑成功！',U('index'));
                }else{
                    $this->error('编辑失败！');
                }
            }else{
                $this->error($model->getError());
            }
        }else{
            $get = $_GET;
            if($get['id']>0){
                $vo = M('product')->find($get['id']);
                $vo['shuxing'] = explode(',',$vo['shuxing']);
                $vo['flag'] = explode(',',$vo['flag']);
                $this->assign('vo',$vo);

                //查询规格
                $where['product_id'] = array('eq',$get['id']);
                $where['status']     = array('eq',1);
                $gg_list = M('product_attrs')->where($where)->order('ggid asc')->select();
                $this->assign('gg_list',$gg_list);
            }
            $this->display();
        }
    }

    //编辑商品规格
    public function edit_gg($post){
        //编辑已有的规格
        if(!empty($post['gg_id'])){
            //删除已经删除的
            $whe['product_id'] = array('eq',$post['id']);
            $liste = M('product_attrs')->where($whe)->select();
            $b = array_column($liste,'ggid');
            $a = $post['gg_id'];
            $idarr = array_merge(array_diff($a, $b),array_diff($b, $a));
            $whet['ggid'] = array('in',implode(',',$idarr));
            M('product_attrs')->where($whet)->delete();

            $ii=0;
            foreach ($post['gg_id'] as $ke=>$ve){
                $stuid[$ii] = $ve;
                $data[$ii]['title']  = $post['cp_title'.$ve]; //中文标题
                $ii++;
            }
            $wherty['GGID'] = $stuid;
            $res = saveAll($wherty,$data,'product_attrs');
        }
        //新增的规格
        if(!empty($post['cp_title'])){
            $iii=0;
            foreach ($post['cp_title'] as $ks=>$vs){
                if(trim($vs)!=''){
                    $datab[$iii]['product_id'] = $post['id'];
                    $datab[$iii]['title']  = $vs; //中文标题
                    $iii++;
                }
            }
            $res2 = M('product_attrs')->addAll($datab);
        }
    }




    public function unlimitforlevel($cate, $html = '------', $pid = 0, $level = 0, $topid = 0) {

        $arr = array();  //定义返回的数组
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {  //等于顶级分离
                $v['topid'] = 0;
                if ($level == 0) {
                    $topid = $v['id'];
                }
                if ($level != 0) {
                    $v['topid'] = $topid;
                }

                $v['level'] = $level + 1;     //层级加1
                if ($level > 1) {
                    $html = str_replace('|', '', $html);
                }

                $v['html'] = str_repeat($html, $level);   //str_repeat  字符串重复次数
                //var_dump($v['id']);
                $lis = M('column')->where('fid=' . $v['id'])->select();
                if ($lis) {
                    $v['num'] = 1;
                } else {
                    $v['num'] = 0;
                }
                $arr[] = $v;
                //array_merge  把多个数组合并为一个数组
                $arr = array_merge($arr, $this->unlimitforlevel($cate, $html, $v['id'], $level + 1, $topid));
            }
        }
        return $arr;
    }

//图集
     



}
