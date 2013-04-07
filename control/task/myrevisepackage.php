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
#categories {width:120px;}
#itemCount {width:100px;}
.progress{margin:0;}
</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">

<div>
   <form name="assignPackage" action="assignrevisepackage.php" method="post">
   <label for="categories" style="display: inline;">选择您要编辑的词条类别：</label><select id="categories" name="categories" style="margin-top: 6px;"></select>
   <label for="packageName" style="display: inline;">数据包名称：</label><input type="text" id="packageName"  name="packageName" style="margin-top: 6px;"/>
	<label for="itemCount" style="display: inline;">申请条数：</label><input type="text" id="itemCount"  name="itemCount" style="margin-top: 6px;" />
   <button type="button" id="btnAssignPackage" name="submit">申请数据包</button><span style="margin-left: 10px;"></span><span>条</span>
   </form>
</div>
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
<!-- <th>用户序号</th>
	<th>用户帐号</th> -->
<div id="bottomPager">
	<div class="ypager" id="pager"></div>
</div>
</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript">

function openPackage(id){
	location = '../word/reviseword.php?packageid=' + id;
}
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
//  dateDiff("2012/12/17 13:00:18", (new Date()), 'day')

$(function () {
	//
	$("#btnAssignPackage").click(function(){
		var itemCount = document.getElementById("itemCount").value;
		if(!(/^[\d]+$/.test(itemCount))){
			alert("申请条数必须为数值");
			return;
		}
		var packageName=document.getElementById("packageName").value;
		if(packageName.length <1){
			alert("数据包名称不能为空！");
			return;
		}
		var category = document.getElementById("categories");
		var fields = {"categoryId":category.options[category.selectedIndex].value, 
				"packageName":packageName,
				"itemCount": itemCount}; 
		jQuery.post("assignrevisepackage.php", fields, function (data) {
			//var data = eval("(" + data + ")");
			//alert(data);
			//alert(typeof data);
			//alert(data== true);
			// $.cookie("currentPackageId", ctx.packageId, { expires:30});			
			if(data == 0){
				alert("没有可以分配的词条");
			} else {
				
				if(data > 1){
					location = 'myrevisepackage.php';
				} else {
					alert("您申请的数据包已经达到上限");
				}
			}
		});
	});

	// enumerableData.packageStatus
	function fillWordCategory(categoryId){
		var html = "";
		var rows = enumerableData.wordCategories;
		for (var i = 0; i < rows.length; i++) {
			html += "<option label='"+ rows[i].name +"' value='"+ rows[i].id +"'>" + rows[i].name + "</option>";
		}
		var c = $("#categories");
	    c.html();
	    c.html(html);
	    
	    if(rows.length > 0){
	    	
	    	var packages1 = document.getElementById("categories");
	    	if(!categoryId) categoryId = 0;
	    	var selectedIndex = 0;
	    	for(var i = 0, j = rows.length;i < j; i++){
		    	if(packages1.options[i].value == categoryId) {
		    		selectedIndex = i;
		    		break;
		    	}
	    	}
	    	packages1.options[selectedIndex].selected=true;

	    	jQuery(packages1).live("change", function(){
	    		getWordCount();
		    });
	    }
	}
	fillWordCategory(<?php echo $_SESSION['WordCategory'] ?>);
	jQuery("#itemCount").val(packageCount);
	

	function getWordCount(){
		var countText = $("#btnAssignPackage").next();
		countText.html("可分配词条：正确读取中，请稍等......");
		jQuery.post("../word/_wordcontroller.php?op=unassignedcount", {"WordCategory": $("#categories").val()}, function (data) {
			var count = eval("(" + data + ")");
			countText.html("可分配词条：" + count);
		});
	}
	getWordCount();
	
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"_get_revisepackage_pagination.php",
			"target":".ypager",
			"pageSize":30,
			"postData":{"UserId":<?php echo $_SESSION ['UserId']; ?>, "Edit":0},
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