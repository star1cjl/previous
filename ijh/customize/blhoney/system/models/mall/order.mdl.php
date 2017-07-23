<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Mall_Order extends Mdl_Table
{   
  
    protected $_table = 'mall_order';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,post_id,post_name,uid,product_jifen,product_number,product_price,order_status,contact,mobile,addr,clientip,dateline,closed,pay_code,pay_status,pay_time,pay_ip,lasttime';

    public function create($data)
    {
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        return $this->db->insert($this->_table, $data, true);
    }

    public function  get_payments(){
        return array(
            'wxpay' => '微信支付',
            'alipay' => '支付宝支付',
            'money' => '余额支付',
        );
    }

    //取消/退单 退回已支付的美币   (已金钱支付的订单用户不可取消，只能管理员取消)
    public function cancel($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = $this->detail($order_id))){
            return false;
        }else if($order['order_status'] == 0 ){ 
            if($this->update($order['order_id'], array('order_status'=>-1,'lasttime'=>__TIME))){
                $product_jifen = $order['product_jifen']; 
                if($product_jifen > 0){ //退回到用户美币
                    K::M('member/member')->update_jifen($order['uid'], $product_jifen, '商城订单(ID:'.$order['order_id'].')取消退回到美币');
                }
                if($from == 'admin'){
                    $log = '管理员取消商城订单(ID:'.$order['order_id'].')';
                }else if($from == 'shop'){
                    $log = '商家取消商城订单(ID:'.$order['order_id'].')';
                }else{
                    $log = '用户取消商城订单(ID:'.$order['order_id'].')';
                }
                // 库存退回处理
                $mall_order_product_items = K::M('mall/orderproduct')->items(array('order_id'=>$order_id));
                foreach ($mall_order_product_items as $k => $v) {
                    K::M('mall/product')->update_count($v['product_id'], 'sales', -$v['product_number']);
                    K::M('mall/product')->update_count($v['product_id'], 'sku', +$v['product_number']);
                }
                K::M('order/log')->create(array('type'=>-1, 'from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id'],'kind'=>'mall'));
          
                return true;
            }
        }
        return false;
    }

    public function set_payed($order_id, $trade=array())
    {        
        if(!$order = $this->detail($order_id)){
            return false;
        }else if($res = $this->db->update($this->_table, array('pay_status'=>1), "order_id='{$order_id}'", true)){
            $a = array('online_pay'=>1, 'pay_ip'=>__IP,'pay_time'=>__TIME,'lasttime'=>time(),'pay_code'=>$trade['pay_code']);
            $this->update($order_id, $a, true);
            K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'payment','log'=>'订单支付成功','type'=>2));
        }
        return $res;
    }
}
