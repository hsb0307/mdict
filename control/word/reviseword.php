<?php

session_start ();
if (! isset ( $_SESSION ["UserId"] )) {
	header ( 'Location: ../../login.htm?returnurl=' . $_SERVER ['REQUEST_URI'] );
	die ();
	exit ();
}
$roleId = $_SESSION ['RoleId'];
$packageLink = 'myeditpackage.php';
switch ($roleId) {
	case 1 :
		$packageLink = 'myeditpackage.php';
		break;
	case 2 :
		$packageLink = 'myrevisepackage.php';
		break;
	case 3 :
		$packageLink = 'myapprovepackage.php';
		break;
	default :
		$packageLink = 'myeditpackage.php';
}
//echo $_COOKIE["mongolian_dictionary"];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>词条编辑</title>
<?php include("../../css.php"); ?>
<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; right:60px;}
#toolbar {margin-top:6px;}
#packages{width:170px;}
#txtSearch{width:120px;}
.span6{width:500px;}
.span6 #user {display:inline;}
.span6 #add,.span6 #left,.span6 #right,.span6 #history,.span6 #user{margin-left:30px;}
.span9 {margin-left:10px;  } /* position: relative; */
.span9 {width:810px;}
.span12 {width:1140px;}
.span2 {width:110px;}
.nav-list li:hover {color: #08C;cursor: pointer;cursor: hand;}

#bottomPager {margin-top:10px;}
#bottomPager #pageinfo,#bottomPager #pager{display:inline;}
#pagination a.first, #pagination a.last, #pagination a.prev,#pagination a.next{font-family:\5b8b\4f53;}
#txtMongolian{top:277px;left:913px;position: absolute;width:288px; height: 125px;}
.form-horizontal{margin-bottom:0px;}
.form-horizontal .controls{margin-left:120px;}
.form-horizontal .control-group{margin-bottom:6px;}
.form-horizontal .control-label{width:110px;}
.form-horizontal .form-actions{margin-bottom:0px;}
.input-xlarge{width:300px}
.well{}
.nav-list{padding-left:0px;padding-right:6px;}
.nav-list li {position: relative; line-height:30px; }
.nav-list li a{position:absolute; right:1px; width:14px;padding:0;display:none;  } /* float:right; */
#Mongolian{height:100px;}
#saveInfo{display:-moz-inline-box;display:inline-block;width:86px;}
#upDown a{margin-left:10px;}
.hint{position: relative; }
.hint .alert{position: absolute; right:0; top:3px; }
#imgNav{float: right;margin-right:30px; width:130px;}
#imgNav img{height:26px; width:26px;}
#operation {margin-left:30px;}
#operation button {margin-left:20px;}
#operation a {margin-left:20px;}
#operation span {margin-left:20px;}
.source{font-family:' \5b8b\4f53';font-size: 14px; margin:4px 0 0 20px; letter-spacing:2px;  word-spacing:4px;}

</style>
</head>

<body>
	<?php include("../../header.php"); ?>
	<div id="toolbar" class="container">
		<div class="row">
			<div class="span12">
					数据包：<select id="packages"></select>
					<input type="text" id="txtSearch" value=""> <a id="btnSearch" href="javascript:void(0)"><i class="icon-search"></i></a>&nbsp;&nbsp;
					<a id="btnClear" href="javascript:void(0)"><i class="icon-remove"></i></a>&nbsp;&nbsp;
					<a id="aSearch" href="selectword.php?w=" target="_blank">在整个词典中查询</a>
					<span id="operation">
					<button id="btnAdd" class="btn">增加词条</button>
					<a id="left" href="javascript:void(0)" tag="10"><i class="icon-arrow-left"></i></a>
					<a id="right" href="javascript:void(0)" tag="0"><i class="icon-arrow-right"></i></a>
					<button id="history" class="btn">历史记录</button></span>
					<span id="user"><i class="icon-user"></i></span>
					<span id="imgNav" >
						<a href="../task/myrevisepackage.php" target="_blank" title="我的数据包"><img src="../../images/package.png" alt="我的数据包" /></a>
						<a href="selectword.php" target="_blank" title="词条查询"><img src="../../images/search.png" alt="词条查询" /></a>
						<a href="../log/index.php" target="_blank" title="操作日志"><img src="../../images/log.png" alt="操作日志" /></a>
						<a href="../guestbook/index.php" target="_blank" title="留言本"><img src="../../images/guestbook.png" alt="留言本" /></a>
					</span>	
				</div>
		</div>
		
	</div>
	
	<div class="container">
		<div class="row">
			<div class="well sidebar-nav span3">
				<ul id="words" class="nav nav-list">

				</ul>
			</div>
			<div class="well span9">
			
<div class="hint">
<div class="alert alert-error" style="width:120px; z-index: 100; display:none;">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>警告!</strong> 您有数据包<a href="../task/myrevisepackage.php" target="_blank">已经超期</a>，请尽快提交。
</div>
</div>
			
			<form class="form-horizontal">
        <fieldset>
			<div class="row">
			 <div class="span6">
			 
         <input type="hidden" id="ItemId" value="" />
         <input type="hidden" id="WordId" value="" />
         <input type="hidden" id="SourceDictionary" value="" />
         <input type="hidden" id="Status" value="" />
         
          <div class="control-group">
            <label class="control-label" for="Chinese">中文:</label>
            <div class="controls"><input type="text" class="input-xlarge" id="Chinese"  disabled="disabled"><a id="btnQuery" href="javascript:void(0)" tag="0"><i class="icon-search"></i></a></div>
            
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
            <label class="control-label" for="Mongolian">传统蒙文:</label>
            <div class="controls"><textarea cols="3"  class="input-xlarge mongolian" id="Mongolian" style="height: 160px;"></textarea></div>
          </div>
           -->
          <div class="control-group">
            <label class="control-label" for="MongolianCyrillic">西里尔蒙古文:</label>
            <div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianCyrillic"></textarea></div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">蒙古文拉丁转写:</label>
            <div class="controls"><textarea rows="3" class="input-xlarge" id="MongolianLatin"></textarea>
            <a id="btnTransfer" href="javascript:void(0)" tag="0"><i class="icon-arrow-right"></i></a>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">词条类别:</label>
            <div class="controls"><select id="WordCategory" name="WordCategory"></select> </div>
          </div>
          
          <div class="control-group">
            <label class="control-label" for="Chinese">词条来源:</label>
            <!-- <div class="controls"><input type="text" class="input-xlarge" id="Chinese"  disabled="disabled"></div> -->
            <div style="margin-top:6px;"><span id="SourceDictionary2" style="padding-left: 18px; "></span></div>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn btn-primary" id="btnSave">保存</button>
            <!-- <button class="btn">取消</button> -->
            <span id="saveInfo" class="text-success"></span>
            <span id="upDown">
				<a id="arrowDown" href="javascript:void(0)"><i class="icon-arrow-down"></i>下一条</a>
				<a id="arrowUp" href="javascript:void(0)"><i class="icon-arrow-up"></i>上一条</a>
				</span>
          </div>
		  <div class="progress" id="progress">
			<div class="bar" style="width: 0;"></div>
		  </div>
				<div id="bottomPager">
					<div id="pageinfo"></div>
					<div class="ypager toppagination" id="pager"></div>
				</div>
			</div>
			
			 <div class="span2" style="margin-left:10px;" >
			 
			 <!-- <label for="Mongolian">传统蒙文:</label>  -->
            <textarea class="mongolian" id="Mongolian" style="height: 404px;width:80px;"></textarea>
            <OBJECT classid="clsid:537BF42E-B49F-444F-B8AD-F6A862504B32"
					codebase="HgUnicodeSgOcx.cab#version=1,0,0,1" id='txtMongolian'
					style="font-family: Mongolian Baiti;"
					align="center" hspace=0 vspace=0>
			</OBJECT>
			 </div>
			 <div  class="span2" style="margin-left:2px;">
 				<div id="existWords" class="mongolian hide"></div>
			</div>
			</div>
			</fieldset>
      </form>
      

      
			</div>
		</div>
	</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/ChinesePY.js"></script>

<script type="text/javascript">

$(function () {
	var ctx = {};
	ctx.page = getQueryStringByName("page");
	if(!ctx.page)
		ctx.page = 1;
	
	ctx.packageId = ctx.packageId
	ctx.packageId = getQueryStringByName("packageid");
	if(!ctx.packageId){
		ctx.packageId = $.cookie("currentPackageId");
	}
	//if(!ctx.packageId){
	//	ctx.packageId = 1;
	//}
	
	ctx.word = null;
	ctx.index = 0; 
	ctx.wordIndex = null;
	ctx.rowCount = null;
	ctx.pageSize = 20;
	ctx.startRowIndex = 0;
	ctx.navPage = 0;
	ctx.total = 0;
	ctx.countHandled = 0;
	ctx.packageStatus = 0;

	var existWords = $("#existWords");

	var roleId = <?php echo $_SESSION ['RoleId']; ?>;
	if(!(roleId == 91 || roleId == 2 || roleId == 82) ) {
		$('#btnSave').prop("disabled","disabled");
	}

	function setProgress(total, countHandled){
    	var percent = total == 0 ? 0 : Math.round(countHandled/total*100);
    	percent = percent > 100 ? 100 : percent;
    	
    	$("#progress .bar").css("width",percent + "%").html(percent+"%");
    }
	//$(".alert").alert();
	//$(".alert").alert('close');
	jQuery.post("../task/_taskcontroller.php?op=expire", {"Days":warningDays}, function (data) {
		var cnt = eval("(" + data + ")");
		if(cnt > 0){
			$(".alert").alert();
			$(".alert").show();
		}
	});

	//alert($('#target').offset().left);
	$("#btnAdd").click(function(){
		var packageid = ctx.packageId;
		if(packageid){
			location = 'newword.php?packageid=' + packageid + '&page=' + ctx.page + '&category=' + document.getElementById("SourceDictionary").value;
		} else {
			location = 'newword.php?page=' + ctx.page+ '&category=' + document.getElementById("SourceDictionary").value;
		}
	});// 
	$("#history").click(function(){
		
	});
	$("#btnQuery").click(function(){
		//$(ctx.word)
		queryWord(false);
	});

	function queryWord(auto){
		var postData = {"SearchText":document.getElementById("Chinese").value, 
				"WordId": document.getElementById("WordId").value};
		jQuery.post("_wordcontroller.php?op=search" , postData, function (data) {
			if(!data){
				if(!auto) {
					alert("查询失败");
				}
				return false;
			}
			var rows = eval("(" + data + ")");
			if(!rows || rows.length == 0){
				if(!auto) {
					alert("没有查找到其他的相同词条：" + postData.SearchText);
				}
				return false;
			}
			var html ='';
			for (var i = 0; i < rows.length; i++) {
				var mongolianText = rows[i].Mongolian ? ($.trim(rows[i].Mongolian).length == 0 ? latinToMongolian(rows[i].MongolianLatin) : rows[i].Mongolian ) : latinToMongolian(rows[i].MongolianLatin);
				html += "<div style='margin:3px;padding:3px;'><div class='word' style='height:"+ (mongolianText.length * 15) + "px;'>" + mongolianText + "</div>";
				html += "<div class='source' style='height:460px;' >" +  enumerableData.getName(rows[i].SourceDictionary?rows[i].SourceDictionary:"", enumerableData.sourceDictionary, true) + "</div></div>"
			}
			// class='well'
			existWords.show().html();
			//existWords;
			existWords.html(html);
		});
	}
	//

	setTimeout(function(){
		var m=$('#Mongolian');//.offset();
		var o = m.offset();
		var txtMongolian = document.getElementById('txtMongolian');//'#txtMongolian'
		if(jQuery.browser.msie){
			//$('#txtMongolian').parent().height(200);
			$(txtMongolian).css("top", m.offset().top).css("left",m.offset().left  ).css("height", m.height() +10).css("width",m.width() + 14);
			//m.css("display", "none")
			txtMongolian.SetFontSize(30);
			txtMongolian.SetMWFontName("Mongolian Baiti");
		} else {
			txtMongolian.style.display="none";
		}
		
		
	},0);

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
	
	$("#btnSave").click(function(){
		if ( $.browser.msie ){
			//document.getElementById("txtMongolian").SetUnicodeText($("#Mongolian").val());
			//alert(document.getElementById("txtMongolian").GetUnicodeText());
			//$("#Mongolian").val(document.getElementById("txtMongolian").GetUnicodeText());
		}
		if(!ctx.word) {
			alert("当前没有可保存的词条");
			return false;
		}		
		var postData = {
				"ItemId": document.getElementById("ItemId").value,
				"WordId": document.getElementById("WordId").value,
				"Chinese": document.getElementById("Chinese").value,
				"Pinyin": document.getElementById("Pinyin").value,
				"Mongolian": $.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"MongolianLatin": document.getElementById("MongolianLatin").value,
				"MongolianCyrillic": document.getElementById("MongolianCyrillic").value,
				"English": document.getElementById("English").value,
				"Japanese": document.getElementById("Japanese").value,
				"SourceDictionary":document.getElementById("SourceDictionary").value, // 为添加词条服务的数据
				"WordCategory":$("#WordCategory").val(),
				"LastModifiedBy":<?php echo $_SESSION["UserId"] ?>,
			    //"Status": 8, // 8表示 编辑完成
				"PackageStatus":ctx.packageStatus,
			    "PackageId":ctx.packageId
			};
		
		jQuery.post("_wordcontroller.php?op=edit", postData, function (data) {
			var response = eval("(" + data + ")");
			if(response.success){
				var saveInfo = document.getElementById("saveInfo");
				saveInfo.innerHTML = "保存成功";
				setTimeout(function(){saveInfo.innerHTML = ""; },3000);
				ctx.navPage = 10;
				//console.log(document.getElementById("Status").value);
				
				if( document.getElementById("Status").value == 0){
					//console.log("11Status:" + document.getElementById("Status").value);
					ctx.countHandled = ctx.countHandled + 1;
					document.getElementById("Status").value = 2;
					//console.log("22Status:" + document.getElementById("Status").value);
				}
				//console.log(ctx.total);
				//console.log("ctx.countHandled:" + ctx.countHandled);
				setProgress(ctx.total, ctx.countHandled);
				//$("#progress .bar").attr("style","width: "20%");
			}
		});
	});
	
	// 这里借用了数据包分页处理逻辑来获取当前用户的数据包,PageSize取一个较大的值，以保证数据只有一页。
	var postData = {"PageSize":10000, 
			"UserId":<?php echo $_SESSION ['UserId']; ?>, 
			"RoleId": <?php echo $_SESSION ['RoleId']; ?>,
			"Edit":1};//"_get_editpackage_count.php"
	jQuery.post("../task/_get_revisepackage_pagination.php", postData, function (data) {
		var resp = eval("(" + data + ")");
		var count = resp.Count;
		var rows = resp.Rows;
		if(rows && rows.length == 0){
			alert("没有合适的数据包包含需要修改的词条!");
			return false;
		}
		
		var html = "";
		var id = ctx.packageId;
		for (var i = 0; i < rows.length; i++) {
			var addition ="";
			if(rows[i].Status == 2){addition = "（已提交）";}
			if(rows[i].Status == 4){addition = "（已撤销）";}
			if(id && id == rows[i].PackageId){
				html += "<option label='"+ rows[i].PackageName + addition +"' value='"+ rows[i].PackageId +"' data-status='"+ rows[i].Status +"' selected='selected'>" + rows[i].PackageName + "</option>";
			} else {
				html += "<option label='"+ rows[i].PackageName + addition +"' value='"+ rows[i].PackageId +"' data-status='"+ rows[i].Status +"'>" + rows[i].PackageName + "</option>";
			}
		}
		var tableBody = $("#packages");
	    tableBody.html();
	    tableBody.html(html);

	    if(rows.length > 0){
	    	var packages1 = document.getElementById("packages");
	    	var selectedIndex = 0;
	    	for(var i = 0, j = rows.length;i < j; i++){
		    	if(packages1.options[i].value == ctx.packageId) {
		    		selectedIndex = i;
		    		break;
		    	}
	    	}
	    	packages1.options[selectedIndex].selected=true;
			ctx.packageId = packages1.options[packages1.selectedIndex].value;
			ctx.packageStatus = $(packages1.options[selectedIndex]).data("status");
			$.cookie("currentPackageId", ctx.packageId, { expires:30});
	    	jQuery(packages1).live("change", function(){
		    	//console.info(this.options[this.selectedIndex].value);
	    		ctx.packageId = this.options[this.selectedIndex].value;
	    		ctx.packageStatus = $(this.options[this.selectedIndex]).data("status");
	    		$.cookie("currentPackageId", ctx.packageId, { expires:30});
		    	refreshWordList(ctx.packageId, 1);//ctx.page
		    });
	    	refreshWordList(ctx.packageId, ctx.page);
		}
	});

	function dataBind(row){
		if(!row) return;
		//if(!row.English) {alert(row.English)};
		if(row.Status != 0) {
			//$('#btnSave').prop("disabled","disabled");
			//console.log("row.Status:" + row.Status);
		}
		existWords.hide();
		$("#aSearch").attr("href","selectword.php?w="+row.Chinese);
		if(row.Mongolian && $.trim(row.Mongolian).length == 0) { row.Mongolian = "";}
		document.getElementById("ItemId").value = row.ItemId;
		document.getElementById("WordId").value = row.WordId;
		document.getElementById("Chinese").value = row.Chinese ? row.Chinese :"";
		document.getElementById("Pinyin").value = row.Pinyin? row.Pinyin : jQuery.trim( Pinyin.GetQP(row.Chinese));//"";
		if($.browser.msie){
			document.getElementById("txtMongolian").SetUnicodeText(row.Mongolian?row.Mongolian:"");
		} else {
			document.getElementById("Mongolian").value = row.Mongolian?row.Mongolian:"";
		}	
		document.getElementById("MongolianLatin").value = row.MongolianLatin?row.MongolianLatin:"";
		document.getElementById("MongolianCyrillic").value = row.MongolianCyrillic?row.MongolianCyrillic:"";
		document.getElementById("English").value = row.English?(row.English=="null"? "":row.English):"";
		document.getElementById("Japanese").value = row.Japanese?(row.Japanese=="null"? "":row.Japanese):"";//row.Japanese?row.Japanese:"";
		document.getElementById("SourceDictionary").value = row.SourceDictionary?row.SourceDictionary:"";
		
		setWordCategory(row.WordCategory);
		document.getElementById("SourceDictionary2").innerHTML = enumerableData.getName(row.SourceDictionary?row.SourceDictionary:"", enumerableData.sourceDictionary, true) ;
		document.getElementById("Status").value = row.Status;

		var txtJapanese = document.getElementById("Japanese");
		if(!txtJapanese.value) {
			setTimeout(function(){ 
				if(txtJapanese.value.length == 0){  
					txtJapanese.value = "正在从网络读取日文翻译，请稍等..."; 
				} 
			}, 500);

		jQuery.post("_wordcontroller.php?op=jp&q=" + encodeURIComponent(document.getElementById("Chinese").value), {}, function (data) {
			if(!data) return false;
            if (data.length < 40 && data.charCodeAt(0) == 9) {
            	txtJapanese.value = ""; 
                return false;
            }
			var result = eval("(" + data + ")");
			
			if(txtJapanese.value.length == 0 || txtJapanese.value == "正在从网络读取日文翻译，请稍等..."){
				document.getElementById("Japanese").value = result.trans_result[0].dst;
			}
		});
		}

		if(!document.getElementById("English").value || !jQuery.trim(document.getElementById("English").value)) {

			jQuery.post("_wordcontroller.php?op=en&q=" + encodeURIComponent(document.getElementById("Chinese").value), {}, function (data) {
				if(!data) return false;
				var result = eval("(" + data + ")");
				document.getElementById("English").value = result.trans_result[0].dst;
			});
		}
		
		if(!row.Mongolian && row.MongolianLatin) {
			if(Sys.ie){
				document.getElementById("txtMongolian").SetUnicodeText(latinToMongolian(row.MongolianLatin));
			} else {
				document.getElementById("Mongolian").value = latinToMongolian(row.MongolianLatin);
			}
		}

		queryWord(true);
	};

	function setRow(li){
		var parent = jQuery(li.parentNode);
		var self = jQuery(li);
    	if(!self.hasClass("selected")){
    		parent.children(".selected").removeClass('selected');
    		self.addClass('selected');
    	};
    	//alert(list.parent().children(".selected").html());
		$("#pageinfo").html("当前第" + ctx.wordIndex + "&nbsp;/&nbsp;" + ctx.rowCount +"条记录，每页20条&nbsp;");
		jQuery.post("_wordcontroller.php?op=getword&id=" + li.id, {}, function (data) {
			var row = eval("(" + data + ")");
			if(!row.ItemId) row.ItemId = self.data("itemid");
			//row.ItemId = self.data("itemid");// self.attr("data-itemid");// data-itemid
			//alert(self.data("itemid") + " " + self.attr("data-itemid"));
			//alert(self.data("status"));
			row.Status = self.data("status");
			dataBind(row);
		});
	};

	function refreshWordList(packageId, page){
		var opts = {//"getCountUrl":"_get_editpackage_count.php",
				"getDataUrl":"_get_reviseword_pagination.php?first=1",
				"init":true,
				"page":page,
				"pageSize":20,
				"postData":{"PackageId":packageId, "UserId":<?php echo $_SESSION ['UserId']; ?>, "Filter":document.getElementById("txtSearch").value  },
				"onFill":function(rows, o){
					if(o.count == 0){
						alert("没有发现数据！");
						return;
					}
					ctx.page = o.page;
					ctx.index = 0;
					ctx.startRowIndex = o.startRowIndex;
					ctx.wordIndex = o.startRowIndex + 1;
					ctx.rowCount = o.count;
					ctx.pageSize = o.pageSize;
					//console.info(o.page);
					//dataBind(o.first);
					//$("#pageinfo").html("当前第" + (o.startRowIndex + 1) + "&nbsp;/&nbsp;" + o.count +"条记录，每页20条&nbsp;");
					ctx.total = o.total;
					ctx.countHandled = parseInt(o.countHandled) ;
					
					setProgress(o.total, o.countHandled);
					var role = <?php echo $_SESSION ['UserId']; ?>;
					var html ='';
					for (var i = 0; i < rows.length; i++) {
						var headWord = "";
				        if (rows[i].Chinese && rows[i].Chinese.length > 30) {
				            //headWord = rows[i].Chinese.substr(0, 15) + "...";
				        	headWord = rows[i].Chinese;
				        } else {
				            headWord = rows[i].Chinese;
				        }
						if( role > 1){ 
							//alert(rows[i].Status);
							if(rows[i].Status == 2 || rows[i].Status == 4) { 
								html += "<li id='" + rows[i].WordId + "' class='text-info' tag='" + i + "' data-itemid='" + rows[i].ItemId + "' data-status='" + rows[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + rows[i].WordId + "' title='删除' data-itemid='" + rows[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}else if(rows[i].Status == 6) {
								html += "<li id='" + rows[i].WordId + "' class='text-warning' tag='" + i + "' data-itemid='" + rows[i].ItemId + "' data-status='" + rows[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + rows[i].WordId + "' title='删除' data-itemid='" + rows[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}else {
								html += "<li id='" + rows[i].WordId + "' tag='" + i + "' data-itemid='" + rows[i].ItemId + "' data-status='" + rows[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + rows[i].WordId + "' title='删除' data-itemid='" + rows[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}
				        }else{
				        	html += "<li id='" + rows[i].WordId + "' tag='" + i + "' data-itemid='" + rows[i].ItemId + "' data-status='" + rows[i].Status + "'><span>" + headWord + "</span></li>";
				        }
				    }
				    
				    var ul = $("#words");
				    ul.html();
				    var list = ul.html(html).children();
				    //alert(list.get(0));
				    if(list.length > 0) {
				    	ctx.word = list.get(0);
				    	setRow(ctx.word);
				    }

				    list.each(function(i){
				    	jQuery(this).live("click", function(){
					    	var index = parseInt(this.getAttribute("tag"));
					    	ctx.index = index;
				    		//ctx.index = i;
				    		ctx.word = this;
				    		ctx.wordIndex = (o.startRowIndex + index + 1);
				    		ctx.rowCount = o.count;
				    		setRow(this);
				    	});
					});

				    list.hover(
				    	//function () { jQuery(this).addClass('active'); },
				        //function () { jQuery(this).removeClass('active'); }
				    	function () {
					    	var li =  jQuery(this).css('background-color','#e3e3e3');
					    	//alert(li.prop("id"));
					    	
					    	var btnDelete = li.children().get(1);
					    	btnDelete.style.display = "inline"; 
					    	btnDelete.onclick = function(){
						    	var id = btnDelete.parentNode.id;
						    	if(window.confirm("您确实要删除当前词条?")){
		                        	$.ajax({
		                                url: "_wordcontroller.php?op=delete",
		                                type: "POST",
		                                dataType: 'json',
		                                data: {"UserId":<?php echo $_SESSION ['UserId']; ?>, "WordId":id, "PackageId":ctx.packageId, "Chinese":btnDelete.parentNode.firstChild.innerHTML},
		                                success: function (data) {
		                                    if (data.success) {
		                                        //alert(data.msg);                                    
		                                        //operator = "d";
		                                        //getDcit();
		                                        $(btnDelete.parentNode).remove();
		                                        if(ctx.pageSize > 1){
		                                        	if(ctx.word.nextSibling && ctx.word.nextSibling.nodeName === "LI"){
		                                        		ctx.word = ctx.word.nextSibling;
			                                			ctx.index = parseInt(ctx.word.getAttribute("tag"));
			                                			ctx.wordIndex = ctx.startRowIndex + ctx.index + 1;
			                                		} else {
			                                			ctx.word = ctx.word.previousSibling;
			                                			ctx.index = parseInt(ctx.word.getAttribute("tag"));
			                                			ctx.wordIndex = ctx.startRowIndex + ctx.index + 1;
			                                		}
		                                        }
		                                        setRow(ctx.word);
		                                                                            
		                                    } else {
		                                    	alert(data.msg);                                    
		                                    }
		                                }
		                            });
		                        }  
						    };
					    },// .children().get(1).css("display","inline")
				        function () { 
					        var btnDelete = jQuery(this).css('background-color','whiteSmoke').children().get(1);
					        btnDelete.style.display = "none";
					    	btnDelete.onclick = null;
					    } // .children().get(1).css("display","none")
					);
					//list.
				} // end OnFill
		};
		pagination.paging(opts);	
	};// end refreshWordList
	
	$("#arrowUp").click(function() {
		//alert(currentWord.outerHTML());
		if(!ctx.word){
			alert("请先选择一条");
			return;
		}
		if(ctx.index == 0){
			alert("当前是第一条记录，请向后浏览。");
			return;
		}
		if(ctx.word.previousSibling && ctx.word.previousSibling.nodeName === "LI"){
			ctx.word = ctx.word.previousSibling;
			ctx.index = parseInt(ctx.word.getAttribute("tag"));
			ctx.wordIndex = ctx.startRowIndex + ctx.index + 1;
			setRow(ctx.word);
		}
	});
	$("#arrowDown").click(function() {
		if(!ctx.word){
			alert("请先选择一条");
			return;
		}
		if(ctx.index == ctx.pageSize - 1){
			alert("已经是最后一条记录了，请翻页。");
			return;
		}
		if(ctx.word.nextSibling && ctx.word.nextSibling.nodeName === "LI"){
			ctx.word = ctx.word.nextSibling;
			ctx.index = parseInt(ctx.word.getAttribute("tag"));
			ctx.wordIndex = ctx.startRowIndex + ctx.index + 1;
			setRow(ctx.word);
		}
	});
	$("#btnSearch").click(function() {
		if(document.getElementById("txtSearch").value == ""){
			alert("请输入查询条件。");
			return;
		} else {
			refreshWordList(ctx.packageId, 1);
		}
	});
	$("#btnClear").click(function() {
		document.getElementById("txtSearch").value = "";
	});
	$("#right").click(function() {
		ctx.navPage = ctx.navPage - 1;
		if(ctx.navPage <= 1) {
			alert("已经是最后一个了。");
			return;
		}
		var postData = {"PackageId":ctx.packageId, "CurrentPage":ctx.navPage};
		jQuery.post("_wordcontroller.php?op=nav", postData, function (data) {
			var row = eval("(" + data + ")");
			dataBind(row.Row);
		});
	});
	$("#left").click(function() {
		ctx.navPage = ctx.navPage + 1;
		if(ctx.navPage >= 11) {
			alert("已经是最后一个了。");
			return;
		}
		var postData = {"PackageId":ctx.packageId, "CurrentPage":ctx.navPage};
		jQuery.post("_wordcontroller.php?op=nav", postData, function (data) {
			var row = eval("(" + data + ")");
			dataBind(row.Row);
		});
	});
	
	$("#btnTransfer").click(function() {
		var latin = document.getElementById("MongolianLatin").value;
		//alert(latin);
		var mongolian =latinToMongolian(latin);
		if(Sys.ie){
			document.getElementById("txtMongolian").SetUnicodeText(mongolian);
		} else {
			document.getElementById("Mongolian").value = mongolian;
		}
		
		
	});
	$("#txtSearch").blur(function () { 
		var text = $("#txtSearch").val();
		if(!text) {
			text = $(ctx.word).html(); 
		}
    	$("#aSearch").attr("href","selectword.php?w="+text);
    });   
	  
});
</script>
</body>
</html>