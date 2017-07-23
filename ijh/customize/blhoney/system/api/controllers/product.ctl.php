<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Product extends Ctl
{
    public function cate($params)
    {
        if(!$shop_id = (int)$params['shop_id']){
           $this->msgbox->add('商家不存在',211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
           $this->msgbox->add('商家不存在或已被删除', 212);
        }else if(empty($shop['audit'])){
           $this->msgbox->add('商户审核中不可访问', 213);
        }else{
            $tree = array();
            if($tree = K::M('product/cate')->tree($shop_id)) {
                foreach($tree as $k=>$v){
                    $v = $this->filter_fields('cate_id,parent_id,title,orderby,childrens', $v);
                    foreach($v['childrens'] as $kk=>$vv){
                        $v['childrens'][$kk] = $this->filter_fields('cate_id,parent_id,title,orderby', $vv);
                    }
                    $tree[$k] = $v;
                } 
            }
            $this->msgbox->set_data('data', array('items'=>array_values($tree)));
            $this->msgbox->add('success');
       }
    }

    public function items($params)
    {
        if(!$shop_id = (int)$params['shop_id']){
           $this->msgbox->add('商家不存在',211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('商家不存在或已被删除',212);
        }else{
            $tree = array();
            if($tree = K::M('product/cate')->tree($shop_id)) {
                foreach($tree as $k=>$v){
                    $v = $this->filter_fields('cate_id,parent_id,title,orderby,childrens', $v);
                    foreach($v['childrens'] as $kk=>$vv){
                        $v['childrens'][$kk] = $this->filter_fields('cate_id,parent_id,title,orderby', $vv);
                        $product = K::M('product/product')->items(array('cate_id'=>$vv['cate_id'],'shop_id'=>$shop_id,'closed'=>0));
                        $v['childrens'][$kk]['products'] = array_values($product);
                    }
                    $tree[$k] = $v;
                } 
            }
			
            if($product_list = K::M('product/product')->items(array('shop_id'=>$shop_id,'closed'=>0), null, 1, 5000)){
                $items = $product_list;
            }else{
                $items = array();
            }
			K::M("system/logs")->log('product.items.sql',$this->system->db->SQLLOG());
            if(CLIENT_OS == 'ANDROID') {
                if(empty($items)) {
                    $this->msgbox->add('没有数据',102);
                }else {
                    $this->msgbox->add('success');
                    $this->msgbox->set_data('data', array('tree'=>array_values($tree))) ;
                }
            }else if(CLIENT_OS == 'IOS'){
                $this->msgbox->add('success');
                $this->msgbox->set_data('data', array('tree'=>array_values($tree))) ;
            } 
        }      
    }

    // 商品搜索
    public function search($params)
    {
        if(!$shop_id = (int)$params['shop_id']) {
            $this->msgbox->add('商家不存在',210);
        }else if(!$title = $params['title']) {
            $this->msgbox->add('商品名称不能为空',211);
        }else if(!$product = K::M('product/product')->items(array('title'=>"LIKE:%".$title."%",'shop_id'=>$shop_id,'closed'=>0))) {
            $this->msgbox->add('商品不存在',212);
        }else {
            $this->msgbox->add('success');
            $this->msgbox->set_data('data', array('items'=>array_values($product)));
        }
    }
}
