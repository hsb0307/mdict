<?php
session_start();
if(!isset($_SESSION["UserId"]) || $_SESSION["RoleId"] != 91)
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
<title>控制面板</title>
<?php include("../../css.php"); ?>

<style type="text/css">
.span3{width:220px;}
.span9{width:840px;}
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
.table{margin-bottom:10px;}
h4{margin-top:30px;}
</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container" style="margin-top: 10px;">
	<div class="row">
	<div class="well sidebar-nav span3">
	<h2>管理员控制台</h2>
		<ul id="words" class="nav nav-list">
			<li><a href='../user/index.php' target="_blank" >用户管理</a> </li>
			<li><a href='../task/editpackage.php' target="_blank" >录入数据包管理</a> </li>
			<li><a href='../task/revisepackage.php' target="_blank" >编辑数据包管理</a> </li>
			<li><a href='../task/approvepackage.php' target="_blank" >审定数据包管理</a> </li>
			<li><a href='../log/log.php' target="_blank" >日志管理</a> </li>
			<li><a href='../guestbook/index.php' target="_blank" >用户留言</a> </li>
		</ul>
	</div>
	<div class="span9">
	
<h4>用户留言</h4>
<table id="guestbook" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>姓名</th>
	<th>日期</th>
	<th>标题</th>
	<th>内容</th>
	<th>最新回复</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="guestbookPager"></div>
</div>

<h4>操作日志</h4>
<table id="userLog" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>姓名</th>
	<th>日期</th>
	<th>日志类型</th>
	<th>内容</th>
	<th>IP</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="logPager"></div>
</div>

<h4>用户列表</h4>
		
<table id="users" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>用户帐号</th>
	<th>姓名</th>
	<th>手机</th>
	<th>单位</th>
	<th>是否验证</th>
	<th>角色</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="userPager"></div>
</div>
		
<h4>录入数据包</h4>
<table id="editPackages" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>用户名</th>
	<th>数据包名称</th>
	<th>建立日期</th>
	<th>状态</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="editPackagePager"></div>
</div>

<h4>编辑数据包</h4>	
<table id="revisePackages" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>用户名</th>
	<th>数据包名称</th>
	<th>建立日期</th>
	<th>状态</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="revisePackagePager"></div>
</div>

<h4>审定数据包</h4>	
<table id="approvePackages" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>用户名</th>
	<th>数据包名称</th>
	<th>建立日期</th>
	<th>状态</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="approvePackagePager"></div>
</div>
	</div>
	</div>
</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">
function rejectEditPackage(id){
	alert('待开发' + id);
}
function deleteEditPackage(id){
	alert('待开发' + id);
}

function openPackage(id){
	location = '../user/edituser.php?op=edit&id=' + id;
}
function openEditPackage(id){
	location = '../word/editword.php?op=edit&id=' + id;
}
function openRevisePackage(id){
	location = '../word/reviseword.php?op=edit&id=' + id;
}

function submitEditPackage(id){
	alert('待开发' + id);
}
function rejectEditPackage(id){
	alert('待开发' + id);
}

