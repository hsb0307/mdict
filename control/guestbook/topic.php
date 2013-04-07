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
<title>留言</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
.form-horizontal .control-label {width: 100px;}
.form-horizontal .controls {margin-left: 120px;}
.input-xlarge{width:300px}
#threadTitle{margin:10px 0 10px 0; font-weight:bold;}
</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<div class="row">
<div class="span12">
<div id="threadTitle">
<span>标题:</span><span id="topicTitle" class="text-error"></span> <span style="float: right;margin-right:30px;"><a href="index.php">返回留言列表</a></span>
</div>
<div>
	<div class="ypager" id="topPager"></div>
</div>
<table id="logRows" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
	<th>姓名</th>
	<th>日期</th>
	<th>回复内容</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>
<div>
	<div class="ypager" id="bottomPager"></div>
</div>

<form class="well">
        <fieldset>
        <label for="FullText">回复</label>
  		<textarea id="FullText" class="input-xlarge" rows="4"></textarea>
		<div class="form-actions" id="formActions">
            <button type="button" class="btn btn-primary" id="btnSave">保存</button>
            <button type="reset" class="btn" id="cancel">取消</button>
          </div>          
        </fieldset>
      </form>
</div>

</div>
</div>


<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">
function deleteLog(id){
	alert(id);
}
function postTopic(id){
	alert(id);
}

$(function () {
	var topicId = getQueryStringByName("id");
	//_logcontroller.php
	function loadPost(){
		var logOpts = {
				"getDataUrl":"_gbcontroller.php?op=posts&id="+ topicId,
				"target":".ypager",
				"pageSize":15,
				"postData":{"TopicId" : topicId}, //, "OrderBy":" CreateDate DESC"
				"onFill":function(rows, o){

					document.title = o.otherData.Title;
					//console.log(o.otherData.Title);
					//console.log(rows.length);
					$("#topicTitle").html(o.otherData.Title);

					var html ='';
					for (var i = 0; i < rows.length; i++) {
				        html += "<tr id='row" + rows[i].TopicId + "' data-id='" + rows[i].TopicId + "'><td style='width:100px;'>" 
				        + rows[i].UserName + "</td> <td style='width:130px;'>" 
				        + rows[i].DateCreated + "</td><td>" 
				        + (rows[i].FullText?(rows[i].FullText=="null"? "":rows[i].FullText):"") + "</td><td></tr>\n";
				    }
					
				    var tableBody = $("#logRows tbody");
				    tableBody.html();
				    tableBody.html(html);
				}
		};
		pagination.paging(logOpts);
	}
	$("#btnSave").click(function(){
		var fullText = $("#FullText").val();
		if(fullText.length == 0) {
			alert("回复内容不能为空！"); 
			return false;
		}
		var postData = {"TopicId":topicId, "Title": "", "FullText":$("#FullText").val() };
		jQuery.post("_gbcontroller.php?op=createpost", postData, function (data) {
			var msg = eval("(" + data + ")");
			if(msg){
				$('#FullText').val('');
				loadPost();
			} else {
				alert("留言失败：" + msg);
			}
		});
	});
	loadPost();
});
</script>
</body>
</html>