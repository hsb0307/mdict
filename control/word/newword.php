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
<title>添加新词条</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#nav ul {}
#nav ul li {display:inline; }

#txtMongolian{top:277px;left:913px;position: absolute;width:288px; height: 125px;}
.form-horizontal .control-group{margin-bottom:10px;}
.form-horizontal .control-label{width:120px;}
.form-horizontal .controls{margin-left:140px;}
.span6{width:460px; margin-left:1px;}
.span7{width:620px;}
.span2{width:120px;}
.source{font-family:' \5b8b\4f53';font-size: 14px; margin:4px 0 0 20px; letter-spacing:2px;  word-spacing:4px;}

</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">

<form id="userForm" class="form-horizontal well" >
<fieldset>
	<legend>添加新词条</legend>
	<input type="hidden" id="WordId" value="" />
	<div class="row">
		<div class="span6">
		<div class="control-group">
	<label class="control-label" for="Chinese">中文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Chinese"></div>
</div>
<div class="control-group">
	<label class="control-label" for="Pinyin">拼音:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Pinyin"></div>
</div>
<div class="control-group">
	<label class="control-label" for="English">英文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="English"></div>
</div> 
<div class="control-group">
	<label class="control-label" for="Japanese">日文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Japanese"></div>
</div>
<!-- 
<div class="control-group">
	<label class="control-label" for="MongolianCyrillic">西里尔蒙古文:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianCyrillic"></textarea></div>
</div>
<div class="control-group">
	<label class="control-label" for="MongolianLatin">蒙古文拉丁转写:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianLatin"></textarea></div>
</div>
 -->		
		</div>
		<div class="span7">
 <div class="row">
 <div class="span2" >
 	<div class="mongolian ">
		<textarea cols="3"   class="input-xlarge mongolian" id="Mongolian" style="height: 340px;width:80px;"></textarea>
	</div>
 </div>
 <div class="span6">

<table id="words" class="table table-bordered table-striped table-hover">
<thead>
  <tr>
  	<th>中文</th>
	<th>英文</th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
 
</tbody>
</table>

<div id="existWords" class="hide mongolian" style="margin-left:-10px;" >		
</div>

 </div>
 </div>
 

		</div>
	</div>


<div class="form-actions">
	<button type="button" class="btn btn-primary" id="btnSave">保存</button>
	<input type="reset" value="重置" class="btn">
	<input type="button" value="取消" class="btn" id="btnCancel">
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
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

<script type="text/javascript">
function pickup(elem){
	//alert(elem);
	//if(Sys.ie){
	//	var txtMongolian = document.getElementById('txtMongolian');
	//	txtMongolian.SetUnicodeText($(elem).next().html());
	//}
	document.getElementById('English').value = elem;
}
$(function () {
	var tableBody = $("#words").hide();
	setTimeout(function(){
		var m=$('#Mongolian');//.offset();
		var o = m.offset();
		var txtMongolian = document.getElementById('txtMongolian');//'#txtMongolian'
		if(jQuery.browser.msie){
			//$('#txtMongolian').parent().height(200);
			$(txtMongolian).css("top", m.offset().top).css("left",m.offset().left).css("height", m.height() +10).css("width",m.width() + 14);
			//m.css("display", "none")
			txtMongolian.SetFontSize(30);
			txtMongolian.SetMWFontName("Mongolian Baiti");
		} else {
			//txtMongolian.style.display="none";
		}
	},300);
	$("#Chinese").on("blur", function(){
		var postData = {"SearchText":document.getElementById("Chinese").value};
		jQuery.post("_wordcontroller.php?op=refer" , postData, function (data) {
			var data = eval("(" + data + ")");
			var refer = data.Refer;
			var html ='';
			if(refer && refer.length > 0) {
			for (var i = 0; i < refer.length; i++) {
				html += "<tr><td style='width:75px;'>" + refer[i].Chinese + "</td><td style='width:80px;'>" 
		        + refer[i].English + "</td> <td style='width:50px;'><a href='javascript:void(0);' style='margin-left:3px;' title='选择' onclick=\"pickup('" 
		        + refer[i].English + "');\" >选择</a></td></tr>\n";
		        
			}
			var words = $("#words").show();
			words.html();
			words.html(html);
			}
		    
		    var existed = data.Existed;
		    var html ='';
		    if(existed && existed.length > 0) {
			for (var i = 0; i < existed.length; i++) {
				html += "<div class='well' style='margin:10px;'><div class='word'>" + (existed[i].Mongolian ? ($.trim(existed[i].Mongolian).length == 0 ? latinToMongolian(existed[i].MongolianLatin) : existed[i].Mongolian ) : latinToMongolian(existed[i].MongolianLatin)) + "</div>";
				html += "<div class='source' >" +  enumerableData.getName(existed[i].SourceDictionary?existed[i].SourceDictionary:"", enumerableData.sourceDictionary, true) + "</div></div>"
			} // style='height:460px;'
			var existWords = $("#existWords").show();
			existWords.html();
			existWords.html(html);
		    }
		});
	});
	
	$("#btnSave").click(function(){
		var packageid = getQueryStringByName("packageid");
		var postData = {
				//"ItemId": document.getElementById("ItemId").value,
				//"WordId": document.getElementById("WordId").value,
				"Chinese": document.getElementById("Chinese").value,
				"Pinyin": document.getElementById("Pinyin").value,
				"Mongolian": $.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"MongolianLatin": "", //document.getElementById("MongolianLatin").value,
				"MongolianCyrillic": "",//document.getElementById("MongolianCyrillic").value,
				"English": document.getElementById("English").value,
				"Japanese": document.getElementById("Japanese").value,
				"SourceDictionary":getQueryStringByName("category"), // 为添加词条服务的数据
				"LastModifiedBy":<?php echo $_SESSION["UserId"] ?>,
			    "Status": 9, // 9表示新增词条
			    "PackageId":packageid
			};
		if(!postData.Chinese){
			alert("必须录入中文词条名！");
			return false;
		}
		if(!postData.Mongolian){
			alert("必须录入传统蒙古文！");
			return false;
		}
		jQuery.post("_wordcontroller.php?op=create", postData, function (data) {
			var data = eval("(" + data + ")");
			if(data.success){
				if(packageid){
					location = 'reviseword.php?packageid=' + packageid + '&page=' + getQueryStringByName("page");
				} else {
					location = 'reviseword.php?page=' + getQueryStringByName("page");
				}
			} else {
				alert("添加词条失败");

			}
		});
	});
	$("#btnCancel").click(function(){
		var packageid = getQueryStringByName("packageid");
		if(packageid){
			location = 'reviseword.php?packageid=' + packageid + '&page=' + getQueryStringByName("page");
		} else {
			location = 'reviseword.php?page=' + getQueryStringByName("page");
		}
	});
	
});			
</script>
</body>
</html>