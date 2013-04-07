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
<title>留言管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
.form-horizontal .control-label {width: 100px;}
.form-horizontal .controls {margin-left: 120px;}
.input-xlarge{width:300px}
</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<div class="row">
<div class="span12">

<a href="#myModal" role="button" class="btn" data-toggle="modal">我要留言</a>

<div>
	<div class="ypager" id="topPager"></div>
</div>
<table id="logRows" class="table table-bordered table-striped table-hover">
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
	<div class="ypager" id="bottomPager"></div>
</div>
</div>

</div>
</div>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">我要留言</h3>
  </div>
  <div class="modal-body">
   <form class="form-horizontal">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="Title">标题</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Title">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="FullText">留言内容</label>
            <div class="controls">
              <textarea id="FullText" class="input-xlarge" rows="4">如题</textarea>
            </div>
          </div>
          
        </fieldset>
      </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <button id="btnSave" class="btn btn-primary">保存</button>
  </div>
</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">

//var userId = <?php echo $_SESSION["RoleId"] ?>;

function deleteLog(id){
	alert(id);
}
function postTopic(id){
	alert(id);
}

$(function () {
	//_logcontroller.php
	function loadTopic(){
		var logOpts = {
				"getDataUrl":"_gbcontroller.php?op=paged",
				"target":".ypager",
				"pageSize":15,
				"postData":{"PageSize" : 30}, //, "OrderBy":" CreateDate DESC"
				"onFill":function(rows){
					var html ='';
					for (var i = 0; i < rows.length; i++) {
				        html += "<tr id='row" + rows[i].TopicId + "' data-id='" + rows[i].TopicId + "'><td style='width:100px;'>" 
				        + rows[i].UserName + "</td> <td style='width:140px;'>" 
				        + rows[i].DateCreated + "</td><td style='width:200px;'>" 
				        + rows[i].Title + "</td><td>" 
				        + (rows[i].FullText?(rows[i].FullText=="null"? "":rows[i].FullText):"") + "</td><td style='width:216px;'>" 
				        + (rows[i].LastPostText?(rows[i].LastPostText=="null"? "无回复":rows[i].LastPostText):"无回复") + "</td> <td style='width:90px;'><a href='javascript:void(0);' style='margin-left:3px;' title='删除' onclick=\"deleteLog(" 
				        + rows[i].TopicId + ");\" >" + (<?php echo $_SESSION["RoleId"] ?> == 91 ? "删除" : "") + "</a><a href='topic.php?id="+ rows[i].TopicId +"' style='margin-left:10px;' title='回复'>回复</a></td></tr>\n";
				    }
					
				    var tableBody = $("#logRows tbody");
				    tableBody.html();
				    tableBody.html(html);
				}
		};
		pagination.paging(logOpts);
	}
	$("#btnSave").click(function(){
		var topicText = document.getElementById('Title').value;
		if(topicText.length == 0) {
			alert("留言标题不能为空！"); 
			return false;
		}
		var postData = {"Title": topicText, "FullText":$("#FullText").val() };
		jQuery.post("_gbcontroller.php?op=createtopic", postData, function (data) {
			var msg = eval("(" + data + ")");
			if(msg){
				$('#myModal').modal('hide');
				loadTopic();
			} else {
				alert("留言失败：" + msg);
			}
		});
	});
	loadTopic();
});
</script>
</body>
</html>