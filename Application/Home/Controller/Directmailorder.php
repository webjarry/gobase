<?php

/**
 * 帮助中心
 */

namespace Home\Controller;

use OT\DataDictionary;

class Directmailorder extends Init {

    public function __construct() {
        parent::__construct();
        $this->finduser();
        header('Content-Type:text/html;charset=utf-8');



	/*	$otherurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if(strpos($otherurl,'wlcxjg') !==false){

		}else{

			   if (!$_COOKIE['token']) {
					header("location:/index/user/login");
					exit;
				}

		}*/


		
        
    }

    /**
     * 直邮下单
     */
    public function directmailorder() {
        if ($_POST) {
            $goodsname = (array) $_POST['goodsname'];
            $brand = (array) $_POST['brand'];
            $select2 = (array) $_POST['select2'];
            $spec = (array) $_POST['spec'];
            $number = (array) $_POST['number'];
            $unitprice = (array) $_POST['unitprice'];
            $smallwarehouseid = (array) $_POST['smallwarehouseid'];
            $addressId = (string) $_POST['recipientid'];
            $file_name = explode(',', (string) $_POST['file_name']);
            $remarks = (string) $_POST['remarks'];
            $is_insurance = (string) $_POST['is_insurance'] ?: 0;
            $shipAddressId = (string) $_POST['sendid'];
            $warehouseId = (string) $_POST['warehouseid'];
            $goodsArray = array();
            foreach ($goodsname as $k => $v) {
                $goodsArray[$k]['name'] = $v;
                $goodsArray[$k]['category'] = $select2[$k];
                $goodsArray[$k]['brand'] = $brand[$k];
                $goodsArray[$k]['spec'] = $spec[$k];
                $goodsArray[$k]['num'] = $number[$k];
                $goodsArray[$k]['price'] = $unitprice[$k];
                $goodsArray[$k]['warehouse_id'] = $smallwarehouseid[$k];
            }
            $data = array(
                'warehouse_id' => $warehouseId,
                'address_id' => $addressId,
                'is_buy_safe' => $is_insurance,
                'storage_transport_sn' => '',
                'ship_address_id' => $shipAddressId,
                'type' => 4,
                'goods' => json_encode($goodsArray),
                'volume_weight' => $volumeWeight);
            $result = $this->request_post('' . $this->url . '/v1/user/domestic_package/save_order', $data);
            $result = json_decode($result, true);
            $this->appReturn($result);
        } else {

            //查询收件人
            $map['type'] = 1;
            $map['sign'] = 1;
            $map['uid'] = $this->uid;
            $addresseelist = table('UserAddress')->where($map)->order('is_default desc')->find('array');
            $this->assign('addresseelist', $addresseelist);
            //获取类别
            $catlist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=578');
            $catlist = json_decode($catlist, true);
            $this->assign('catlist', $catlist['data']);
            //获取小仓库
            $houselist = table('UserWarehouse')->where(array('uid' => $this->uid))->find('array');
            $this->assign('houselist', $houselist);
            //获取发件人
            $wheresend['type'] = 1;
            $wheresend['sign'] = 2;
            $wheresend['uid'] = $this->uid;
            $senderlist = table('UserAddress')->where($wheresend)->order('is_default desc')->find('array');
            $this->assign('senderlist', $senderlist);
            //获取仓库
            $warehouseid = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=743');
            $warehouseid = json_decode($warehouseid, true);
            $this->assign('warehouseid', $warehouseid['data']);

            $this->assign('style', 2);
            $this->assign('styletwo', 1);
            $this->show();
        }
    }

    /**
     * 上传照片
     */
    public function addphoto() {
        $user_ablum = (string) $_POST['user_ablum'];
        $order_sn = (string) $_POST['order_sn'];
        if (!$user_ablum) {
            $this->appReturn(array('status' => false, 'msg' => '请上传照片'));
        }
        $map = array();
        $map['uid'] = $this->uid;
        $map['order_sn'] = $order_sn;

        $logistics = table('Logistics')->where('order_sn', $order_sn)->field('user_ablum,id')->find();
        if (!$logistics) {
            $this->appReturn(array('status' => false, 'msg' => '可操作信息不存在'));
        }

        $data['user_ablum'] = $user_ablum;
        $result = table('Logistics')->where('id', $logistics['id'])->save($data);
        if (!$result) {
            $this->appReturn(array('status' => false, 'msg' => '操作失败,请稍后重试'));
        }

        $this->appReturn(array('status' => 1, 'msg' => '操作成功'));
    }

