<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>审定数据包管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#nav ul {}
#nav ul li {display:inline; }
</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">

<div id="nav">
<ul>
<li><a href='editpackage.php' target="_blank" >录入数据包管理</a> </li>
<li><a href='revisepackage.php' target="_blank" >编辑数据包管理</a> </li>
<li><a href='approvepackage.php' target="_blank" >审定数据包管理</a> </li>
</ul>
</div>
<!-- 
<div class="page-header">
    <h1>录入数据包列表</h1>
</div>
 -->
<div id="topPager">
	<div class="ypager"></div>
</div>
<table id="packagerows" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
	<th>数据包名</th>
	<th>用户序号</th>
	<th>用户帐号</th>
	<th>姓名</th>
	<th>状态</th>
	<th>日期</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
  
</tbody>
</table>
<div id="bottomPager">
	<div class="ypager"></div>
</div>
</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">
$(function () {
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"_get_approvepackage_count.php",
			"pageSize":30,
			"postData":{},
			"onFill":function(rows){
				var role = <?php echo $_SESSION ['UserId']; ?>;
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].PackageId + "' data-id='" + rows[i].PackageId + "'><td style='width:150px;'>" 
			        + rows[i].PackageName + "</td> <td style='width:60px;'>" 
			        + rows[i].UserId + "</td><td style='width:60px;'>" 
			        + rows[i].Username + "</td><td>" 
			        + rows[i].RealName + "</td><td>" 
			        + rows[i].Status + "</td><td>" 
			        + rows[i].CreateDate + "</td> <td style='width:30px;'><a href='javascript:void(0);' title='详情' onclick=\"seeMessage('" + rows[i].PackageId + "');\" >详情</a></td></tr>\n";
			    }
			    var tableBody = $("#packagerows tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(opts);
});		
</script>
</body>
</html>