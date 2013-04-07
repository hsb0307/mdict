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
<title>用户管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
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
<table id="users" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>用户帐号</th>
	<th>姓名</th>
	<th>手机</th>
	<th>单位</th>
	<th>性别</th>
	<th>出生日期</th>
	<th>是否验证</th>
	<th>角色</th>
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

function openPackage(id){
	location = '../word/reviseword.php?packageid=' + id;
}
function editUser(id){
	location = 'edituser.php?op=edit&id=' + id;
}
function approvalUser(id, isApproved){
	//alert('待开发' + id);
	//approval
	//alert(isApproved);
	if(isApproved == "1"){
		alert("此用户已经通过验证，无需再次验证");
		return false;
	}
	
	var postData = {"UserId":id};
	jQuery.post("_usercontroller.php?op=approval", postData, function (data) {
		var msg = eval("(" + data + ")");
		//dataBind(row.Row);
		if(msg == 1){
			//$("#row"+id).remove();
			$("#row"+id).children('td').eq(6).html("已验证");
			alert("批准成功！")
		} else {
			alert("批准失败：" + msg);
		}
	});
};
function deleteUser(id,roleId){
	if(roleId == 91){
		alert("此用户不允许删除！")
		return;
	}
	var postData = {"UserId":id, "RoleId":roleId};
	jQuery.post("_usercontroller.php?op=delete", postData, function (data) {
		var msg = eval("(" + data + ")");
		//dataBind(row.Row);
		if(msg == 1){
			$("#row"+id).remove();
			alert("删除成功！")
		} else if(msg == 0) {
			alert("此用户已经分配数据包，无法删除。");
		} else {
			alert("删除失败：" + msg.msg);
		}
	});
}

$(function () {
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"_get_user_list.php",
			"target":".ypager",
			"pageSize":30,
			"postData":{"PageSize":20},
			"onFill":function(rows){
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].UserId + "' data-id='" + rows[i].UserId + "'><td style='width:70px;'>" 
			        + rows[i].UserName + "</td> <td style='width:100px;'>" 
			        + rows[i].RealName + "</td><td style='width:60px;'>" 
			        + rows[i].Mobile + "</td><td>" 
			        + rows[i].Company + "</td><td>" 
			        + rows[i].Gender + "</td><td>" 
			        + rows[i].Birthday + "</td><td>" 
			        + (rows[i].IsApproved == "1"  ? "已验证" : "<span class='text-error'>未验证</span>") + "</td><td>" 
			        + enumerableData.getName(rows[i].RoleId, enumerableData.userRole) + "</td> <td style='width:200px;'><a href='javascript:void(0);' style='margin-left:10px;' title='详情' onclick=\"editUser('" 
			        + rows[i].UserId + "');\" >编辑</a><a href='javascript:void(0);' style='margin-left:10px;' title='提交' onclick=\"approvalUser('" 
			        + rows[i].UserId + "', '" + rows[i].IsApproved + "');\" >批准</a><a href='javascript:void(0);' style='margin-left:10px;' title='删除用户' onclick=\"deleteUser('" 
			        + rows[i].UserId + "', '" + rows[i].RoleId + "');\" >删除</a></td></tr>\n";
			    }
				
			    var tableBody = $("#users tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(opts);
});
		
</script>
</body>
</html>