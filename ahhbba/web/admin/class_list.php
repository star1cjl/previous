<?php 
require("up.php"); 
require("tools.php"); 

$search = "";
if (@$_GET['type'] <> "") {
    $type = $_GET['type'];

    if ($search == "") {
        $search .= " WHERE ";
    } else {
        $search .= " AND ";
    }
    $search .= " type  = '" . $type . "'  ";
}

if (@$_GET['classname'] <> "") {
    $classname = $_GET['classname'];
    if ($search == "") {
        $search .= " WHERE ";
    } else {
        $search .= " AND ";
    }
    $search .= " classname  like '%" . $classname . "%'  ";
}


$table = "hb_class";

$sql = "select * from " . $table . $search;

//请参考下面代码排序
//$sql="select * from " . $table . $search  ." order by id DESC";
//echo $sql;exit();
$result = mysql_query($sql);

$pagesize = 10;  //每页记录条数
$result_num = mysql_num_rows($result);

if ($result_num <= 0) {
    if ($search == "") {
        $word = "目前还没有记录!";
    } else {
        $word = "没有查到符合条件的记录!";
    }
} else {

    $maxpage = ceil($result_num / $pagesize);
    @$page = $_GET['page'];
    if (is_long($page) or $page == "") {
        $page = 1;
    } else {
        $page = (int) ($page);
    }

    if ($page < 1) {
        $page = 1;
    } else if ($page > $maxpage) {
        $page = $maxpage;
    }

    mysql_data_seek($result, ($page - 1) * $pagesize);
    $n = 1;
}
?>
<BODY>
    <H1><SPAN class=action-span>
    	<!--<A href="class_Edit.php?Result=Add&type=<?php echo $type; ?>">新增分类</A>-->
    	</SPAN> 
    	<SPAN 
            class=action-span1><A 
                href="class_list.php?type=<?php echo $type; ?>">豪邦后台管理中心</A> - 分类列表 
        </SPAN>
        <DIV style="CLEAR: both"></DIV></H1>


    <DIV class=form-div>
        <FORM name=searchForm action="class_list.php" method="get"><IMG height=22 alt=SEARCH 
                                                                        src="images/icon_search.gif" width=26 border=0> &nbsp;
            &nbsp;&nbsp;标题:
            <INPUT name=classname > <INPUT name=type type="hidden" value="<?php echo $type; ?>"> <INPUT type=submit value=" 搜索 "> </FORM></DIV>
    <DIV class=form-div>共搜索到<font color="#FF0000"><?php print($result_num); ?></font>条符合条件的信息</DIV>
    <?php if ($result_num <= 0) { ?>

    <center><BR><BR><?php print($word); ?></center>

<?php } else { ?>
    <FORM name=listForm action="" method=post><!-- start users list -->
        <DIV class=list-div id=listDiv><!--用户列表部分-->
            <TABLE cellSpacing=1 cellPadding=3 width="100%">
                <TBODY>
                    <TR>
                        <TH > 序号 </TH>
                        <TH >分类名称</TH>
                        <TH  >操作</TH>
                    </TR>
                    <?php while ($row = mysql_fetch_array($result)) { ?>  
                   
                        <TR>
                            <TD width=100><?php print($row["id"]); ?></TD>
                            <TD  ><?php print($row["classname"]); ?></TD>
                            <TD align=middle width=100>
                                <A title=编辑 href="class_Edit.php?Result=Modify&type=<?php echo $type; ?>&id=<?php print(HtmlOut($row["id"])); ?>">
                                    <IMG height=16 src="images/icon_edit.gif" width=16 border=0>
                                </A>
                                <A title=移除 href="javascript:confirm_redirect('您确定要删除该数据吗？',%20'class_del.php?act=remove&type=<?php echo $type; ?>&id=<?php print(HtmlOut($row["id"])); ?>')">
                                    <IMG height=16 src="images/icon_drop.gif" width=16  border=0>
                                </A> 
                            </TD>
                        </TR>
                        <?php
                        
                        $n++;
                        if ($n > $pagesize)
                            break;
                    }
                    ?> 
                    <TR>
                        <!--<TD colSpan=4>&nbsp;</TD>-->

                    </TR></TBODY></TABLE>
        </DIV><!-- end users list --></FORM>
    <?php
    if (@$_GET["title"] == "") {
        LastNextPage($maxpage, $page, "width=100% ", "<p  align=center class=font2>");
    } else {
        TurnPage($maxpage, $page, "width=100% ", "<p  align=center class=font2>");
    }
    ?>
<?php } ?>
<SCRIPT language=JavaScript>
<!--
    function CheckAll(form)
    {
        if (form.submitAllSearch.checked == true) {
            for (var i = 0; i < form.elements.length; i++)
            {
                var e = form.elements[i];
                e.checked = true;
            }
        }
        else {
            for (var i = 0; i < form.elements.length; i++)
            {
                var e = form.elements[i];
                e.checked = false;
            }
        }
    }
</script>
</BODY></HTML>