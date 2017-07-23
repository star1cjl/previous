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
  'log_id' => 
  array (
    'field' => 'log_id',
    'label' => 'log_id',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '店铺',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'shop',
    'comment' => '',
    'default' => '',
    'SO' => '=',
  ),
  'platform' => 
  array (
    'field' => 'platform',
    'label' => '所属平台',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'select',
    'comment' => '',
    'default' => 'baidu',
    'SO' => '=',
  ),
  'api_id' => 
  array (
    'field' => 'api_id',
    'label' => '接口id',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '系统根据 platform 和 api_id 来解析数据,',
    'default' => '',
    'SO' => '=',
  ),
  'api_url' => 
  array (
    'field' => 'api_url',
    'label' => '接口url',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => 'like',
  ),
  'request_body' =>
  array (
    'field' => 'request_body',
    'label' => '提交数据',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => 'like',
  ),
  'api_status' => 
  array (
    'field' => 'api_status',
    'label' => '解析数据状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => 'true/false',
    'default' => 'false',
    'SO' => '=',
  ),
  'log_ip' => 
  array (
    'field' => 'log_ip',
    'label' => 'log_ip',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => 'dateline',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
);