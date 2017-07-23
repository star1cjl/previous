<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Mall_Order extends Ctl
{
	// 美币商城下单
	public function create($params) 
	{
        $this->check_login();
        if(!$addr_id = (int)$params['addr_id']){
            $this->msgbox->add('请选择一个收货地址',210);
        }else if(!$addr_detail = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('收货地址不存在',211);
        }else if($addr_detail['uid'] != $this->uid){
            $this->msgbox->add('非法操作',212);
        }else if(!$pro_list = $params['products']) {
            $this->msgbox->add('你未选择商品',213);
        }else{
            if($this->MEMBER['jifen'] == 0) {
                $this->msgbox->add('你当前的美币为0', 214)->response();
            }
            $pro_list = explode(',',$pro_list);
            $product_ids = $product_numbers = $mall_order_product = $mall_product_items = array();
            foreach ($pro_list as $key => $val) {
                $local = explode(':',$val);
                $local[0] = (int) $local[0];
                $local[1] = (int) $local[1];
                if (!empty($local[0]) && !empty($local[1]) && $local[1] > 0) {
                    $product_ids[$local[0]] = $local[0];     //[product_id] => product_id
                    $product_numbers[$local[0]] = $local[1]; //[product_id] => product_number
                }
            }

            $mall_product_items = K::M('mall/product')->items_by_ids($product_ids);
            foreach($mall_product_items as $k => $v) {
                // 判断商品库存
                if($v['sku'] < $product_numbers[$k]) {
                    $this->msgbox->add('商品库存不足',215)->response();
                }else {
                    $mall_order_product[$k] = array(
                        'product_id'    => $k,
                        'product_name'  => $v['title'],
                        'product_jifen' => $v['jifen'],
                        'product_price' => $v['price'],
                        'product_number'=> $product_numbers[$k],
                        'amount'        => $v['price'] * $product_numbers[$k],
                        'integral'      => $v['jifen'] * $product_numbers[$k],
                    );
                    $product_number += $product_numbers[$k];
                    $product_price += $v['price'] * $product_numbers[$k];
                    $product_jifen += $v['jifen'] * $product_numbers[$k];
                }
            }


            if($product_jifen > $this->MEMBER['jifen']) {
                $this->msgbox->add('你的美币不足',216)->response();
            }
            $data = array(
                'uid' => $this->uid,
                'product_number'=> $product_number,
                'product_jifen' => $product_jifen,
                'product_price' => $product_price,
                'order_status' => 0,
                'contact'=> $addr_detail['contact'],
                'mobile' => $addr_detail['mobile'],
                'addr' => $addr_detail['addr'],
            );

            if($order_id = K::M('mall/order')->create($data)){
                foreach($mall_order_product as $key => $val) {
                    $val['order_id'] = $order_id;
                    K::M('mall/orderproduct')->create($val);
                    K::M('mall/product')->update_count($val['product_id'], 'sales', $val['product_number']);
                    K::M('mall/product')->update_count($val['product_id'], 'sku', -$val['product_number']);
                }
                K::M('member/member')->update_count($this->uid, 'orders', 1);
                K::M('member/member')->update_jifen($this->uid, -$product_jifen, '商城订单(ID:'.$order_id.')下单支付美币', 'member');
                K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'member','log'=>'订单已提交成功','type'=>1,'kind'=>'mall'));
                $this->msgbox->add('success');
                $this->msgbox->set_data('data', array('order_id'=>$order_id));
            }
        }
	}

    // 美币商城订单列表
	public function items($params)
	{
		$this->check_login();
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['closed'] = 0;
        $page = max((int)$params['page'], 1);
        if($items = K::M('mall/order')->items($filter, array('order_id'=>'DESC'), $page, 10, $count)) {
            foreach($items as $k=>$v) {
                $order_ids[] = $v['order_id'];              
                $items[$k] = $this->filter_fields('order_id,order_status,pay_status,pay_code,contact,mobile,addr,product_price,dateline',$v);
                if(isset($v['pay_code']) && $v['pay_status']==1) {
                    $payments = K::M('mall/order')->get_payments();
                    $items[$k]['pay_method'] = $payments[$v['pay_code']];
                }else {
                    $items[$k]['pay_method'] = '未支付';
                }
                $items[$k]['products'] = array();
            }
            if($order_products = K::M('mall/orderproduct')->items(array('order_id'=>$order_ids))) {
                foreach($order_products as $k=>$v) {
                    $product_ids[] = $v['product_id'];
                }
            }
            $products = K::M('mall/product')->items(array('product_id'=>$products_ids));
            foreach($items as $k=>$v) {
                foreach($order_products as $k2=>$v2) {
                    $v2['photo'] = $products[$v2['product_id']]['photo'];
                    if($v['order_id'] == $v2['order_id']) {
                        $items[$k]['products'][] = $this->filter_fields('product_name,product_price,product_jifen,product_number,photo',$v2);
                    }
                }
            }
        }else {
            $items = array();
        }
        $this->msgbox->add('success');
        $this->msgbox->set_data('data', array('items'=>array_values($items)));
	}

    // 取消订单
    public function cancle($params)
    {
        $this->check_login();
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('mall/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',212);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作',213);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已经取消，无需重复取消',214);
        }else if($order['order_status']==0 && $order['pay_status']==0){
            if(K::M('mall/order')->cancel($order_id, $order, 'member')) {
                $this->msgbox->add('success');
                $this->msgbox->set_data('data',array('sql'=>$this->system->db->SQLLOG()));
            }  
        }else{
            $this->msgbox->add('当前订单是不可取消的状态',215);  
        }
    }

    // 删除订单
    public function delete($params)
    {
        $this->check_login();
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('错误的订单ID',211);
        }else if(!$order = K::M('mall/order')->detail($order_id)){
            $this->msgbox->add('不存在的订单',212);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('你没有权限操作',213);
        }else if(in_array($order['order_status'],array(-1,8))) {
            if(K::M('mall/order')->delete($order_id)) {
                // 如果订单已取消则已经退过库存了此处无需再退库存
                if($order['order_status'] != -1) {
                    K::M('mall/order')->update($order_id, array('lasttime'=>__TIME));
                    $mall_order_product_items = K::M('mall/orderproduct')->items(array('order_id'=>$order_id));
                    foreach ($mall_order_product_items as $k => $v) {
                        K::M('mall/product')->update_count($v['product_id'], 'sales', -$v['product_number']);
                        K::M('mall/product')->update_count($v['product_id'], 'sku', +$v['product_number']);
                    }
                }
                $this->msgbox->add('success');
            }
        }else{
            $this->msgbox->add('订单为不可删除状态',214);
        }
    }

    // 确认收货
    public function receive($params)
    {
        if(!$order_id = (int)$params['order_id']) {
            $this->msgbox->add('订单不存在',210);
        }else if(!$order = K::M('mall/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',210);
        }else if($order['order_status'] != 5 && $order['pay_status'] != 1) {
            $this->msgbox->add('订单是不可确认收货的状态',211);
        }else {
            if(K::M('mall/order')->update($order['order_id'], array('order_status'=>8,'lasttime'=>__TIME))) {
                $this->msgbox->add('success');
            }
        }
    }

    // 订单详情
    public function detail($params)
    {
        $this->check_login();
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('mall/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',212);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作',213);
        }else{
            $order = $this->filter_fields('order_id,uid,product_jifen,product_number,product_price,order_status,contact,mobile,addr,clientip,dateline,closed,pay_code,pay_status,pay_time,pay_ip,lasttime',$order);
            if(isset($order['pay_code']) && $order['pay_status']==1) {
                $payments = K::M('mall/order')->get_payments();
                $order['pay_method'] = $payments[$order['pay_code']];
            }else {
                $order['pay_method'] = '未支付';
            }

            if($product_list = K::M('mall/orderproduct')->items(array('order_id'=>$order_id))){
                foreach($product_list as $k=>$v) {
                    $proids[] = $v['product_id'];
                    $product_list[$k] = $this->filter_fields('pid,product_id,product_name,product_price,product_jifen,product_number',$v);
                }
                if($mall_product = K::M('mall/product')->items(array('product_id'=>$proids))) {
                    foreach($mall_product as $k=>$v) {
                        foreach($product_list as $key=>$val) {
                            if($v['product_id'] == $val['product_id'] ) {
                                $product_list[$key]['photo'] = $v['photo'];
                            }
                        }
                    }
                }
            }else {
                $product_list = array();
            }

            $order['products'] = array_values($product_list);
            $this->msgbox->add('success');
            $this->msgbox->set_data('data', array('detail'=>$order));
        }
    }
}