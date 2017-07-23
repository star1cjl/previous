<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Mall extends Ctl
{
    protected function getmallcart()
    {
        if($cart = (array) json_decode(str_replace("\\\"", "\"", urldecode($this->system->cookie->get('mall'))), true)) {
            foreach($cart as $k=>$v) {
                $pids[] = $v['product_id'];
                $nums[$v['product_id']] = $v['num'];
            } 
            $product_items = K::M('mall/product')->items(array('product_id'=>$pids));

            foreach($product_items as $k=>$val) {
                $product_items[$k]['num'] = $nums[$val['product_id']];
            }
            return $product_items;
        }else {
            return NULL;
        }
        
    }

    //商城订单提交页面  
	public function ordersub()
    {
        if(!$m_addr = K::M('member/addr')->find(array('uid'=>$this->uid,'is_default'=>1))){
            $m_addr = K::M('member/addr')->find(array('uid'=>$this->uid));
        }
        $cart = $this->getmallcart();
        if(empty($cart)){
            $this->msgbox->add('你还没有选择兑换商品呢',223)->response();
        }
        $this->pagedata['maddr'] = $m_addr;
        $this->tmpl = 'mall/ordersub.html';
    }

    public function create() {
        $this->check_login();
        if(!$addr_id = (int)$this->GP('addr_id')){
            $this->msgbox->add('请选择一个收货地址',210);
        }else if(!$addr_detail = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('收货地址不存在',211);
        }else if($addr_detail['uid'] != $this->uid){
            $this->msgbox->add('非法操作',212);
        }else{          
            if($this->MEMBER['jifen'] == 0) {
                $this->msgbox->add('你当前的美币为0', 213)->response();
            }else {
                $mall_product_items = $mall_order_product = array();
                $mall_product_items = $this->getmallcart();
                if(empty($mall_product_items)) {
                    $this->msgbox->add('你未选择商品', 214)->response;
                }else {
                    foreach($mall_product_items as $k => $v) {
                        if($v['sku'] < $v['num']) {
                            $this->msgbox->add('商品库存不足',215)->response();
                        }else {
                            $mall_order_product[$k] = array(
                                'product_id'    => $k,
                                'product_name'  => $v['title'],
                                'product_jifen' => $v['jifen'],
                                'product_price' => $v['price'],
                                'product_number'=> $v['num'],
                                'amount'        => $v['price'] * $v['num'],
                                'integral'      => $v['jifen'] * $v['num'],
                            );
                            $product_number += $v['num'];
                            $product_price += $v['price'] * $v['num'];
                            $product_jifen += $v['jifen'] * $v['num'];
                        }
                    }
                    if($product_jifen > $this->MEMBER['jifen']) {
                        $this->msgbox->add('你的美币不足',216)->response();
                    }else {
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
                            $this->msgbox->add('订单提交成功');
                            $this->msgbox->set_data('data', array('order_id'=>$order_id));
                        }
                    }
                }
            }     
        }
    }
	
    public function index($cate_id)
    {
        $filter = $items = array();
        if($cate_id = (int)$cate_id){
            $filter['cate_id'] = $cate_id;
        }
        $filter['closed'] = 0;        
        if($items = K::M('mall/product')->items($filter, null, 1, 500,$count)){
            foreach($items as $k=>$v){
                $items[$k] = $this->filter_fields('product_id,cate_id,title,photo,jifen,sales,sku,price', $v);
            }
        }
        $cates = K::M('mall/cate')->items();
        $this->pagedata['cates']= $cates;
        $this->pagedata['items'] = $items;
        $this->pagedata['total_price'] = $total_price;
        $this->tmpl = 'mall/index.html';
    }

    
    public function items(){
        $cate_id = (int)$this->GP('cate_id');
        if($cate_id){
            $filter['cate_id'] = $cate_id;
            $this->pagedata['cate_id'] = $cate_id;
        }
        $filter['closed'] = 0;        
        if($items = K::M('mall/product')->items($filter, null, 1, 500,$count)){
            foreach($items as $k=>$v){
                $items[$k] = $this->filter_fields('product_id,cate_id,title,photo,jifen,sales,sku,price', $v);
            }
        }
        $cates = K::M('mall/cate')->items();
        $this->pagedata['cates']= $cates;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'mall/items.html';
    }

    public function detail($product_id)
    {
        $product_id = (int)$product_id;
        if(!$product_id){
            $this->error(404);
        }else if(!$detail = K::M('mall/product')->detail($product_id)){
            $this->error(404);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'mall/detail.html';
        }
    }
    
    
    public function exchange(){
        $product_id = $this->GP('product_id');
        $exchange = K::M('mall/product')->detail($product_id);
        $addr_id = $this->GP('addr_id');
 
        if($addr_id){
            $my_addr = K::M('member/addr')->detail($addr_id);
            if($my_addr['uid'] == $this->uid){
                $this->pagedata['my_addr'] = $my_addr;
            }
        }

        $addr_count = K::M('member/addr') -> count(array('uid'=>$this->uid));
        $this->pagedata['addr_count'] = $addr_count;
        $this->pagedata['exchange'] = $exchange;
        $this->tmpl = 'mall/exchange.html';
    }
    
    
    public function handle(){
        
        if(!$product_id = (int)$this->GP('product_id')){
            $this->msgbox->add('错误的商品ID！',211);
        }else if(!$product_number = $this->GP('product_number')){
            $this->msgbox->add('没有选择兑换的数量！',212);
        }else if(!$addr_id = $this->GP('addr_id')){
            $this->msgbox->add('错误的地址！',213);
        }else if(!$product = K::M('mall/product')->detail($product_id)){
            $this->msgbox->add('商品错误！',214);
        }else if(!$addr = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('地址错误！',215);
        }else if($addr['uid'] != $this->uid){
            $this->msgbox->add('地址非法！',216);
        }else if(!$this->uid){
            $this->msgbox->add('您还没有登录！',101);
        }else if($this->MEMBER['jifen'] < ($product['jifen']*$product_number)){
            $this->msgbox->add('您的美币不足！',217);
        }else if($product['sku']< $product_number){
            $this->msgbox->add('商品库存不足！',218);
        }else{
            $data = array(
                'uid'=>$this->uid,
                'product_id' => $product_id,
                'product_name' => $product['title'],
                'product_jifen' => $product['jifen']*$product_number,
                'product_number' => $product_number,
                'contact' => $addr['contact'],
                'mobile' => $addr['mobile'],
                'addr' => $addr['addr'].$addr['house'],
                
            );
            $data2['sku'] = $product['sku'] - $product_number;
            $data2['sales'] = $product['sales'] + $product_number;
            if($create = K::M('mall/order')->create($data)){
                //扣除用户的美币余额
                if(K::M('member/member')->update_jifen($this->uid,-($product['jifen']*$product_number),'兑换商品消耗美币')){
                    K::M('mall/product')->update($product['product_id'], $data2);
                    $this->msgbox->add('兑换成功！');
                }
            }else{
                $this->msgbox->add('兑换失败！');
            }
        }
        
    }

}