    /**
     * 删除订单
     */
    public function delorder() {
        $order_sn = $_POST['id'];
        $result = $this->request_post('' . $this->url . '/v1/user/orders/del', array('order_sn' => $order_sn));
        $result = json_decode($result, true);
        $this->appReturn($result);
    }

    /**
     * 编辑订单操作
     */
    public function editpackagepost() {
        $goodsname = (array) $_POST['goodsname'];
        $brand = (array) $_POST['brand'];
        $select2 = (array) $_POST['select2'];
        $spec = (array) $_POST['spec'];
        $number = (array) $_POST['number'];
        $unitprice = (array) $_POST['unitprice'];
        $smallwarehouseid = (array) $_POST['smallwarehouseid'];
        $addressId = (string) $_POST['recipientid'];
        $file_name = explode(',', (string) $_POST['file_name']);
        $message = (string) $_POST['remarks'];
        $is_insurance = (string) $_POST['is_insurance'] ?: 0;
        $shipAddressId = (string) $_POST['sendid'];
        $orderSn = post('order_sn', 'text', '');
        $goodsArray = array();
        foreach ($goodsname as $k => $v) {
            $goodsArray[$k]['name'] = $v;
            $goodsArray[$k]['category'] = $select2[$k];
            $goodsArray[$k]['brand'] = $brand[$k];
            $goodsArray[$k]['spec'] = $spec[$k];
            $goodsArray[$k]['num'] = $number[$k];
            $goodsArray[$k]['price'] = $unitprice[$k];
            $goodsArray[$k]['warehouse_id'] = $smallwarehouseid[$k];
        }
        $result = dao('Directmailorder')->editpackagepost($addressId, $file_name, $message, $is_insurance, $shipAddressId, $orderSn, $goodsArray, $this->uid);
        $this->appReturn($result);
    }

    /**
     * 小仓库列表
     */
    public function smallwarehouse() {
        if ($_POST) {
//            $id = post('id', 'intval', 0);
//            $data['name'] = post('name', 'text', '');
//            $category = post('category', 'intval', 0);
//            $data['brand'] = post('brand', 'text', '');
//            $data['spec'] = post('spec', 'floatval', 0.000);
//            $data['num'] = post('num', 'intval', 0);
//            $data['price'] = post('price', 'floatval', 0.00);
            $result = $this->request_post('' . $this->url . '/v1/user/warehouse/editPost', $_POST);
            $result = json_decode($result, true);
            $this->appReturn($result);
        } else {
            $map['uid'] = $this->uid;
            $param = '';
            $pageNo = get('pageNo', 'intval', 1);
            $pageSize = get('pageSize', 'intval', 10);
            $offer = max(($pageNo - 1), 0) * $pageSize;
//            $list = table('UserWarehouse')->where($map)->limit($offer, $pageSize)->find('array');
//            $total = table('UserWarehouse')->where($map)->count();
            $page = new \denha\Pages($total, $pageNo, $pageSize, url('', $param));

            $list = $this->curl_get_https('' . $this->url . '/v1/user/warehouse/lists');
            $list = json_decode($list, true);
            //获取类别
            $catlist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=578');
            $catlist = json_decode($catlist, true);
            $this->assign('catlist', $catlist['data']);
            $this->assign('pages', $page->loadConsoleuser());
            $this->assign('list', $list['data']['list']);
            $this->assign('style', 2);
            $this->assign('styletwo', 6);
            $this->show();
        }
    }

    /**
     * 删除仓库
     */
    public function delhouse() {
        $result = $this->request_post('' . $this->url . '/v1/user/warehouse/del', $_POST);
        $result = json_decode($result, true);
        $this->appReturn($result);
    }

    /**
     * 选定收件人，发件人
     */
    public function choiceaddressee() {
        $id = (string) $_POST['id'];
        $addresseelist = table('UserAddress')->where(array('id' => $id))->find();
        if ($addresseelist) {
            $this->appReturn(array('status' => 1, 'data' => $addresseelist));
        }
    }

    /**
     * 选定小仓库
     */
    public function smallhouse() {
        $arr = (array) $_POST['arr'];
        $where['id'] = array('in', $arr);
        $list = table('UserWarehouse')->where($where)->find('array');
        $catlist = table('Category')->where(array('parentid' => 578))->find('array');
        if ($list) {
            $this->appReturn(array('status' => 1, 'data' => $list, 'catlist' => $catlist));
        }
    }