function submitPackage(id){
	alert('待开发' + id);
}
function approvalUser(id, isApproved){
	if(isApproved == "1"){
		alert("此用户已经通过验证，无需再次验证");
		return false;
	}
	var postData = {"UserId":id};
	jQuery.post("../user/_usercontroller.php?op=approval", postData, function (data) {
		var msg = eval("(" + data + ")");
		//dataBind(row.Row);
		if(msg == 1){
			//$("#row"+id).remove();
			$("#row"+id).children('td').eq(4).html("已验证");
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
	var r = confirm("您确实要删除此用户吗？" );
	if(!r) return false;
	
	var postData = {"UserId":id, "RoleId":roleId};
	jQuery.post("../user/_usercontroller.php?op=delete", postData, function (data) {
		var msg = eval("(" + data + ")");
		//dataBind(row.Row);
		if(msg == 1){
			$("#row"+id).remove();
			alert("删除成功！")
		} else if(msg == 0) {
			alert("此用户已经分配数据包，无法删除。");
		} else {
			alert("删除失败：" + msg);
		}
	});
};

$(function () {
	// == 用户留言开始
	var guestbookOpts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../guestbook/_gbcontroller.php?op=paged",
			"target":"#guestbookPager",
			"pageSize":5,
			"postData":{"PageSize" : 30}, //, "OrderBy":" CreateDate DESC"
			"onFill":function(rows){
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].TopicId + "' data-id='" + rows[i].TopicId + "'><td style='width:100px;'>" 
			        + rows[i].UserName + "</td> <td style='width:140px;'>" 
			        + rows[i].DateCreated + "</td><td style='width:200px;'>" 
			        + rows[i].Title + "</td><td>" 
			        + (rows[i].FullText?(rows[i].FullText=="null"? "":rows[i].FullText):"") + "</td><td>" 
			        + (rows[i].LastPostText?(rows[i].LastPostText=="null"? "无回复":rows[i].LastPostText):"无回复") + "</td> <td style='width:76px;'><a href='javascript:void(0);' style='margin-left:3px;' title='删除' onclick=\"deleteLog(" 
			        + rows[i].TopicId + ");\" >" + (<?php echo $_SESSION["RoleId"] ?> == 91 ? "删除" : "") + "</a><a href='../guestbook/topic.php?id="+ rows[i].TopicId +"' style='margin-left:10px;' title='回复'>回复</a></td></tr>\n";
			    }
				
			    var tableBody = $("#guestbook tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(guestbookOpts);
	// == 用户留言结束
	// == 操作日志开始
	var logOpts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../log/_logcontroller.php?op=paged",
			"target":"#logPager",
			"pageSize":5,
			"postData":{"PageSize" : 30}, //, "OrderBy":" CreateDate DESC"
			"onFill":function(rows){
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].LogId + "' data-id='" + rows[i].LogId + "'><td style='width:100px;'>" 
			        + rows[i].RealName + "</td> <td style='width:140px;'>" 
			        + rows[i].CreateDate + "</td><td style='width:180px;'>" 
			        + rows[i].OperationName + "</td><td>" 
			        + (rows[i].ContentText?(rows[i].ContentText=="null"? "":rows[i].ContentText):"") + "</td><td style='width:110px;'>" 
			        + rows[i].IPAddress + "</td> <td style='width:40px;'><a href='javascript:void(0);' style='margin-left:3px;' title='删除' onclick=\"deleteLog(" 
			        + rows[i].LogId + ");\" >" + (<?php echo $_SESSION["RoleId"] ?> == 91 ? "删除" : "") + "</a></td></tr>\n";
			    }
				
			    var tableBody = $("#userLog tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(logOpts);
	// == 操作日志结束
	
	var opts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../user/_get_user_list.php",
			"target":"#userPager",
			"pageSize":5,
			"postData":{"OrderBy":"IsApproved, CreateDate DESC"},
			"onFill":function(rows){
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].UserId + "' data-id='" + rows[i].UserId + "'><td style='width:70px;'>" 
			        + rows[i].UserName + "</td> <td style='width:100px;'>" 
			        + rows[i].RealName + "</td><td style='width:60px;'>" 
			        + rows[i].Mobile + "</td><td>" 
			        + rows[i].Company + "</td><td>" 
			        + (rows[i].IsApproved == "1"  ? "已验证" : "<span class='text-error'>未验证</span>") + "</td><td>" 
			        + enumerableData.getName(rows[i].RoleId, enumerableData.userRole) + "</td> <td style='width:120px;'><a href='javascript:void(0);' style='margin-left:10px;' title='详情' onclick=\"openPackage('" 
			        + rows[i].UserId + "');\" >编辑</a><a href='javascript:void(0);' style='margin-left:10px;' title='批准' onclick=\"approvalUser('" 
			        + rows[i].UserId + "', '" + rows[i].IsApproved + "');\" >"+ (rows[i].IsApproved == 1?"":"批准") +"</a><a href='javascript:void(0);' style='margin-left:10px;' title='删除用户' onclick=\"deleteUser('" 
			        + rows[i].UserId + "', "+ rows[i].RoleId +");\" >删除</a></td></tr>\n";
			    }
				
			    var tableBody = $("#users tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(opts);

	var editPackageOpts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../task/_get_editpackage_pagination.php",
			"target":"#editPackagePager",
			"pageSize":5,
			"postData":{"orderBy":"e.CreateDate","isAsc":"false"},
			"onFill":function(rows){
				if(!rows) return false;
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].PackageId + "' data-id='" + rows[i].PackageId + "'><td>" 
			        + rows[i].RealName + "</td><td>" 
			        + rows[i].PackageName + "</td><td style='width:140px;'>" 
			        + rows[i].CreateDate + "</td><td style='width:60px;'>" 
			        + enumerableData.getName(rows[i].Status, enumerableData.packageStatus) + "</td> <td style='width:60px;'><a href='../task/editpackage.php?packageid=" 
			        + rows[i].PackageId + "' style='margin-left:10px;' title='详情' target='_blank'>查看</a></td></tr>\n";
			    }
				
			    var tableBody = $("#editPackages tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(editPackageOpts);

	var revisePackageOpts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../task/_get_revisepackage_pagination.php",
			"target":"#revisePackagePager",
			"pageSize":5,
			"postData":{ "OrderBy":" CreateDate DESC"},
			"onFill":function(rows){
				if(!rows) return false;
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].PackageId + "' data-id='" + rows[i].PackageId + "'><td>" 
			        + rows[i].RealName + "</td> <td>" 
			        + rows[i].PackageName + "</td><td style='width:140px;'>" 
			        + rows[i].CreateDate + "</td><td style='width:60px;'>" 
			        + enumerableData.getName(rows[i].Status, enumerableData.packageStatus) + "</td> <td style='width:60px;'><a href='../task/revisepackage.php?packageid=" 
			        + rows[i].PackageId + "' style='margin-left:10px;' title='详情' target='_blank'>查看</a></td></tr>\n";
			    }
				
			    var tableBody = $("#revisePackages tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(revisePackageOpts);

	var approvePackageOpts = {//"getCountUrl":"_get_editpackage_count.php",
			"getDataUrl":"../task/_get_approvepackage_pagination.php",
			"target":"#approvePackagePager",
			"pageSize":5,
			"postData":{ "OrderBy":" CreateDate DESC"},
			"onFill":function(rows){
				//if(!rows) return false;
				if(!rows) rows = [];
				var html ='';
				for (var i = 0; i < rows.length; i++) {
			        html += "<tr id='row" + rows[i].PackageId + "' data-id='" + rows[i].PackageId + "'><td style='width:100px;'>" 
			        + rows[i].RealName + "</td> <td style='width:200px;'>" 
			        + rows[i].PackageName + "</td><td style='width:140px;'>" 
			        + rows[i].CreateDate + "</td><td>" 
			        + enumerableData.getName(rows[i].Status, enumerableData.packageStatus) + "</td> <td style='width:120px;'><a href='javascript:void(0);' style='margin-left:10px;' title='详情' onclick=\"openPackage('" 
			        + rows[i].PackageId + "');\" >编辑</a><a href='javascript:void(0);' style='margin-left:10px;' title='批准' onclick=\"approvalUser('" 
			        + rows[i].PackageId + "');\" >批准</a><a href='javascript:void(0);' style='margin-left:10px;' title='删除用户' onclick=\"deleteUser('" 
			        + rows[i].PackageId + "', '"+ rows[i].PackageId +"');\" >删除</a></td></tr>\n";
			    }
				
			    var tableBody = $("#approvePackages tbody");
			    tableBody.html();
			    tableBody.html(html);
			}
	};
	pagination.paging(approvePackageOpts);
	
});
		
</script>
</body>
</html>