<?php /* Smarty version Smarty-3.1.8, created on 2016-08-16 11:31:49
         compiled from "admin:page/middle.html" */ ?>
<?php /*%%SmartyHeaderCode:49399670857b289257a51d6-43322380%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ec64d3edf8153b9363fd5c8616f390d5de64db7a' => 
    array (
      0 => 'admin:page/middle.html',
      1 => 1470380608,
      2 => 'admin',
    ),
  ),
  'nocache_hash' => '49399670857b289257a51d6-43322380',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_57b289257c1f73_34485096',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b289257c1f73_34485096')) {function content_57b289257c1f73_34485096($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<title>LVCMS旅游系统管理中心</title>
<style type="text/css">
<!--
* { margin:0; padding:0; border:0; }
body { background:yellow; background:url(data:image/gif;base64,R0lGODlhCgAFALMAAPb5/u/3/Ob1+qjN3/j7/t/49+j1+9339v///wAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAACgAFAAAEFHCgckwABA1JLdZcdWXbJH5lN2oRADs=) repeat-y left; }
a.ctr_close{background:url(data:image/gif;base64,R0lGODlhCgDIANUAAP///fb4++/3+v/+/fb7/6rO3qjN373a76nO4Of0/KzQ4Ojz/Pn7/jB6wd32+N36+OH4+KzR4+j4+qvQ4L3a7cLe7uD39+L1+93z9ub1+vf6//j9//f8/qbN4Of0+vn4/Of3+cHc8eX2++f2+8Da6zJ5vd32+i55ve76/N/3+ej2+fb5/uj1/TB7vO/3/N73+avP39739v3///j7/vX4+9/29t/49zB2vzF4vOj1+9339vf8/+b29v///wAAAAAAACwAAAAACgDIAAAG/0BFr/ZIuGicQUFINCKVzOIxuRxKn9XmFGp1UqNerhYL3mav3665TFa3x2m4GD0Pn+3sOP2+duvxfnV9MSYqAjQMMh1ChIaIioyFh4mLPY2TkJaSj5WXnJGOlKCYnZuimqGZnqerqqauqaWxo5+opLSsr7K3trWtu766uLC8v8PAubO9ycXCy8TBysbPyNDM0c3SztPc297a4Nni2OTX5tbo1erH7NTt3eHl6UI2OjkuKzM9CowWCygBdvSYIORFDRA3KmjoEUGIDggNSlDYAACGkBQncJD4oM9iDwcvcLQ4QEAGAnoYJNwIIdBAHkFv9rzkExOQHJp/+tycyTMQTlSYOWvqlOmz586iSI8qJbrUJtOnTqMOlSq0atCrQLP+3GoU6tSvVrV2pYqVa1KvYc02BVt27KBv486tc0cXXtx5deHKm2t37zu9cv/GC5x3MN5fQQAAOw==);width:10px;height:200px;overflow: hidden;position:absolute; top:50%; left:50%; margin:-100px 0 0 -5px; cursor:pointer;}
a.ctr_open{background:url(data:image/gif;base64,R0lGODlhCgDIANUAAOj1+9339r3a7ff8/v/+/d/29v3//zB6wejz/MLe7uf0/MHc8fn7/t36+DJ5vb3a7+j2+abN4N32+uL1++D39zB7vMDa66rO3u/3+i55ve76/PX4+zB2vzF4vKvQ4Pb4+/b5/u/3/OX2+/f8/+b1+t739vj7/qzQ4N/49////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAACgDIAAAG9MBTChUAhECm1ElINCKVzOIxuRxKn9XmFGp1UqNerhYL3mav3665TFa3x2m4GD0Pn+3sOP2+duvxfnV9cnxve3mChoCEiIV/g4eBjomPipCLkYySjZybnpqgmaKYpJemlqiVqpSsk66doaWprbCjp6uvn7azubG3tLqyuLXCwJolFAgaHyMpHkwJHH0lAg4HzM5RFh0ZcQ8VHW0lCxzLzc8pBQ0KIRsDBBdCJRIQGBsMBhHEv727w8H89vES+M/XQIAHDRb0Z4xhv2IPAyJcCJFgw4oTL0pUqDGhQ4sRPWLkGJLixo8ZS3Y0KfLkSJQkQcqUFAQAOw==);width:10px;height:200px;overflow: hidden;position:absolute; top:50%; left:50%; margin:-100px 0 0 -5px; cursor:pointer;};
-->
</style>
</head>
<script type="text/javascript">
var counter = 0;  //加个计数参数 
//伸缩frame 
function folder(){ 

       counter++; 
        if(parent.document.getElementById('frameset_main').cols=="0,10,*"){
            parent.document.getElementById('frameset_main').cols = "180,10,*";       
            document.getElementById('close').style.display="block";
            document.getElementById('open').style.display="none";
            
        }else{
            parent.document.getElementById('frameset_main').cols="0,10,*";
            document.getElementById('close').style.display="none";
            document.getElementById('open').style.display="block";
        }

}
</script>
<body>
<a class="ctr_close" alt="点击关闭" id="close" onclick="folder()"></a>
<a class="ctr_close" alt="点击展开"  id="open"style="display:none;" onclick="folder()"></a>
</body>
</html>
<?php }} ?>