    /**
     * 包裹管理
     */
    public function packagelist() {
//        $map['uid'] = $this->uid;
        $param = '';
//        $pageNo = get('pageNo', 'intval', 1);
//        $pageSize = get('pageSize', 'intval', 10);
//        $offer = max(($pageNo - 1), 0) * $pageSize;
//        $list = table('UserWarehouse')->where($map)->limit($offer, $pageSize)->find('array');
//        $total = table('UserWarehouse')->where($map)->count();
//        //获取类别
//        $catlist = table('Category')->where(array('parentid' => 578))->find('array');
//        $this->assign('pages', $page->loadConsolewap());
//        $this->assign('list', $list);
//        $this->assign('catlist', $catlist);
        $type = get('type', 'text', '1');
        $status = get('status', 'text', '');
        if ($status == 5) {
            $status = '';
        }
        $pageNo = get('pageNo', 'intval', 1);
        $pageSize = get('pageSize', 'intval', 10);
        $keyword = get('keyword', 'text', '');
        $start_time = get('start_time', 'text', '');
        $end_time = get('end_time', 'text', '');
        $result = $this->curl_get_https('' . $this->url . '/v1/user/orders/lists?type=' . $type . '&status=' . $status . '&pageNo=' . $pageNo . '&pageSize=' . $pageSize . '&keyword=' . $keyword . '&start_time=' . $start_time . '&end_time=' . $end_time . '', '');
        $result = json_decode($result, true);
        $list = $result['data']['list'];
        $page = new \denha\Pages($result['data']['total'], $pageNo, $pageSize, url('', $param));
        $this->assign('list', $list);
        $this->assign('pages', $page->loadConsoleuser());
        $this->assign('style', 2);
        $this->assign('styletwo', 2);
        $this->assign('keyword', $keyword);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        if ($status == '') {
            $this->assign('status', 5);
        } else {
            $this->assign('status', $status);
        }
        // print_r($list['list']);die;
        $this->show();
    }

    /**
     * 包裹管理编辑
     */
    public function editpackage() {
        $orderSn = get('order_sn', 'text', '');
        $maps['order_sn'] = $orderSn;
        $maps['uid'] = $this->uid;
        $orders = table('Orders')->where($maps)->field('id,order_sn,message')->find();
        if (!$orders) {
            $this->appReturn(array('status' => false, 'msg' => '信息不存在'));
        }
        $goodsList = table('OrdersPackage')->where('order_sn', $orders['order_sn'])->find('array');
        $logistics = table('Logistics')->where('order_sn', $orders['order_sn'])->find();
        $this->assign('goodsList', $goodsList);
        //总金额
        foreach ($goodsList as $k => $v) {
            $sumprice += number_format($v['account'], 2, '.', '');
        }
        $this->assign('sumprice', $sumprice);
        $this->assign('logistics', $logistics);
        $this->assign('orders', $orders);
        //查询收件人
        $map['type'] = 1;
        $map['sign'] = 1;
        $map['uid'] = $this->uid;
        $addresseelist = table('UserAddress')->where($map)->order('is_default desc')->find('array');
        $this->assign('addresseelist', $addresseelist);
        //获取类别
        $catlist = table('Category')->where(array('parentid' => 578))->find('array');
        $this->assign('catlist', $catlist);
        //获取小仓库
        $houselist = table('UserWarehouse')->where(array('uid' => $this->uid))->find('array');
        $this->assign('houselist', $houselist);
        //获取发件人
        $wheresend['type'] = 1;
        $wheresend['sign'] = 2;
        $wheresend['uid'] = $this->uid;
        $senderlist = table('UserAddress')->where($wheresend)->order('is_default desc')->find('array');
        $this->assign('senderlist', $senderlist);
        $this->assign('style', 2);
        $this->assign('styletwo', 2);
        $this->show();
    }

    /**
     * 详情
     */
    public function wdbdzy() {
        $order_sn = (string) $_GET['order_sn'];
        $result = $this->curl_get_https('' . $this->url . '/v1/user/domestic_package/edit_order?order_sn=' . $order_sn . '', '');
        $result = json_decode($result, true);
        $list = $result['data'];

        $this->assign('list', $list);
        $this->assign('style', 2);
        $this->assign('styletwo', 2);
        $this->show();
    }

    /**
     * 采购明细
     */
    public function purchaselist() {
        $result = $this->curl_get_https('' . $this->url . '/v1/user/purchase/lists', '');
        $result = json_decode($result, true);
        $list = $result['data']['list'];
        $this->assign('list', $list);
        $this->assign('style', 2);
        $this->assign('styletwo', 5);
        $this->show();
    }

