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
<title>编辑数据包管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#nav ul {}
#nav ul li {display:inline; }
.progress{margin:0;}
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
	<th>姓名</th>
	<th>状态</th>
	<th>日期</th>
	<th>进度</th>
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
function submitPackage(id, total, unhandled, name){
	// _taskcontroller.php
	var r = true;
	if(unhandled > 0) {
		r = confirm("本数据包共" + total + "个词条，尚有" + unhandled + "个词条未处理，您确实要提交吗？" );
	}
	if (r==true){
		var postData = {"PackageId":id, "PackageName":name};
		jQuery.post("_taskcontroller.php?op=revisesubmit", postData, function (data) {
			var row = eval("(" + data + ")");
			//alert(data);
			jQuery("#row"+ id + " td:last a:last").remove();
		});
	}else{
	}
    
	//alert('待开发' + id);
}
function rejectPackage(id, name){
	var r = confirm("您确实要撤销此数据包吗？" );
	if (r==true){
		var postData = {"PackageId":id, "PackageName":name};
		jQuery.post("_taskcontroller.php?op=revisereject", postData, function (data) {
			var row = eval("(" + data + ")");
			//alert(data);
			jQuery("#row"+ id + " td:last a:last").remove();
		});
	}else{
	}
}
$(function () {
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"_get_revisepackage_pagination.php",
			"target":".ypager",
			"pageSize":30,
			"postData":{},
			"onFill":function(rows){
				var html ='';
				for (var i = 0; i < rows.length; i++) {
					var row = rows[i];
					row.Percent = row.Total == 0 ? 0 : Math.round(row.Handled/row.Total*100);
					if(row.Percent > 100) {row.Percent =  100;}
					if( row.Status != "0") {row.Percent =  100;}
					row.Warning = false;
					if(row.Status === "0" &&  (dateDiff(row.CreateDate, (new Date()), 'day') > warningDays) ){
						row.Warning = true;
					}
					//alert(typeof parseInt(row.Total))
			        html += "<tr id='row" + rows[i].PackageId + "' data-id='" + rows[i].PackageId + "'><td style='width:200px;'><span class='" + (row.Warning ? "text-error":"") + "'>" 
			        + rows[i].PackageName + (row.Warning ? "(超期)":"") + "</span></td> <td style='width:160px;'>" 
			        //+ rows[i].UserId + "</td><td style='width:60px;'>" 
			        //+ rows[i].Username + "</td><td>" 
			        + rows[i].RealName + "</td><td style='width:60px;'>" 
			        + enumerableData.getName(rows[i].Status, enumerableData.packageStatus)  + "</td><td style='width:140px;'>" 
			        + rows[i].CreateDate + "</td><td><div class='progress' id='progress" 
			        + row.PackageId + "'><div class='bar' style='width: " + row.Percent + "%;display:inline;'>" + row.Percent + "%</div></div></td> <td style='width:90px;'><a href='../word/reviseword.php?packageid=" 
			        + rows[i].PackageId + "' style='margin-left:10px;' title='详情'>详情</a>";
			        // Total, Unhandled, Handled
			        if(row.Status === "0") {
			        	if(parseInt(row.Handled) == 0){
				    	    html += "<a href='javascript:void(0);' style='margin-left:10px;' title='撤销' onclick=\"rejectPackage(" + rows[i].PackageId + ", '" + row.PackageName + "');\" >撤销</a>"
			        	} else {
				    	    html += "<a href='javascript:void(0);' style='margin-left:10px;' title='提交' onclick=\"submitPackage(" + rows[i].PackageId + ", " + row.Total + ", " + row.Unhandled + ", '" + row.PackageName + "');\" >提交</a>";
				    	}
			        }
				    html += "</td></tr>";
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