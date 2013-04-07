<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	// $url_this = "'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>日志管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
select{width:120px;}
.modal{width:60%;left:40%;}
#users{}
#users li{float:left; display:inline;width:19%; margin-bottom:5px; line-height:26px; padding:3px;}
#users li a:hover{background:#ccc;border:1px solid #f29901; }

</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<div class="row">
<div class="span12">
<div id="search">
<span>用户：</span><input type="text" id="txtUser"></input><input type="hidden" id="userId"></input><a href="#myModal" role="button" class="btn" data-toggle="modal">查找用户</a>
<span>日志类型：</span><select id="categories"></select>
<span>所属模块：</span><select id="modules"></select>
<span>操作类型：</span><select id="operations"></select>
<button class="btn" id="btnSearch">查询</button>
</div>
<div>
	<div class="ypager" id="topPager"></div>
</div>
<table id="logRows" class="table table-bordered table-striped table-hover">
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
	<div class="ypager" id="bottomPager"></div>
</div>
</div>

</div>
</div>

<?php include("../../footer.php"); ?>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">请选择一个用户：</h3>
  </div>
  <div class="modal-body">
    <ul id="users"></ul>
    
    <div class="ypager" id="userPager"></div>
  </div>
  <div class="modal-footer">
  <!-- 
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <button class="btn btn-primary">Save changes</button>
     -->
  </div>
</div>

<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">


function deleteLog(id){
	alert(id);
}

$(function () {
	function getOptions(options){
		var html = "<option label='请选择...' value='0'>请选择...</option>";
		if(options && options.length > 0){
			for (var i = 0; i < options.length; i++) {
				html += "<option label='"+ options[i].name +"' value='"+ options[i].id +"'>" + options[i].name + "</option>";
			}
		}
		return html;
	}
	//var user = document.getElementById("users");
	var categories = document.getElementById("categories");
	var modules = document.getElementById("modules");
	var operations = document.getElementById("operations");

	$(categories).html(getOptions(enumerableData.logCategories));
	$(modules).html(getOptions(enumerableData.sysModules));
	categories.selectedIndex = 0;
	modules.selectedIndex = 0;
	$(operations).prop("disabled", true);
	
	$(categories).live("change", function(){
		if(4 == this.options[this.selectedIndex].value){
			$(operations).prop("disabled", false);
		} else {
			$(operations).prop("disabled", true);
		}
    });
	$(modules).live("change", function(){
		var moduleId = this.options[this.selectedIndex].value;
		moduleId = parseInt(moduleId);
		var ops = [];
		for(var i = 0, l = enumerableData.operations.length;i < l ;i++){
			if( enumerableData.operations[i].id > 100 * moduleId && enumerableData.operations[i].id < 100 * (moduleId + 1)){
				ops.push(enumerableData.operations[i]);
			}
		}
		$(operations).html(getOptions(ops));
		operations.selectedIndex = 0;
    });
	//_logcontroller.php
	function loadLog(userId, username, categoryId, moduleId, operationId) {
		var logOpts = {
				"getDataUrl":"_logcontroller.php?op=paged",
				"target":"#topPager",
				"pageSize":15,
				"postData":{ "UserId":userId,"Username":username,"CategoryId":categoryId , "ModuleId":moduleId, "OperationId":operationId }, //, "OrderBy":" CreateDate DESC"
				"onFill":function(rows){
					var html ='';
					for (var i = 0; i < rows.length; i++) {
				        html += "<tr id='row" + rows[i].LogId + "' data-id='" + rows[i].LogId + "'><td style='width:100px;'>" 
				        + rows[i].RealName + "</td> <td style='width:140px;'>" 
				        + rows[i].CreateDate + "</td><td style='width:200px;'>" 
				        + rows[i].OperationName + "</td><td>" 
				        + (rows[i].ContentText?(rows[i].ContentText=="null"? "":rows[i].ContentText):"") + "</td><td style='width:216px;'>" 
				        + rows[i].IPAddress + "</td> <td style='width:40px;'><a href='javascript:void(0);' style='margin-left:3px;' title='删除' onclick=\"deleteLog(" 
				        + rows[i].LogId + ");\" >" + (<?php echo $_SESSION["RoleId"] ?> == 91 ? "删除" : "") + "</a></td></tr>\n";
				    }
					
				    var tableBody = $("#logRows tbody");
				    tableBody.html();
				    tableBody.html(html);
				}
		};
		pagination.paging(logOpts);
	}
	loadLog();

	$('#myModal').on('shown', function () {
		var logOpts = {
				"getDataUrl":"../user/_get_user_list.php",
				"target":"#userPager",
				"pageSize":30,
				"postData":{ }, //, "OrderBy":" CreateDate DESC"
				"onFill":function(rows){
					var html ='';
					for (var i = 0; i < rows.length; i++) {
				        html += "<li data-id='" + rows[i].UserId + "'><a href='#'>" + rows[i].RealName + "</a></li>";
				    }
				    var tableBody = $("#users")
				    tableBody.html();
				    var list = tableBody.html(html).children();
				    list.each(function(i){
				    	jQuery(this).live("click", function(){
				    		$("#txtUser").val(this.firstChild.innerHTML);
				    		$("#userId").val($(this).data("id"));
					    	$('#myModal').modal('hide');
				    	});
					});
				}
		};
		pagination.paging(logOpts);
        
    });
	jQuery("#btnSearch").click(function(){
		loadLog($("#userId").val(), 
				$("#txtUser").val(), 
				$("#categories").val(), 
				$("#modules").val(), 
				$("#operations").val());
	});
});
</script>
</body>
</html>