    /**
     * 代收点
     */
    public function collectionpoint() {
        if ($_POST) {
            $seller_uid = $_POST['seller_uid'];
            $order_sn = $_POST['order_sn'];
            $order_sn = implode(',', $order_sn);
            $result = $this->request_post('' . $this->url . '/v1/user/domestic_package/ship', array('order_sn' => $order_sn, 'seller_uid' => $seller_uid));
            $result = json_decode($result, true);
            $this->appReturn($result);
        } else {
            header('Content-Type:text/html;charset=utf-8');
            $param['keyword'] = get('keyword', 'text', '');
            $param['city_id'] = get('city_id', 'text', '');
            $lng = get('lng', 'text', 0);
            $lat = get('lat', 'text', 0);
            $pageNo = get('pageNo', 'intval', 1);
            $pageSize = get('pageSize', 'intval', 10);
            $result = $this->curlStream('' . $this->url . '/v1/user/shop/lists?keyword=' . $param['keyword'] . '&city_id=' . $param['city_id'] . '&pageNo=' . $pageNo . '&pageSize=' . $pageSize . '&lng=' . $lng . '&lat=' . $lat . '', '');
            $result = json_decode($result, true);
            $list = $result['data']['list'];
            $this->assign('list', $list);
            //发货列表
            $resultgoods = $this->curlStream($this->url . '/v1/user/domestic_package/ship');
            $resultgoods = json_decode($resultgoods, true);
            $this->assign('resultgoods', $resultgoods['data']['list']);
            //获取仓库
            $warehouseid = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=743');
            $warehouseid = json_decode($warehouseid, true);
            $this->assign('warehouseid', $warehouseid['data']);
            $this->assign('city_id', $param['city_id']);
            $this->assign('keyword', $param['keyword']);
            $this->assign('style', 2);
            $this->assign('styletwo', 3);
            $this->show();
        }
    }

    /**
     * 转运管理
     */
    public function wdzygl() {
        if ($_POST) {
            $warehouseId = post('warehouse_id', 'text', '');
            $message = post('message', 'text', '');
            $storageTransportSn = post('storage_transport_sn', 'text', '');
            $storageTransportId = post('storage_transport_id', 'intval', 0);
            $volumeWeight = post('volume_weight', 'float', 0);
            $goodsname = (array) $_POST['goodsname'];
            $brand = (array) $_POST['brand'];
            $select2 = (array) $_POST['select2'];
            $spec = (array) $_POST['spec'];
            $number = (array) $_POST['number'];
            $unitprice = (array) $_POST['unitprice'];
            $goodsArray = array();
            foreach ($goodsname as $k => $v) {
                $goodsArray[$k]['name'] = $v;
                $goodsArray[$k]['category'] = $select2[$k];
                $goodsArray[$k]['brand'] = $brand[$k];
                $goodsArray[$k]['spec'] = $spec[$k];
                $goodsArray[$k]['num'] = $number[$k];
                $goodsArray[$k]['price'] = $unitprice[$k];
            }
//            $result = dao('Directmailorder')->addPackage($warehouseId, $message, $storageTransportSn, $storageTransportId, $volumeWeight, $type, $goodsArray, $this->uid, $this->group);
//            $this->appReturn($result);
            $data = array(
                'warehouse_id' => $warehouseId,
                'message' => $message,
                'storage_transport_sn' => $storageTransportSn,
                'storage_transport_id' => $storageTransportId,
                'goods' => json_encode($goodsArray),
                'volume_weight' => $volumeWeight);
            $result = $this->request_post('' . $this->url . '/v1/user/transport/add_package', $data);
            $result = json_decode($result, true);
            $this->appReturn($result);
        } else {
            //获取国家
            $result = $this->curl_get_https('' . $this->url . '/v1/common/category/getIntCountry', '');
            $result = json_decode($result, true);
            $this->assign('list', $result['data']['list']);
            //获取快递公司
            $expresslist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=613');
            $expresslist = json_decode($expresslist, true);
            $this->assign('expresslist', $expresslist['data']);
            //获取类别
            $catlist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=578');
            $catlist = json_decode($catlist, true);
            $this->assign('catlist', $catlist['data']);
            $this->assign('style', 1);
            $this->assign('styleone', 1);
            $this->show();
        }
    }

    public function selectcountry() {
        $alias = (string) $_POST['alias'];
        if ($alias) {
            $ct = table('Category')->tableName();
            $us = table('WarehouseInfo')->tableName();
            $map["$ct.parentid"] = 743;
            $map["$ct.alias_2"] = '国际转运';
            $map["$ct.alias"] = $alias;
            $field = "$ct.id,$ct.thumb,$ct.name,$ct.bname,$ct.bname_2,$ct.name_en,$ct.name_jp,$us.name as username,$us.mobile,$us.address,$us.zip_code";
            $map['is_show'] = 1;
            $warehouselist = table('Category')->where($map)->join($us, "$us.category_id = $ct.bname_2")->field($field)->order('bname asc,sort asc')->find('array');
            foreach ($warehouselist as $k => $v) {
                $reg .= '<option value="' . $v['bname_2'] . '">' . $v['name'] . '(' . $v['username'] . '/' . $v['mobile'] . '/' . $v['address'] . ')</option>';
            }
        }
        $this->appReturn(array('status' => 1, 'data' => $reg));
    }

