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
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => 'ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => '=',
  ),
  'title' => 
  array (
    'field' => 'title',
    'label' => '店铺名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => 'like',
  ),
'info' =>
    array (
        'field' => 'info',
        'label' => '店铺简介',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => false,
        'list' => true,
        'type' => 'text',
        'comment' => '',
        'default' => '',
        'SO' => 'like',
    ),
  'phone' => 
  array (
    'field' => 'phone',
    'label' => '电话',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'phone',
    'comment' => '',
    'default' => '',
    'SO' => 'like',
  ),
  'logo' => 
  array (
    'field' => 'logo',
    'label' => 'LOGO',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'photo',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
'is_daofu' => 
  array (
    'field' => 'is_daofu',
    'label' => 'is_daofu',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否支持货到付款，0不支持，1支持',
    'default' => '',
    'SO' => 'false',
  ),
  'is_ziti' => 
  array (
    'field' => 'is_ziti',
    'label' => 'is_ziti',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否支持到店自提，0不支持，1支持',
    'default' => '',
    'SO' => 'false',
  ),
  'online_pay' => 
  array (
    'field' => 'online_pay',
    'label' => '是否支持在线支付',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否支持在线支付 0:不支持在线支付，1：支持在线支付',
    'default' => '',
    'SO' => false,
  ),
  'products' => 
  array (
    'field' => 'products',
    'label' => '商品数量',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'orders' => 
  array (
    'field' => 'orders',
    'label' => '订单数量',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'audit' => 
  array (
    'field' => 'audit',
    'label' => '审核',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'boolean',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'clientip' => 
  array (
    'field' => 'clientip',
    'label' => 'clientip',
    'pk' => false,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'clientip',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => 'dateline',
    'pk' => false,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'dateline',
    'comment' => '',
    'default' => '',
    'SO' => 'betweendate',
  ),
);