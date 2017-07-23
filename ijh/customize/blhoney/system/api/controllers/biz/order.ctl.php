<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Biz_Order extends Ctl_Biz
{
    // 订单管理接口
    public function items($params)
    {
        $filter = array();
        $orderby = array('order_id'=>'DESC');    
        if(in_array($params['status'], array(1, 2, 3))){
            switch ($params['status']) {
                case 1 : //待接单
                    $filter['order_status'] = 0;
                    $filter['pay_status'] = 1;
                    $filter['spend_status'] = 0;
                    break;
                case 2 : //待核销
                    $filter['order_status'] = 1;
                    $filter['pay_status'] = 1;
                    $filter['spend_status'] = 0;
                    break;
                case 3 : //已完成
                    $filter['order_status'] = 8;
                    $filter['pay_status'] = 1;
                    $filter['spend_status'] = 1;       
                    break;
            }
        }
        $filter['closed'] = 0;
        $filter['shop_id'] = $this->shop_id;     
        $page = max((int)$params['page'], 1);
        $limit = 10;
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $order_ids = array();
            foreach($items as $k=>$v){
                $order_ids[] = $v['order_id'];
                $items[$k] = $this->filter_fields('order_id,contact,mobile,product_price,amount,first_youhui,hongbao,comment_status,dateline,ordered_time',$v);
                $items[$k]['comment_info'] = array('comment_id'=>0,'shop_id'=>0,'uid'=>0,'order_id'=>0,'content'=>'','reply'=>'','reply_time'=>0,'dateline'=>0);
            }
            if($order_ids){
                if($order_product_list = K::M('order/product')->items(array('order_id'=>$order_ids), null, 1, 50)){
                    foreach($order_product_list as $k=>$v){
                        $items[$v['order_id']]['product_info'][] = $this->filter_fields('product_name,product_price', $v);
                    }
                }
            }
            if($order_ids){
                if($comment_list = K::M('shop/comment')->items(array('order_id'=>$order_ids), null, 1, $limit)){
                    foreach($comment_list as $k=>$v){
                        $items[$v['order_id']]['comment_info'] = $this->filter_fields('comment_id,shop_id,uid,order_id,content,reply,reply_time,dateline', $v);
                    }
                }
            }

        }
        $this->msgbox->add('success');
        $this->msgbox->set_data('data', array('items'=>array_values($items)));
    }

    public function detail($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add(L('订单不存在或已被删除'), 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add(L('非法操作'), 213);
        }else{
            $order = $this->filter_fields('order_id,hongbao,first_youhui,product_price,amount,pay_code,pay_status,contact,mobile,dateline,ordered_time',$order);
            if(isset($order['pay_code']) && $order['pay_status']==1) {
                $payments = K::M('order/order')->get_payments();
                $order['pay_method'] = $payments[$order['pay_code']];
            }else {
                $order['pay_method'] = '未支付';
            }
            if(!$products = K::M('order/product')->items(array('order_id'=>$order_id))){
                $products = array();
            }
            $order['product_info'] = array_values($products);
            if($reply = K::M('shop/comment')->detail_by_order($order['order_id'])) {
                $order['comment_info'] = $this->filter_fields('comment_id,shop_id,uid,order_id,score,score_fuwu,score_kouwei,pei_time,content,have_photo,reply,reply_ip,reply_time,dateline,mobile,face,photo', $reply);
                $reply['have_photo'] = 0;
                $reply['photo_list'] = array();
                if($photo_list = K::M('shop/photo')->items(array('comment_id'=>$reply['comment_id']), null, 1, 5)) {                    
                    foreach($photo_list as $k=>$v){
                        $reply['have_photo'] = 1;
                        $reply['photo_list'][] = $v['photo'];
                    }
                }
                $order['comment_info'] = $reply;
            }else{
                $order['comment_info'] = array('comment_id'=>0);
            }
            $this->msgbox->set_data('data', $order);
            $this->msgbox->add('success');
        }        
    }

    public function jiedan($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在', 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除', 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($this->biz['verify_name'] != 1){
            $this->msgbox->add('您还没有认证通过或被拒绝，不可以接单',212);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add('代购订单不可接单', 214);
        }else if($order['online_pay'] && !$order['pay_status']){
            $this->msgbox->add('订单未支付不可接单', 215);
        }else if((int)$order['order_status'] !== 0){
            $this->msgbox->add('订单状态不可接单', 216);
        }else if(K::M('order/order')->update($order_id, array('order_status'=>1,'lasttime'=>time()))){
            //订单日志
            K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'shop', 'log'=>'商家已接单', 'type'=>'3'));
            $this->msgbox->add('success');
        }
    }

    //抢单
    public function qiang($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在', 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除', 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($this->biz['verify_name'] != 1){
            $this->msgbox->add('您还没有认证通过或被拒绝，不可以接单',212);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add('代购订单不可抢送', 214);
        }else if($order['staff_id']>0){
            $this->msgbox->add('配送员已经接单不可抢送', 215);
        }else if($order['online_pay'] && !$order['pay_status']){
            $this->msgbox->add('订单未支付不可抢送', 216);
        }else if(!in_array($order['order_status'], array(0, 1, 2))){
            $this->msgbox->add('订单状态不可抢送', 217);
        }else if(K::M('order/order')->update($order_id, array('order_status'=>3, 'pei_type'=>0))){
            //订单日志
            K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'shop', 'log'=>'商家自己开始配送', 'type'=>3));
            $this->msgbox->add('success');
            $orderdetail = K::M('order/order')->detail($order_id);
            $this->msgbox->set_data('data', array('order'=>$orderdetail));
        }
    }

    public function cancel($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在', 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除', 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add('代购订单不可取消', 214);
        }else if($order['order_status'] != 0 ){
            $this->msgbox->add('订单状态不可取消', 215);
        }else if(K::M('order/order')->cancel($order_id, $order, 'shop')){
            $this->msgbox->add('success');
        }
    }

    public function pei($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在', 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除', 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add('代购订单不可抢送', 214);
        }else if($order['staff_id']>0){
            $this->msgbox->add('配送员已经接单不可操作', 214);
        }else if(!in_array($order['order_status'], array(1,2))){
            $this->msgbox->add('订单状态不可发货', 215);
        }else if(K::M('order/order')->update($order_id, array('order_status'=>3, 'pei_type'=>0, 'lasttime'=>__TIME))){
            //订单日志 v-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成
            K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'shop', 'log'=>'商家开始配送', 'type'=>4));
            $this->msgbox->add('success');
            $orderdetail = K::M('order/order')->detail($order_id);
            $this->msgbox->set_data('data', array('order'=>$orderdetail));
        }
    }

    public function delivered($params)
    {
        if(!$order_id = (int)$params['order_id']){
            $this->msgbox->add('订单不存在', 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除', 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($order['staff_id']>0){
            $this->msgbox->add('订单由配送员配送不可操作', 214);
        }else if((int)$order['order_status'] !== 3 ){
            $this->msgbox->add('订单状态不可设置为已送达', 215);
        }else if(K::M('order/order')->update($order_id, array('order_status'=>4, 'lasttime'=>__TIME))){
            //订单日志 v-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成
            K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'shop', 'log'=>'订单已送达', 'type'=>5));
            $this->msgbox->add('success');
        }
    }

    public function batch($params)
    {

    }

    //订单提醒
    public function tixing($params)
    {
        $this->check_login();
        if(!$dateline = (int)$params['dateline']){
            //如果没有传时间戳设置为15分钟前
            $dateline = __TIME - 900;
        }
        //$lasttime = K::M('order/order')->get_last_dateline();
        $filter = array('shop_id'=>$this->shop_id,'order_status'=>0,'lasttime'=>">:".$dateline);
        $filter[':OR'] =  array('pay_status'=>1, 'online_pay'=>0);
        $filter['pei_type'] = array(0, 1);
        $filter['closed'] = 0;
        $new_order = (int)K::M('order/order')->count($filter);
        $cui_order = (int)K::M('order/order')->count(array('shop_id'=>$this->shop_id, 'pei_type'=>array(0,1), 'cui_time'=>">:".$dateline, 'order_status'=>array(1,2,3,4),'closed'=>0));
   
        $new_msg = (int)K::M('shop/msg')->items(array('shop_id'=>$this->shop_id, 'dateline'=>">:".$dateline, 'is_read'=>0));
        
        $this->msgbox->set_data('data', array('new_order'=>$new_order, 'cui_order'=>$cui_order, 'new_msg'=>$new_msg, 'dateline'=>__TIME));
        $this->msgbox->add('success');
    }    

    // 订单核销
    public function spend($params)
    {
        $this->check_login();
        if(!$order_id = (int) $params['order_id']) {
            $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',212);
        }else if($order['closed'] != 0) {
            $this->msgbox->add('订单不存在或已被删除',213);
        }else if($order['shop_id'] != $this->shop_id) {
            $this->msgbox->add('非法操作',214);
        }else if(!($order['order_status']==1 && $order['pay_status']==1)) {
            $this->msgbox->add('订单状态不可核销',215);
        }else if($order['spend_status'] == 1) {
            $this->msgbox->add('订单已核销，请勿重复操作',216);
        }else if($order['spend_number'] != $params['number']) {
            $this->msgbox->add('核销密码不正确',217);
        }else if(K::M('order/order')->confirm($order_id,$order,'shop')) {
            if(K::M('order/order')->update($order_id, array('spend_status'=>1))) {
                $this->msgbox->add('success');
            }
        }
    }

    // 检测核销订单和核销密码
    public function checkspend($params)
    {
        $this->check_login();
        if(!$order_id = (int) $params['order_id']) {
            $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',212);
        }else if($order['closed'] != 0) {
            $this->msgbox->add('订单不存在或已被删除',213);
        }else if($order['shop_id'] != $this->shop_id) {
            $this->msgbox->add('非法操作',214);
        }else if(!($order['order_status']==1 && $order['pay_status']==1)) {
            $this->msgbox->add('订单状态不可核销',215);
        }else if($order['spend_status'] == 1) {
            $this->msgbox->add('订单已核销，请勿重复操作',216);
        }else if($order['spend_number'] != $params['number']) {
            $this->msgbox->add('核销密码不正确',217);
        }else {  
            $this->msgbox->add('success');
        }
    }

}