    /**
     * 中转地址
     */
    public function zyglzzdz() {
        if ($_POST) {
            $trans_id = post('id', 'text', '');
            $id = array(
                'id' => $trans_id,
            );
            $result = $this->request_post('' . $this->url . '/v1/user/transport/defaultTransfer', $id);
            $result = json_decode($result, true);
            $this->appReturn($result);
//            if (!$trans_id) {
//                $this->appReturn(array('status' => false, 'msg' => '请选择需要设为默认的中转地址'));
//            }
//            $result = table('user')->where('uid', $this->uid)->save(array('default_transfer' => $trans_id));
//            $this->appReturn(array('msg' => '操作成功'));
        } else {
            //获取中转仓
            $alias = (string) $_GET['alias'];
            if ($alias) {
                $map['parentid'] = 743;
                $map['alias_2'] = '国际转运';
                $map['alias'] = $alias;
                $field = 'id,thumb,name,bname,bname_2,name_en,name_jp';
                $map['is_show'] = 1;
                $warehouselist = table('Category')->where($map)->field($field)->order('bname asc,sort asc')->find('array');
                $this->assign('warehouselist', $warehouselist);
            }
            $cityId = get('city_id', 'text', '');
            if ($cityId) {
                $cityIds = $cityId;
            } else {
                $cityIds = $warehouselist[0]['bname_2'];
            }
            $where['category_id'] = $cityIds;
            $data = table('WarehouseInfo')->where($where)->field('id,name,mobile,address,zip_code')->find();
            $data['name'] = isset($this->uid) ? $data['name'] . '|' . $this->uid : $data['name'];
            $this->assign('data', $data);
            //获取国家
            $result = $this->curl_get_https('' . $this->url . '/v1/common/category/getIntCountry', '');
            $result = json_decode($result, true);
            $this->assign('list', $result['data']['list']);

            $this->assign('cityId', $cityIds);
            $this->assign('alias', $alias);
            $this->assign('style', 1);
            $this->assign('styleone', 5);
            $this->show();
        }
    }

    /**
     * 包裹统计
     */
    public function wdtj() {
        $param = '';
        $pageNo = get('pageNo', 'intval', 1);
        $pageSize = get('pageSize', 'intval', 10);
        $data['start_time'] = get('start_time', 'text', '');
        $data['end_time'] = get('end_time', 'text', '');
        $result = $this->request_post('' . $this->url . '/v1/common/user/packageStatistics', array('page' => $pageNo, 'limit' => $pageSize, 'start_time' => $data['start_time'], 'end_time' => $data['end_time']));
        $result = json_decode($result, true);
        $page = new \denha\Pages($result['count'], $pageNo, $pageSize, url('', $param));
        $this->assign('total_price', $result['total_price']);
        $this->assign('list', $result['data']);
        $this->assign('pages', $page->loadConsoleuser());
        $this->assign('style', 3);
        $this->assign('start_time', $data['start_time']);
        $this->assign('end_time', $data['end_time']);
        $this->show();
    }

    /**
     * 转运管理 包裹管理
     */
    public function zypackagelist() {
        $param['type'] = get('type', 'text', '2');
        $param['status'] = get('status', 'text', '');
        if ($param['status'] == 5) {
            $param['status'] = '';
        }
        $pageNo = get('pageNo', 'intval', 1);
        $pageSize = get('pageSize', 'intval', 10);
        $param['keyword'] = get('keyword', 'text', '');
        $param['start_time'] = get('start_time', 'text', '');
        $param['end_time'] = get('end_time', 'text', '');
        $result = $this->curl_get_https('' . $this->url . '/v1/user/orders/lists?type=' . $param['type'] . '&status=' . $param['status'] . '&pageNo=' . $pageNo . '&pageSize=' . $pageSize . '&keyword=' . $param['keyword'] . '&start_time=' . $param['start_time'] . '&end_time=' . $param['end_time'] . '', '');
        $result = json_decode($result, true);
        $list = $result['data']['list'];

        $page = new \denha\Pages($result['data']['total'], $pageNo, $pageSize, url('', $param));
        $this->assign('list', $list);
        $this->assign('pages', $page->loadConsoleuser());
        $this->assign('keyword', $param['keyword']);
        $this->assign('start_time', $param['start_time']);
        $this->assign('end_time', $param['end_time']);
        if ($param['status'] == '') {
            $this->assign('status', 5);
        } else {
            $this->assign('status', $param['status']);
        }
        $this->assign('style', 1);
        $this->assign('styleone', 2);
        $this->show();
    }

