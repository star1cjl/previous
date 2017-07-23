<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Mall_Order extends Ctl {

    public function index($page = 1) {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['order_id']) {
                $filter['order_id'] = $SO['order_id'];
            }
            if ($SO['uid']) {
                $filter['uid'] = $SO['uid'];
            }
            if ($SO['product_name']) {
                $filter['product_name'] = "LIKE:%" . $SO['product_name'] . "%";
            }
            if ($SO['product_jifen']) {
                $filter['product_jifen'] = $SO['product_jifen'];
            }
            if ($SO['contact']) {
                $filter['contact'] = "LIKE:%" . $SO['contact'] . "%";
            }
            if ($SO['mobile'] && is_array($SO['mobile'])) {
                $filter['mobile'] = $SO['mobile'];
            } else if (K::M("verify/check")->ids($SO['mobile'])) {
                $filter['mobile'] = "IN:" . $SO['mobile'];
            }
            if ($SO['addr']) {
                $filter['addr'] = "LIKE:%" . $SO['addr'] . "%";
            }
            if (is_array($SO['dateline'])) {
                if ($SO['dateline'][0] && $SO['dateline'][1]) {
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }
        }

        $uids = $orderids = array();
        $filter['order_status'] = array(0,5,8);
        $filter['pay_status'] = 1;
        $filter['closed'] = 0;
        if ($items = K::M('mall/order')->items($filter, array('order_id'=>'desc'), $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
            foreach($items as $k=>$v) {
                $items[$k] = $this->filter_fields('order_id,uid,contact,mobile,addr,product_price,product_jifen,clientip,dateline,order_status',$v);
                $orderids[$v['order_id']] = $v['order_id'];
                $uids[$v['uid']] = $v['uid'];
            }
        }
    
        if(!empty($uids)){
            $this->pagedata['members'] = K::M('member/member')->items_by_ids($uids);
        }    
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:mall/order/items.html';
    }

    public function so() {
        $this->tmpl = 'admin:mall/order/so.html';
    }

    public function detail($order_id = null) {
        if (!$order_id = (int) $order_id) {
            $this->msgbox->add('未指定要查看内容的ID', 211);
        } else if (!$detail = K::M('mall/order')->detail($order_id)) {
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        } else {
            $this->pagedata['products'] = K::M('mall/orderproduct')->items(array('order_id'=>$order_id));
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:mall/order/detail.html';
        }
    }

    public function create() {
        if ($data = $this->checksubmit('data')) {

            if ($order_id = K::M('mall/order')->create($data)) {
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?mall/order-index.html');
            }
        } else {
            $this->tmpl = 'admin:mall/order/create.html';
        }
    }

    public function edit($order_id = null) {
        if (!($order_id = (int) $order_id) && !($order_id = $this->GP('order_id'))) {
            $this->msgbox->add('未指定要修改的内容ID', 211);
        } else if (!$detail = K::M('mall/order')->detail($order_id)) {
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        } else if ($data = $this->checksubmit('data')) {

            if (K::M('mall/order')->update($order_id, $data)) {
                $this->msgbox->add('修改内容成功');
            }
        } else {
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:mall/order/edit.html';
        }
    }


    public function delete($order_id = null) {
        if ($order_id = (int) $order_id) {
            if (!$detail = K::M('mall/order')->detail($order_id)) {
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            } else {
                if (K::M('mall/order')->delete($order_id)) {
                    $this->msgbox->add('删除内容成功');
                }
            }
        } else if ($ids = $this->GP('order_id')) {
            if (K::M('mall/order')->delete($ids)) {
                $this->msgbox->add('批量删除内容成功');
            }
        } else {
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    
    // 商城订单发货
    public function deliver($order_id = null) {
        if ($order_id = (int) $order_id) {
            if (!$detail = K::M('mall/order')->detail($order_id)) {
                $this->msgbox->add('等待发货的订单不存在', 211);
            } else {
                if (K::M('mall/order')->batch($order_id, array('order_status' => 5))) {
                    $this->msgbox->add('发货成功');
                }
            }
        } else if ($ids = $this->GP('order_id')) {
            if (K::M('mall/order')->batch($ids, array('order_status' => 5))) {
                $this->msgbox->add('批量发货成功');
            }
        } else {
            $this->msgbox->add('未指定要发货的订单', 401);
        }
    }
    
    // 编辑运单
    public function post_edit($order_id=null)
    {
        if (!($order_id = (int) $order_id) && !($order_id = $this->GP('order_id'))) {
            $this->msgbox->add('未指定要修改的内容ID', 211);
        } else if (!$detail = K::M('mall/order')->detail($order_id)) {
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        } else if ($data = $this->checksubmit('data')) {
            if (K::M('mall/order')->update($order_id, $data)) {
                $this->msgbox->add('修改内容成功');
            }
        } else {
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:mall/order/post_edit.html';
        }
    }
 }
