<?php /* Smarty version Smarty-3.1.8, created on 2016-08-16 11:50:31
         compiled from "admin:shop/shop/recycle.html" */ ?>
<?php /*%%SmartyHeaderCode:172995541857b28d878f51e7-95803624%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98dcac6633e82da39c866ea2099c728b638d47ea' => 
    array (
      0 => 'admin:shop/shop/recycle.html',
      1 => 1470380627,
      2 => 'admin',
    ),
  ),
  'nocache_hash' => '172995541857b28d878f51e7-95803624',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pager' => 0,
    'MOD' => 0,
    'items' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_57b28d8797f938_91202686',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b28d8797f938_91202686')) {function content_57b28d8797f938_91202686($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_format')) include '/data/htdocs/blhoney_com/public_html/system/plugins/smarty/modifier.format.php';
if (!is_callable('smarty_function_link')) include '/data/htdocs/blhoney_com/public_html/system/plugins/smarty/function.link.php';
?><?php echo $_smarty_tpl->getSubTemplate ("admin:common/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="page-title">
	<table width="100%" align="center" cellpadding="0" cellspacing="0" >
		<tr>
			<td width="30" align="right"><img src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/images/main-h5-ico.gif" alt="" /></td>
			<th><?php echo $_smarty_tpl->tpl_vars['MOD']->value['title'];?>
</th>
			<td align="right"><td width="15"></td>
		</tr>
	</table>
</div>
<div class="page-data">	
	<form id="items-form">
	<input type="hidden" name="force" value="true" />
    <table width="100%" border="0" cellspacing="0" class="table-data table">
    <tr>
		<th class="w-100">SHOPID</th><th>商户名称</th><th>手机</th><th>状态</th><th>注册时间</th><th class="w-150">操作</th>
	</tr>
    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
    <tr>
		<td class="left"><label><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['shop_id'];?>
" name="shop_id[]" CK="PRI"/><?php echo $_smarty_tpl->tpl_vars['item']->value['shop_id'];?>
<label></td>
		<td class="left"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['item']->value['mobile'];?>
</td>
		<td><?php if ($_smarty_tpl->tpl_vars['item']->value['closed']=='3'){?>删除<?php }elseif($_smarty_tpl->tpl_vars['item']->value['closed']=='2'){?>锁定<?php }elseif($_smarty_tpl->tpl_vars['item']->value['closed']=='1'){?>禁言<?php }else{ ?>正常<?php }?></td>
		</td><td><?php echo smarty_modifier_format($_smarty_tpl->tpl_vars['item']->value['dateline']);?>
</td>
		<td>
			<?php echo smarty_function_link(array('ctl'=>"shop/shop:delete",'arg0'=>$_smarty_tpl->tpl_vars['item']->value['shop_id'],'arg1'=>'1','act'=>"mini:彻底删除",'confirm'=>"mini:确定要彻底删除吗？不可恢复",'title'=>"彻底删除",'class'=>"button"),$_smarty_tpl);?>

			<?php echo smarty_function_link(array('ctl'=>"shop/shop:regain",'arg0'=>$_smarty_tpl->tpl_vars['item']->value['shop_id'],'act'=>"mini:恢复",'confirm'=>"mini:确定要恢复该商家吗？",'title'=>"恢复",'class'=>"button"),$_smarty_tpl);?>

		</td>
	</tr>
    <?php }
if (!$_smarty_tpl->tpl_vars['item']->_loop) {
?>
     <tr><td colspan="20"><p class="text-align">没有数据</p></td></tr>
    <?php } ?>
    </table>
	</form>
	<div class="page-bar">
		<table>
			<tr>
			<td class="w-100"><label><input type="checkbox" CKA="PRI"/>&nbsp;&nbsp;全选</label></td>
			<td colspan="10" class="left"><?php echo smarty_function_link(array('ctl'=>"shop/shop:delete",'type'=>"button",'submit'=>"mini:#items-form",'confirm'=>"mini:确定要批量删除选中的内容吗?",'priv'=>"hide",'value'=>"批量删除"),$_smarty_tpl);?>
</td>
			<td class="page-list"><?php echo $_smarty_tpl->tpl_vars['pager']->value['pagebar'];?>
</td>
		</tr>
		</table>
	</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("admin:common/footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>