    /**
     * 编辑转运包裹
     */
    public function editzypackage() {
        $orderSn = get('order_sn', 'text', '');
        $maps['order_sn'] = $orderSn;
        $maps['uid'] = $this->uid;
        $orders = table('Orders')->where($maps)->field('id,order_sn,message')->find();
        if (!$orders) {
            $this->appReturn(array('status' => false, 'msg' => '信息不存在'));
        }
        $goodsList = table('OrdersPackage')->where('order_sn', $orders['order_sn'])->find('array');
        $logistics = table('Logistics')->where('order_sn', $orders['order_sn'])->find();
        $this->assign('goodsList', $goodsList);
//        var_dump($logistics);die;
        //获取国家
        $result = $this->curl_get_https('' . $this->url . '/v1/common/category/getIntCountry', '');
        $result = json_decode($result, true);
        $this->assign('list', $result['data']['list']);
        //获取快递公司
        $expresslist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=613');
        $expresslist = json_decode($expresslist, true);
        $this->assign('expresslist', $expresslist['data']);
        //获取类别
        $catlist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=578');
        $catlist = json_decode($catlist, true);
        $this->assign('catlist', $catlist['data']);
        //查询该中转仓的国家
        $wheresele['bname_2'] = $logistics['warehouse_id'];
        $wheresele['parentid'] = 743;
        $wheresele['is_show'] = 1;
        $whereselelist = table('Category')->where($wheresele)->find();
        //查询该国家下的中转仓
        if ($whereselelist['alias']) {
            $ct = table('Category')->tableName();
            $us = table('WarehouseInfo')->tableName();
            $map["$ct.parentid"] = 743;
            $map["$ct.alias_2"] = '国际转运';
            $map["$ct.alias"] = $whereselelist['alias'];
            $field = "$ct.id,$ct.thumb,$ct.name,$ct.bname,$ct.bname_2,$ct.name_en,$ct.name_jp,$us.name as username,$us.mobile,$us.address,$us.zip_code";
            $map['is_show'] = 1;
            $warehouselist = table('Category')->where($map)->join($us, "$us.category_id = $ct.bname_2")->field($field)->order('bname asc,sort asc')->find('array');
            foreach ($warehouselist as $k => $v) {
                if ($v['bname_2'] == $logistics['warehouse_id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $reg .= '<option value="' . $v['bname_2'] . '" ' . $selected . '>' . $v['name'] . '(' . $v['username'] . '/' . $v['mobile'] . '/' . $v['address'] . ')</option>';
            }
        }
        $this->assign('reg', $reg);
        $this->assign('whereselelist', $whereselelist);
        $this->assign('logistics', $logistics);
        $this->assign('orders', $orders);
        $this->assign('style', 1);
        $this->assign('styleone', 2);
        $this->show();
    }

    /*      \
     * 编辑运转包裹操作
     */

    public function editzyPackagePost() {
        $orderSn = post('order_sn', 'text', '');
        $warehouseId = post('warehouse_id', 'text', '');
        $message = post('message', 'text', '');
        $storageTransportSn = post('storage_transport_sn', 'text', '');
        $storageTransportId = post('storage_transport_id', 'intval', 0);
        $volumeWeight = post('volume_weight', 'float', 0);
        $type = 5;
        $goodsname = (array) $_POST['goodsname'];
        $brand = (array) $_POST['brand'];
        $select2 = (array) $_POST['select2'];
        $spec = (array) $_POST['spec'];
        $number = (array) $_POST['number'];
        $unitprice = (array) $_POST['unitprice'];
        $goodsArray = array();
        foreach ($goodsname as $k => $v) {
            $goodsArray[$k]['name'] = $v;
            $goodsArray[$k]['category'] = $select2[$k];
            $goodsArray[$k]['brand'] = $brand[$k];
            $goodsArray[$k]['spec'] = $spec[$k];
            $goodsArray[$k]['num'] = $number[$k];
            $goodsArray[$k]['price'] = $unitprice[$k];
        }
        $result = dao('Directmailorder')->editzyPackage($orderSn, $warehouseId, $message, $storageTransportSn, $storageTransportId, $volumeWeight, $type, $goodsArray, $this->uid, $this->group);
        $this->appReturn($result);
    }

    public function getName($bname_2, $lg) {

        $map['bname_2'] = array('in', $bname_2);

        $name = table('Category')->where($map)->field('name')->find('one', true);

        if ($lg && $lg != 'zh') {
            $name = table('Category')->where($map)->field('name_' . $lg)->find('one', true);
        } else {
            $name = table('Category')->where($map)->field('name')->find('one', true);
        }

        if (!$name) {
            return null;
        }

        if (count($name) == 1) {
            return (string) $name[0];
        }

        return (array) $name;
    }

    /**
     * 包裹打包
     */
    public function zyglbgdb() {

        $result = $this->curl_get_https('' . $this->url . '/v1/user/transport/get_success_list', '');
        $result = json_decode($result, true);
        $list = $result['data']['list'];
        foreach ($list as $k => $v) {
            $listarr[] = $v['child'];
        }
        foreach ($listarr as $k => $v) {
            foreach ($v as $ks => $vs) {
                $listarray[] = $vs;
            }
        }
        $this->assign('lists', $listarray);
        $this->assign('style', 1);
        $this->assign('styleone', 3);
        $this->show();
    }

    /**
     * 创建运单
     */
    public function bgdbcjyd() {
        if ($_POST) {
            $sign = $_POST['sign'];
            $orders_num = $_POST['orders_num'];
            $declared_price = $_POST['declared_price'];
            $tax = $_POST['tax'];
            $aegis_price = $_POST['aegis_price'];
            $volume_weight = $_POST['volume_weight'];
            $message = $_POST['message'];
            $recipientid = $_POST['recipientid'];
            $zzfu = json_encode($_POST['zzfu']);
            $ship_address_id = $_POST['ship_address_id'];
            $orderSnText = post('order_sn', 'text', '');
            $channel_id = explode(',', $_POST['channel_id']);
            $number = $_POST['number'];
            $order_sn = $_POST['order_sns'];
            $goodsid = $_POST['id'];
            $goodsArray = array();
            foreach ($goodsid as $k => $v) {
                $goodsArray[$k]['id'] = $v;
                $goodsArray[$k]['order_sn'] = $order_sn[$k];
                $goodsArray[$k]['number'] = $number[$k];
            }
            $orders = array('address_id' => $recipientid, 'orders_price' => $channel_id[1], 'outbound_company' => $channel_id[0], 'goods' => $goodsArray);
            $data = array(
                'type' => $sign,
                'orders_num' => $orders_num,
                'aegis_total_price' => $aegis_price,
                'aegis_price' => $aegis_price,
                'tax' => $tax,
                'volume_weight' => $volume_weight,
                'ship_address_id' => $ship_address_id,
                'message' => $message,
                'order_sn' => $orderSnText,
                'vat' => $zzfu,
                'orders' => json_encode($orders),
                'declared_price' => $declared_price);

            $result = $this->request_post('' . $this->url . '/v1/user/transport/add_orders', $data);
            $result = json_decode($result, true);
            $this->appReturn($result);
        } else {
            $orderSn = get('order_sn', 'text', '');
            $result = $this->curl_get_https('' . $this->url . '/v1/user/transport/add_orders?order_sn=' . $orderSn . '', '');
            $result = json_decode($result, true);
            $data = $result['data'];
            $this->assign('data', $data);

            foreach ($data['list'] as $k => $v) {
                $sumaccount += $v['account'];
            }
            $this->assign('sumaccount', $sumaccount);
            //获取发件人
            $wheresend['type'] = 2;
            $wheresend['sign'] = 2;
            $wheresend['uid'] = $this->uid;
            $senderlist = table('UserAddress')->where($wheresend)->order('is_default desc')->find('array');
            $this->assign('senderlist', $senderlist);
            //查询收件人
            $mapadd['type'] = 2;
            $mapadd['sign'] = 1;
            $mapadd['uid'] = $this->uid;
            $addresseelist = table('UserAddress')->where($mapadd)->order('is_default desc')->find('array');
            $this->assign('addresseelist', $addresseelist);
            //查询增值服务
            $zzfu = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=708');
            $zzfu = json_decode($zzfu, true);
            foreach ($zzfu['data'] as $k => $v) {
                $values = json_decode($this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=' . $v['id'] . ''), true);
                $v['values'] = $values['data'];
                $zzfu['data'][$k] = $v;
            }
            $this->assign('zzfu', $zzfu['data']);
            $this->assign('style', 1);
            $this->assign('styleone', 3);
            $this->show();
        }
    }

    //预估价格
    public function checkwuliu() {
        $weight = (string) $_POST['volume_weight'];
        $country = (string) $_POST['country'];
        $result = $this->curl_get_https('' . $this->url . '/v1/index/LogisticsPrice/index?weight=' . $weight . '&country=' . $country . '');
        $result = json_decode($result, true);
        $this->appReturn(array('status' => 1, 'data' => $result['data']['list']));
    }

    /**
     * 我的运单
     */
    public function myorder() {
        $param['type'] = get('type', 'text', '2');
        $param['status'] = get('status', 'text', '');
        if ($param['status'] == 5) {
            $param['status'] = '';
        }
        $pageNo = get('pageNo', 'intval', 1);
        $pageSize = get('pageSize', 'intval', 10);
        $result = $this->request_post('' . $this->url . '/v1/user/orders/lists?type=' . $param['type'] . '&status=' . $param['status'] . '&pageNo=' . $pageNo . '&pageSize=' . $pageSize . '', '');
        $result = json_decode($result, true);
        $list = $result['data']['list'];
        $page = new \denha\Pages($result['data']['total'], $pageNo, $pageSize, url('', $param));
        $this->assign('list', $list);
        $this->assign('pages', $page->loadConsoleuser());
        if ($param['status'] == '') {
            $this->assign('status', 5);
        } else {
            $this->assign('status', $param['status']);
        }
        $this->assign('style', 1);
        $this->assign('styleone', 4);
        $this->show();
    }

    /**
     * 完成打包
     */
    public function packagefinish() {
        $order_sn = $_POST['ordersn'];
        $result = $this->request_post('' . $this->url . '/v1/user/orders/orders_package_finish', array('order_sn' => $order_sn));
        $result = json_decode($result, true);
        $this->appReturn($result);
    }

    /**
     * 退件
     */
    public function ordersback() {
        $order_sn = $_POST['id'];
        $result = $this->request_post('' . $this->url . '/v1/user/orders/apply_orders_back', array('order_sn' => $order_sn));
        $result = json_decode($result, true);
        $this->appReturn($result);
    }

    public function edituser() {
        if ($_POST) {

            $data['sex'] = post('sex', 'intval', '');

            $data['nickname'] = post('nickname', 'text', '');

            $data['avatar'] = post('img');
            if (!$data['nickname']) {
                $this->appReturn(array('status' => false, 'msg' => '请输入昵称'));
            }


            $reslut = table('User')->where(array('uid' => $this->uid))->save($data);
            if (!$reslut) {
                $this->appReturn(array('status' => false, 'msg' => '执行失败'));
            }
            $this->appReturn(array('msg' => '保存成功'));
        } else {
            //获取类别
            $catlist = $this->curl_get_https('' . $this->url . '/v1/common/category/getAllList?id=575');
            $catlist = json_decode($catlist, true);
            $this->assign('catlist', $catlist['data']);
            $this->show();
        }
    }

    /**
     * 余额
     */
    public function dotbalance() {
        $result = $this->curl_get_https('' . $this->url . '/v1/common/Deposit/index');
        $result = json_decode($result, true);
        $this->assign('list', $result['data']['list']);
        $userinfo = table('User')->where($where)->field('id as user_id,nickname,is_bind_mail,type,sex,avatar,mobile,moeny,moeny_aud')->find();
        $this->assign('userinfo', $userinfo);
        $this->assign('style', 4);
        $this->show();
    }

    public function automoenyupdate() {
        $is_auto_moeny = (string) $_POST['is_auto_moeny'];
        $result = $this->request_post('' . $this->url . '/v1/user/index/autoMoenyUpdate', array('is_auto_moeny' => $is_auto_moeny));
        $result = json_decode($result, true);
        $this->appReturn($result);
    }

    /**
     * 物流查询
     */
    public function wlcxjg() {
        $order_sn = (string) $_GET['order_sn'];
        $type = (string) $_GET['type'];
        if ($type != 1) {
            $type = 2;
        }
        $result = $this->curl_get_https('' . $this->url . '/v1/index/Logistics/index?type=' . $type . '&order_sn=' . $order_sn . '');
        $result = json_decode($result, true);
        $this->assign('list', $result['data']['list']);
        $this->assign('order_sn', $order_sn);
        $this->assign('type', (string) $_GET['type']);
        $this->show();
    }

    public function printA4(){

        $order_sn = (string) $_GET['order_sn'];

        $result = $this->curl_get_https('' . $this->url . '/v1/user/orders/detail?order_sn=' . $order_sn . '');
        $result = json_decode($result, true);

        $this->assign('list', $result['data']);

        $this->display();

    }
    public function printRe(){

        $order_sn = (string) $_GET['order_sn'];

        $result = $this->curl_get_https('' . $this->url . '/v1/user/orders/detail?order_sn=' . $order_sn . '');
        $result = json_decode($result, true);

        $this->assign('list', $result['data']);

        $this->show();

    }

}
