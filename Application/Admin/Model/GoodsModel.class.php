<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
/**
 * 配置模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class GoodsModel extends Model {
    protected $_validate = array(
		array('name', 'require', '请输入商品名称', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('content', 'require', '请输入商品简介', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('cid', 'require', '请选择商品类别', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('bid', 'require', '请选择商品品牌', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('color[]', 'require', '请选择商品颜色', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		//array('url', 'require', '请输入商品链接', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		//array('url', 'require', '链接格式不正确', self::EXISTS_VALIDATE, 'url', self::MODEL_BOTH),
		//array('icon', 'require', '请选择商品图片', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),

    );

}
