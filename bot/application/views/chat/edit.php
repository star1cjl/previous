<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="webpage"> 
<meta name="keywords" content="kalcaddle">
<meta name="author" content="kalcaddle.">
  <head>
  	<title>KodExplorer - Powered by KodExplorer</title>
  	<link href="<?php echo $this->config->item('static_chat'); ?>/style/bootstrap.css?ver=3.12" rel="stylesheet"/>
	<link rel="stylesheet" href="<?php echo $this->config->item('static_chat'); ?>/style/font-awesome/css/font-awesome.css">
	<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo $this->config->item('static_chat'); ?>/style/font-awesome/css/font-awesome-ie7.css">
	<![endif]-->
	
	<link href="<?php echo $this->config->item('static_chat'); ?>/style/skin/metro/green_app_code_edit.css?ver=3.12" rel="stylesheet" id='link_css_list'/>
	
  </head>

  <body>
	<div class="edit_main" style="height: 100%;" oncontextmenu="return core.contextmenu();">
		<div class="tools">
			<div class="left">
				<!-- <div class="disable_mask"></div> -->
				<a class="toolMenu editMenuFile" href="javascript:;" draggable="false">文件</a>
				<a class="toolMenu editMenuEdit" href="javascript:;" draggable="false">编辑</a>
				<a class="toolMenu editMenuView" href="javascript:;" draggable="false">视图</a>
				<a class="toolMenu editMenuTools" href="javascript:;" draggable="false">工具</a>
				<a class="toolMenu editMenuHelp" href="javascript:;" draggable="false">帮助</a>
			</div>
			<div class="right">
				<a action="close" href="javascript:;" title="关闭"><i class="font-icon icon-remove"></i></a>
				<a action="fullscreen" href="javascript:;" title="全屏/退出全屏"><i class="font-icon icon-resize-full"></i></a>
			</div>
			<div style="clear:both"></div>
		</div><!-- end tools -->

		<!-- 主体部分 -->
		<div class="frame_left">
			<div class="edit_tab">
				<div class="tabs">
					<a  href="javascript:Editor.add()" class="add icon-plus"></a>
					<div style="clear:both"></div>
				</div>
			</div>
			<div class="edit_body">
				<div class="introduction">
					<div class="intro_left">
    <div class="tips blue">
        <h1> <span>丰富的功能</span> </h1>
        <p>代码自动提示</p>
        <p>多主题：选择你喜欢的编程风格</p>
        <p>自定义字体：适合种场景下使用</p>
        <p>多光标编辑,块编辑等媲美sublime的在线编程体验</p>
        <p>代码块折叠、展开；自动换行</p>
        <p>支持多标签,拖动切换顺序;</p>
        <p>维持多个文档、查找替换；历史记录；</p>
        <p>自动补全[],{},(),"",''</p>
        <p>在线实时预览,使您爱上在线编程！</p>
        <p>zendcodeing支持,写代码健步如飞</p>
        <p>更多功能,等待你的发现……</p>
    </div>
    <div class="tips orange">
        <h1> <span>多种代码高亮</span> </h1>
        <p>前端：html,JavaScript,css,less,sass,scss</p>
        <p>web开发：php,perl,python,ruby,elang,go...</p>
        <p>传统语言：java,c,c++,c#,actionScript,VBScript...</p>
        <p>其他：markdown,shell,sql,lua,xml,yaml...</p>
    </div>
