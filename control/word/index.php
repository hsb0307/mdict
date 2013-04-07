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
<title>我处理的词条</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#pageTitle {position: relative; }
#pageTitle div {position: absolute; top:30px; padding-left:460px; font-size:16pt; }
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }

</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<div id="topPager">
	<div class="ypager"></div>
</div>
<table id="mywords" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
	<th>中文</th>
	<th>内大拉丁</th>
	<th>西里尔蒙文</th>
	<th>传统蒙文</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div id="bottomPager">
	<div class="ypager" id="pager"></div>
</div>
</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">

function openWord(id,page){
	location = '../word/updateword.php?id=' + id + "&page="+page;
}
function rejectWord(id){
	alert('待开发' + id);
}
function rejectWord(id){
	if(!window.confirm("是否删除?")) return false;
	var postData = {"WordId":id};
	jQuery.post("_wordcontroller.php?op=mydelete", postData, function (data) {
		var msg = eval("(" + data + ")");
		//dataBind(row.Row);
		if(msg.success){
			$("#row"+id).remove();
			alert(msg.msg)
		} else {
			alert("删除失败：" + msg);
		}
	});
};
$(function () {
	$("#pageHint").html("我处理的词条");
	var ctx = {};
	ctx.page = getQueryStringByName("page");
	if(!ctx.page){
		ctx.page = 1;
	}
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"_wordcontroller.php?op=paged",
			"target":"#pager",
			"page":ctx.page,
			"pageSize":20,
			"postData":{ "UserId":<?php echo $_SESSION["UserId"] ?>, "OrderBy":" CreateDate DESC"},
			"onFill":function(rows, o){
				if(!rows) return false;
				if(o.count == 0){
					alert("没有发现数据！");
					return;
				}
				ctx.page = o.page;
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].WordId + "' data-id='" + rows[i].WordId + "'><td style='width:160px;'>" 
			        + rows[i].Chinese + "</td> <td style='width:350px;'>" 
			        + rows[i].MongolianLatin + "</td><td style='width:200px;'>" 
			        + rows[i].MongolianCyrillic + "</td><td style='font-size:18pt;' class='mongol'>" 
			        + rows[i].Mongolian + "</td> <td style='width:80px;'><a href='javascript:void(0);' style='margin-left:8px;' title='详情' onclick=\"openWord('" 
			        + rows[i].WordId + "', " + ctx.page + ");\" >查看</a><a href='javascript:void(0);' style='margin-left:8px;' title='批准' onclick=\"rejectWord('" 
			        + rows[i].WordId + "');\" >删除</a></td></tr>\n";
			    }
				
			    var tableBody = $("#mywords tbody");
			    tableBody.html();
			    tableBody.html(html);
			    
			}
	};
	pagination.paging(opts);
});
		
</script>
</body>
</html>