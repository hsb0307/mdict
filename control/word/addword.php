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
<title>添加词条</title>
<?php include("../../css.php"); ?>
<link href="../../css/jquery/theme/ui-lightness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
<style type="text/css">
#pageTitle {position: relative; }
#pageTitle div {position: absolute; top:30px; padding-left:460px; font-size:16pt; }
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#nav ul {}
#nav ul li {display:inline; }

#txtMongolian{top:277px;left:913px;position: absolute;width:288px; height: 125px;}
.ui-widget-content {background-color:#fff;}

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
<form id="wordForm" class="form-horizontal well" >
<fieldset>
	<legend>添加新词条</legend>
	<input type="hidden" id="WordId" value="" />
	<div class="row">
		 <div class="span6">
<div class="control-group">
	<label class="control-label" for="Chinese">中文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Chinese"><span class="help-inline">*</span></div>
</div>
<!-- 
<div class="control-group">
    <label class="control-label" for="RoleId">词条类别</label>
    <div class="controls">
        <select id="categories"></select>
    </div>
</div>

<div class="control-group">
	<label class="control-label" for="MongolianCyrillic">西里尔蒙古文:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianCyrillic" style="font-family:sylfaen;font-size:14pt;"></textarea></div>
</div>
<div class="control-group">
	<label class="control-label" for="MongolianLatin">蒙古文拉丁转写:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianLatin"></textarea></div>
</div>
 -->
 <div class="control-group">
            <label class="control-label" for="WordCategory">词条类别:</label>
            <div class="controls"><select id="WordCategory" name="WordCategory"></select> </div>
</div>
<div class="control-group">
	<label class="control-label" for="English">英文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="English"></div>
</div> 
<div class="control-group">
	<label class="control-label" for="Japanese">日文:</label>
	<div class="controls"><input type="text" class="input-xlarge" id="Japanese"></div>
</div>
<div class="control-group">
	<label class="control-label" for="Description">备注:</label>
	<div class="controls"><textarea rows="3" class="input-xlarge" id="Description"></textarea></div>
</div>
		 </div>
		 <div class="span7" >
 <div class="row">
 <div class="span2" >
 	<div class="mongolian ">
		<textarea cols="3"   class="input-xlarge mongolian" id="Mongolian" style="height: 340px;width:80px;"></textarea>
	</div>
 </div>
 <div class="span6">

<table id="words" class="table table-bordered table-striped table-hover hide">
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
	<span id="saveInfo" class="text-success"></span>
</div>
</fieldset>
</form>
<OBJECT classid="clsid:537BF42E-B49F-444F-B8AD-F6A862504B32" codebase="HgUnicodeSgOcx.cab#version=1,0,0,1" 
id="txtMongolian" style="font-family: Mongolian Baiti;" align="center" hspace=0 vspace=0>
</OBJECT>
</div>
<?php include("../../footer.php"); ?>
<!-- 
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
 -->
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="../../js/ChinesePY.js"></script>

<script type="text/javascript">
function pickup(elem){
	document.getElementById('English').value = elem;
}

$(function () {
	$("#pageHint").html("词条征集：词条录入");
	
	//$("#categories").width($("#Chinese").width());
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
			txtMongolian.style.display="none";
		}
	},300);
	
    $("#Chinese").focus();
    

    function fillWordCategory(){
		var html = "";
		var rows = enumerableData.wordCategories;
		for (var i = 0; i < rows.length; i++) {
			html += "<option label='"+ rows[i].name +"' value='"+ rows[i].id +"'>" + rows[i].name + "</option>";
		}
		var wordCategory = document.getElementById("WordCategory");
		var c = $(wordCategory);
	    c.html();
	    c.html(html);
	}
	function setWordCategory(categoryId){
		var rows = enumerableData.wordCategories;
		var wordCategory = document.getElementById("WordCategory");
		if(rows.length > 0){
	    	if(!categoryId) categoryId = 0;
	    	var selectedIndex = 0;
	    	for(var i = 0, j = rows.length;i < j; i++){
		    	if(wordCategory.options[i].value == categoryId) {
		    		selectedIndex = i;
		    		break;
		    	}
	    	}
	    	wordCategory.options[selectedIndex].selected=true;
	    }
	}
	fillWordCategory();
	setWordCategory(<?php echo $_SESSION['WordCategory'] ?>);
    /*
    $("#MongolianLatin").keyup(function(event){
    	if(jQuery.browser.msie){
    		document.getElementById("txtMongolian").SetUnicodeText(latinToMongolian($(this).val()));
		} else {
			$( "#Mongolian" ).val(latinToMongolian($(this).val()));
		}
    	
    });
    */

	$("#Chinese").on("blur", function(){
		var searchText = document.getElementById("Chinese").value;
		var words = $("#words");
		words.html("");
		words.hide();
		var existWords = $("#existWords");
		existWords.html("");
		existWords.hide();
		if(searchText.length == 0 || searchText == " "){
			 return false;
			 //
		}
		var postData = {"SearchText":searchText};
		jQuery.post("_wordcontroller.php?op=refer" , postData, function (data) {
			var data = eval("(" + data + ")");
			var refer = data.Refer;
			var html ='';
			if(refer && refer.length > 0) {
			for (var i = 0; i < refer.length; i++) {
				html += "<tr><td style='width:75px;'>" + refer[i].Chinese + "</td><td style='width:80px;'>" 
		        + refer[i].English + "</td> <td style='width:50px;'><a href='javascript:void(0);' style='margin-left:3px;width:60px;' title='选择' onclick=\"pickup('" 
		        + refer[i].English + "');\" >选择</a></td></tr>\n";
		        
			}
			
			words.show();
			words.html(html);
			}
		    
		    var existed = data.Existed;
		    var html ='';
		    if(existed && existed.length > 0) {
			for (var i = 0; i < existed.length; i++) {
				html += "<div class='well' style='margin:10px;'><div class='word'>" + (existed[i].Mongolian ? ($.trim(existed[i].Mongolian).length == 0 ? latinToMongolian(existed[i].MongolianLatin) : existed[i].Mongolian ) : latinToMongolian(existed[i].MongolianLatin)) + "</div>";
				html += "<div class='source' >" +  enumerableData.getName(existed[i].SourceDictionary?existed[i].SourceDictionary:"", enumerableData.sourceDictionary, true) + "</div></div>"
			} // style='height:460px;'

			
			existWords.show();
			existWords.html(html);
		    }
		});
	});
	
    
	$("#btnSave").click(function(){
/*
		if (showTip.nodes.length) {
	        alert("数据验证没有通过，请检查,中文词条必须填写!");
	        return;
	    }
	    */
		var queryCode = makePy(document.getElementById("Chinese").value);
		if(queryCode.length){
			queryCode = queryCode[0];
		} else {
			queryCode = "";
		}
		var postData = {
				//"ItemId": document.getElementById("ItemId").value,
				//"WordId": document.getElementById("WordId").value,
				"Chinese": document.getElementById("Chinese").value,
				"QueryCode": queryCode,//makePy(document.getElementById("Chinese").value),
				"Pinyin": jQuery.trim( Pinyin.GetQP(document.getElementById("Chinese").value)),//document.getElementById("Pinyin").value,
				"Mongolian": $.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"MongolianLatin": "",//document.getElementById("MongolianLatin").value,
				"MongolianCyrillic": "",//document.getElementById("MongolianCyrillic").value,
				"English": document.getElementById("English").value,
				"Japanese": document.getElementById("Japanese").value,
				"WordCategory":$("#WordCategory").val(),
				"Description":$("#Description").val(),
				"SourceDictionary": 101,//getQueryStringByName("category"), // 为添加词条服务的数据
			    "Status": 9, // 8表示 编辑完成
				"LastModifiedBy":<?php echo $_SESSION["UserId"] ?>,
			    "UserId":<?php echo $_SESSION["UserId"] ?>
			};
		if(!postData.Chinese){
			alert("必须录入中文词条名！");
			return false;
		}
		if(!postData.Mongolian){
			alert("必须录入传统蒙古文！");
			return false;
		}
		
		jQuery.post("_wordcontroller.php?op=add", postData, function (data) {
			var data = eval("(" + data + ")");
			//alert(data.msg);
			if(data.success){
				if(jQuery.browser.msie){
	        		document.getElementById("txtMongolian").SetUnicodeText("");
	    		} else {
	    			$( "#Mongolian" ).val("");
	    		}
	    		
				document.getElementById("Chinese").value = "";
				document.getElementById("Mongolian").value = "";
				document.getElementById("English").value = "";
				document.getElementById("Japanese").value = "";
				//document.getElementById("MongolianLatin").value = "";
				//document.getElementById("MongolianCyrillic").value = "";
				$("#Description").val("");

				$("#words").hide();
				$("#words").html("").height(0).hide();
				$("#existWords").html("").height(0).hide();
				
				var saveInfo = document.getElementById("saveInfo");
				saveInfo.innerHTML = "保存词条成功";
				setTimeout(function(){saveInfo.innerHTML = ""; },3000);
			} else {
				alert(data.msg);

			}
		});
	});
	$("#btnCancel").click(function(){
		
	});

	/*
	 validate("#wordForm", "#Chinese", {
		 "必填":function(el){ return $.trim(el.value).length != 0 },
		 "词条已存在":function(el){ 
			 var postData = {"Chinese" : el.value, "UserId":<?php echo $_SESSION["UserId"] ?>};
			 jQuery.post("_wordcontroller.php?op=existword", postData,function(data) {
			 	var cnt = eval("(" + data + ")");
			 	if(cnt.success && parseInt(cnt.count) > 0) {
			 		 showTip(el, 0, "词条已存在");
			 	} else {
			 		// 验证通过
			 		showTip(el, 1);
			 	}
			 	
			 });
			 return true;
			}
		});
		*/
});			
</script>
</body>
</html>