</div>
<div class="intro_right">
    <div class="tips green">
        <h1> <span>快捷键操作</span> </h1>
    <pre>常用快捷键：
      ctrl+s 保存      
      ctrl+a 全选      ctrl+x 剪切   
      ctrl+c 复制      ctrl+v 粘贴
      ctrl+z 撤销      ctrl+y 反撤销
      ctrl+f 查找      ctrl+f+f 替换
      alt+0 折叠所有   alt+shift+0 展开所有
      esc [退出搜索,取消自动提示...]</pre>
    <pre>选择：
      鼠标框选——拖动
      shift+home/end/up/left/down/right  
      shift+pageUp/PageDown 上下翻页选中
      ctrl+shift+ home/end  当前光标到头尾
      alt+鼠标拖动  块选择</pre>
    <pre> 光标移动：
      home/end/up/left/down/right
      ctrl+home/end 光标移动到文档首/尾
      ctrl+p 跳转到匹配的标签
      pageUp/PageDown 光标上下翻页
      alt+left/right 光标移动到行首位
      shift+left/right  光标移动到行首&尾
      ctrl+l 跳转到指定行
      ctrl+alt+up/down  上(下)增加光标</pre>
    <pre>编辑：
      ctrl+/ 注释&取消注释  ctrl+alt+a 左右对齐      
      table tab对齐         shift+table 整体前移table
      delete 删除           ctrl+d 删除整行
      ctrl+delete           删除该行右侧单词
      ctrl/shift+backspace  删除左侧单词
      alt+shift+up/down     复制行并添加到上(下面)面
      alt+delete            删除光标右侧内容
      alt+up/down           当前行和上一行(下一行交换)
      ctrl+shift+d          复制行并添加到下面
      ctrl+delete           删除右侧单词
      ctrl+shift+u 转换成小写 ctrl+u 选中内容转换成大写</pre>
    </div>
</div>

					<div style="clear:both"></div>
				</div>
				<div class="tabs"></div>
			</div>			
		</div>
		<!-- 预览 -->
		<div class="frame_right">
			<div class="resize"></div>
			<div class="right_main">
				<div class="function_list" style="display:none;">
					<div class="function_list_tool">
						<div class="box">
						<span> <i class="icon-code"></i> 函数列表</span>
						<a action="close_preview" href="javascript:preview.close();"><i class="font-icon icon-remove"></i></a>
						</div>
					</div>
					<div class="function_list_parent">
						<div class="function_list_box"></div>
					</div>
				</div>
				<div class="preview" style="display:none;">
					<div class="preview_tool">
						<input type="text" value="" />
						<div class="box">
							<a action="refresh" href="javascript:preview.refresh();" title="刷新"><i class="font-icon icon-refresh"></i></a>
							<a action="open_ie" href="javascript:preview.openUrl();" target="_blank" title="浏览器打开"><i class="font-icon icon-globe"></i></a>
							<a action="close_preview" href="javascript:preview.close();" title="关闭"><i class="font-icon icon-remove"></i></a>
						</div>
					</div>
					<div class="preview_frame">
						<iframe src="" style="width:100%;height:100%;border:0;"></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>

<script src="/index.php/chat/user/common_js#id=<?php echo rand_string(8);?>"></script>
<script src="<?php echo $this->config->item('static_chat'); ?>/js/lib/seajs/sea.js?ver=3.12"></script>
<script src="<?php echo $this->config->item('static_chat'); ?>/js/lib/ace/src-min-noconflict/ace.js?ver=3.12"></script>
<script src="<?php echo $this->config->item('static_chat'); ?>/js/lib/ace/src-min-noconflict/ext-language_tools.js?ver=3.12"></script>
<script type="text/javascript">
	G.frist_file = "<?php echo $filename;?>";
	G.code_config = <?php echo $editor_config;?>;
	G.code_theme_all = "chrome,clouds,crimson_editor,eclipse,github,solarized_light,tomorrow,xcode,ambiance,idle_fingers,monokai,pastel_on_dark,solarized_dark,tomorrow_night_blue,tomorrow_night_eighties";
	seajs.config({
		base: "<?php echo $this->config->item('static_chat'); ?>/js/",
		preload: ["lib/jquery-1.8.0.min"],
		map:[
			[ /^(.*\.(?:css|js))(.*)$/i,'$1$2?ver='+G.version]
		]
	});
	seajs.use("app/src/edit/main");	
</script>
</body>
</html>