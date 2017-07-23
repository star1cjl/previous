<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'attr_id' => 
  array (
    'field' => 'attr_id',
    'label' => 'attr_ID',
    'pk' => true,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => 'attr_ID',
    'default' => '',
    'SO' => '=',
  ),
    'goods_id' => 
  array (
    'field' => 'goods_id',
    'label' => '商品ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '商品ID',
    'default' => '',
    'SO' => '=',
  ),
  'cloud_num' => 
  array (
    'field' => 'cloud_num',
    'label' => '云购期数',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '云购期数',
    'default' => '',
    'SO' => '=',
  ),
    'cate_id' => 
  array (
    'field' => 'cate_id',
    'label' => '分类ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '分类ID',
    'default' => '',
    'SO' => '=',
  ),
    'is_set' => 
  array (
    'field' => 'is_set',
    'label' => '是否后台设置',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否后台设置',
    'default' => '0',
    'SO' => false,
  ),  
     'is_fine' => 
  array (
    'field' => 'is_fine',
    'label' => '是否精品',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否精品',
    'default' => '0',
    'SO' => false,
  ),  
  'price' => 
  array (
    'field' => 'price',
    'label' => '价格',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '价格',
    'default' => '0',
    'SO' => 'between',
  ),
  'join' => 
  array (
    'field' => 'join',
    'label' => '已参与',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '已参与',
    'default' => '0',
    'SO' => 'between',
  ),    
  'win_uid' => 
  array (
    'field' => 'win_uid',
    'label' => '中奖用户',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '中奖用户',
    'default' => '0',
    'SO' => false,
  ),
  'win_number' => 
  array (
    'field' => 'win_number',
    'label' => '中奖号码',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '中奖号码',
    'default' => '',
    'SO' => false,
  ),
  'lottery_time' => 
  array (
    'field' => 'lottery_time',
    'label' => '中奖时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '中奖时间',
    'default' => '',
    'SO' => false,
  ),
  'lottery_ip' => 
  array (
    'field' => 'lottery_ip',
    'label' => '中奖IP',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '中奖IP',
    'default' => '',
    'SO' => 'between',
  ),
  'closed' => 
  array (
    'field' => 'closed',
    'label' => '删除标识',
    'pk' => false,
    'add' => false,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '删除标识',
    'default' => '',
    'SO' => false,
  ),  
    'status' => 
  array (
    'field' => 'status',
    'label' => '云购状态',
    'pk' => false,
    'add' => false,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '云购状态，0正在云购中 1已结束',
    'default' => '',
    'SO' => false,
  ), 
    'share_status' => 
  array (
    'field' => 'share_status',
    'label' => '晒单状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '晒单状态',
    'default' => '',
    'SO' => false,
  ),    
  'clientip' => 
  array (
    'field' => 'clientip',
    'label' => '创建IP',
    'pk' => false,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'clientip',
    'comment' => '创建IP',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => '创建时间',
    'pk' => false,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'dateline',
    'comment' => '创建时间',
    'default' => '',
    'SO' => false,
  ),
 'addr' => 
  array (
    'field' => 'addr',
    'label' => '收货地址',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '收货地址',
    'default' => '',
    'SO' => false,
  ),
);