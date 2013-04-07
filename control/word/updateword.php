<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}

require_once '../../lib/dao/WordDao.php';
require_once '../../lib/common/_common.php';
$dao = new WordDao();
$userid = 1;
if(isset( $_SESSION ['UserId']))
{
	$userid = $_SESSION ['UserId'];
}
$wordId = 0;
if(isset( $_GET ['id']))
{
	$wordId =$_GET ['id'];
}
$word = $dao->GetById( $wordId);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>更新词条</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#pageTitle {position: relative; }
#pageTitle div {position: absolute; top:30px; padding-left:460px; font-size:16pt; }
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#nav ul {}
#nav ul li {display:inline; }

#txtMongolian{top:277px;left:913px;position: absolute;width:288px; height: 125px;}
.form-horizontal .control-group{margin-bottom:10px;}
.form-horizontal .control-label{width:120px;}

</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<form id="userForm" class="form-horizontal well" >
<fieldset>
	<legend>修改词条</legend>
	<input type="hidden" id="WordId" value="<?php echo $word->WordId ?>" />
	<div class="row">
		 <div class="span6">
<div class="control-group">
	<label class="control-label" for="Chinese">中文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Chinese" value="<?php echo $word->Chinese ?>"><span class="help-inline">*</span></div>
</div>
<div class="control-group">
    <label class="control-label" for="RoleId">词条类别</label>
    <div class="controls">
        <select id="categories"></select>
    </div>
</div>

<div class="control-group">
	<label class="control-label" for="MongolianCyrillic">西里尔蒙文:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianCyrillic"><?php echo $word->MongolianCyrillic ?></textarea></div>
</div>
<div class="control-group">
	<label class="control-label" for="MongolianLatin">蒙文拉丁转写:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianLatin"><?php echo $word->MongolianLatin ?></textarea></div>
</div>

<div class="control-group">
	<label class="control-label" for="Description">备注:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="Description"><?php echo $word->Description ?></textarea></div>
</div>
		 </div>
		 <div class="span3">
		 <!-- 
<div><label class="control-label" for="Chinese">传统蒙文:</label></div><br />
<div><textarea  class=" mongolian" id="Mongolian" style="width:100px; height: 360px;"></textarea></div>
 -->
 <textarea  class=" mongolian" id="Mongolian" style="width:100px; height: 360px;"><?php echo $word->Mongolian ?></textarea>
 
		 </div>
	 </div>
	


<div class="form-actions">
	<button type="button" class="btn btn-primary" id="btnSave">保存</button>
	<input type="reset" value="重置" class="btn">
	<input type="button" value="取消" class="btn" id="btnCancel">
	<span id="saveInfo" class="text-success"></span>
</div>
</fieldset>
</form>
<OBJECT classid="clsid:537BF42E-B49F-444F-B8AD-F6A862504B32"
	codebase="HgUnicodeSgOcx.cab#version=1,0,0,1" id='txtMongolian'
	style="font-family: Mongolian Baiti;"
	align="center" hspace=0 vspace=0>
</OBJECT>
</div>
<?php include("../../footer.php"); ?>
<!-- 
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
 -->
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/ChinesePY.js"></script>

<script type="text/javascript">
$(function () {
	$("#pageHint").html("词条征集：词条修改");
	$("#categories").width($("#Chinese").width());
	setTimeout(function(){
		var m=$('#Mongolian');//.offset();
		var o = m.offset();
		var txtMongolian = document.getElementById('txtMongolian');//'#txtMongolian'
		if(jQuery.browser.msie){
			
			//$('#txtMongolian').parent().height(200);
			$(txtMongolian).css("top", m.offset().top).css("left",m.offset().left).css("height", m.height() +10).css("width",m.width() + 14);
			//m.css("display", "none")
			txtMongolian.SetFontSize(24);
			txtMongolian.SetMWFontName("Mongolian Baiti");
			txtMongolian.SetUnicodeText(m.val());
		} else {
			txtMongolian.style.display="none";
		}
	},300);
	// enumerableData.wordCategories
	var html = "";
	for (var i = 0, length = enumerableData.wordCategories.length; i < length; i++) {
		html += "<option label='"+ enumerableData.wordCategories[i].name +"' value='"+ enumerableData.wordCategories[i].id +"'>" + enumerableData.wordCategories[i].name + "</option>";
	}
	var c = $("#categories");
    c.html();
    c.html(html);
    if(enumerableData.wordCategories.length > 0){
    	var category = document.getElementById("categories");
    	//category.options[enumerableData.wordCategories.length - 1].selected=true;
    	for(var i = 0, len = category.options.length; i < len; i++ ) {
    		if(category.options[i].value == '<?php echo $word->WordCategory ?>'){
    			category.options[i].selected=true;
    			break;
    		}
    	}
    }
    $("#Chinese").focus();
    $("#MongolianLatin").keyup(function(event){
    	if(jQuery.browser.msie){
    		document.getElementById("txtMongolian").SetUnicodeText(latinToMongolian($(this).val()));
		} else {
			$( "#Mongolian" ).val(latinToMongolian($(this).val()));
		}
    });
	$("#btnSave").click(function(){
		//var packageid = getQueryStringByName("packageid");
		if (showTip.nodes.length) {
	        alert("数据验证没有通过，请检查,中文词条必须填写!");
	        return;
	    }
 
		var queryCode = makePy(document.getElementById("Chinese").value);
		if(queryCode.length){
			queryCode = queryCode[0];
		} else {
			queryCode = "";
		}
		var postData = {
				//"ItemId": document.getElementById("ItemId").value,
				"WordId": document.getElementById("WordId").value,
				"Chinese": document.getElementById("Chinese").value,
				"QueryCode": queryCode,//makePy(document.getElementById("Chinese").value),
				"Pinyin": jQuery.trim( Pinyin.GetQP(document.getElementById("Chinese").value)),//document.getElementById("Pinyin").value,
				"Mongolian": $.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"MongolianLatin": document.getElementById("MongolianLatin").value,
				"MongolianCyrillic": document.getElementById("MongolianCyrillic").value,
				"English": "",//document.getElementById("English").value,
				"Japanese": "",//document.getElementById("Japanese").value,
				"WordCategory":$("#categories").val(),
				"Description":$("#Description").val(),
				"SourceDictionary": <?php echo $_SESSION["UserId"] ?>,//28,//getQueryStringByName("category"), // 为添加词条服务的数据
			    //"Status": 0, // 8表示 编辑完成
			    "UserId":<?php echo $_SESSION["UserId"] ?>
			};
		jQuery.post("_wordcontroller.php?op=update", postData, function (data) {
			var data = eval("(" + data + ")");
			//alert(data.msg);
			if(data.success){
				$("#txtMongolian").hide();
				location = '../word/myword.php?page='+getQueryStringByName("page");
			} else {
				alert(data.msg);

			}
		});
	});
	$("#btnCancel").click(function(){
		
	});
	
});			
</script>
</body>
</html>