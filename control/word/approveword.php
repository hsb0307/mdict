<?php
session_start ();
if (! isset ( $_SESSION ["UserId"] )) {
	header ( 'Location: ../../login.htm?returnurl=' . $_SERVER ['REQUEST_URI'] );
	die ();
	exit ();
}
$roleId = $_SESSION ['RoleId'];
$userId = $_SESSION ['UserId'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>词条审定</title>
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

.nav-list li:hover {color: #08C;cursor: pointer;cursor: hand;}

#bottomPager {margin-top:10px;}
#bottomPager #pageinfo,#bottomPager #pager{display:inline;}
#pagination a.first, #pagination a.last, #pagination a.prev,#pagination a.next{font-family:\5b8b\4f53;}
#txtMongolian{top:277px;left:913px;position: absolute;width:288px; height: 125px;}
.form-horizontal{margin-bottom:0px;}
.form-horizontal .controls{margin-left:120px;}
.form-horizontal .control-group{margin-bottom:6px;}
.form-horizontal .control-label{width:100px;}
.form-horizontal .form-actions{margin-bottom:0px;}
.input-xlarge{width:300px}
.well{}
.nav-list{padding-left:0px;padding-right:6px;}
.nav-list li {position: relative; line-height:30px; }
.nav-list li a{position:absolute; right:1px; width:14px;padding:0;display:none;  } /* float:right; */

#saveInfo{display:-moz-inline-box;display:inline-block;width:86px;}
#upDown a{margin-left:10px;}
.hint{position: relative; }
.hint .alert{position: absolute; right:0; top:3px; }
#imgNav{float: right;margin-right:30px;}
#imgNav img{height:26px; width:26px;}
#operation {margin-left:30px;}
#operation button {margin-left:20px;}
#operation a {margin-left:20px;}
#operation span {margin-left:20px;}

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
					<!-- <button id="btnAdd" class="btn">增加词条</button> -->
					<a id="left" href="javascript:void(0)" tag="10"><i class="icon-arrow-left"></i></a>
					<a id="right" href="javascript:void(0)" tag="0"><i class="icon-arrow-right"></i></a>
					<button id="history" class="btn">历史记录</button></span>
					<span id="user"><i class="icon-user"></i></span>
					<span id="imgNav">
						<a href="../task/myapprovepackage.php" target="_blank" title="我的数据包"><img src="../../images/package.png" alt="我的数据包" /></a>
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
			
			<form class="form-horizontal">
			<fieldset>
		<input type="hidden" id="ItemId" value="" />
        <input type="hidden" id="WordId" value="" />
        <input type="hidden" id="SourceDictionary" value="" />
        <input type="hidden" id="WordCategory" value="" />
        <input type="hidden" id="Status" value="" />
        <input type="hidden" id="WordId2" value="" />
        <input type="hidden" id="QueryCode" value="" />
		
        <div class="control-group">
            <label class="control-label" for="Chinese">中文:</label>
            <div class="controls"><label class="radio" style="padding-left: 0;font-weight:bold;"><span id="Chinese"></span></label></div>
        </div> 
         <!--   
        <div class="control-group">
            <label class="control-label" for="SourceDictionary">词条来源:</label>
            <div class="controls"><label class="radio" style="padding-left: 0; "><span id="txtSourceDictionary"></span></label></div>
          </div>
          -->
        <div class="control-group">
            <label class="control-label">拼音:</label>
            <div class="controls" id="Pinyin">
            	
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">英文:</label>
            <div class="controls" id="English">
            
            </div>
          </div> 
          <div class="control-group">
            <label class="control-label" for="Japanese">日文:</label>
            <div class="controls" id="Japanese">
            
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianCyrillic">西里尔蒙文:</label>
            <div class="controls" id="MongolianCyrillic">
            
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">蒙文拉丁转写:</label>
            <div class="controls" id="MongolianLatin">
            
            </div>
          </div>
          <!-- 
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">传统蒙古文:</label>
            <div class="controls " >
            	class="mongolian"
           	</div>
          </div>
          <div class="radio mongolian">
            		<input type="radio" name="Mongolian" id="Mongolian1" style="margin-left: 1px;" value="" checked><div style="margin-top: 26px;  " ></div>
            		<input type="radio" name="Mongolian" id="Mongolian2" style="margin-left: 1px;" value="" ><div  style="margin-top: 26px;  " ></div>
           </div>
          submit
           -->
           <div class="mongolian" id="Mongolian">
           </div>
           
          <div class="form-actions">
            <button type="button" class="btn btn-primary" id="btnSave">提交</button>
            <span id="saveInfo" class="text-success">&nbsp;</span>
            <button type="button" class="btn" id="btnSkip" style="margin-left: 180px;">跳过</button>
            <button type="button" class="btn btn-inverse" id="btnAdd">新建</button>
          </div>
			</fieldset>
      </form>
      
    <div class="progress" id="progress">
		<div class="bar" style="width: 0;"></div>
	</div>
	
	<div id="bottomPager">
		<div id="pageinfo"></div>
		<div class="ypager toppagination" id="pager"></div>
	</div>
      
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
	ctx.packageId = getQueryStringByName("packageid");
	if(!ctx.packageId){
		ctx.packageId = $.cookie("currentPackageId");
	}
	if(!ctx.packageId){
		ctx.packageId = 1;
	}
	
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
	ctx.items = [];
	var items = [];
	var itemIds = [];

	var postData = {"PageSize":10000, 
			"UserId":<?php echo $userId; ?>, 
			"RoleId": <?php echo $roleId; ?>,
			"Edit":1};//"_get_editpackage_count.php"
	jQuery.post("../task/_get_approvepackage_pagination.php", postData, function (data) {
		var resp = eval("(" + data + ")");
		var count = resp.Count;
		var rows = resp.Rows;
		if(rows && rows.length == 0){
			alert("没有合适的数据包包含需要审定的词条!");
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
	}); // 结束获取数据包列表

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
	//fillWordCategory();

	function setProgress(total, countHandled){
    	var percent = total == 0 ? 0 : Math.round(countHandled/total*100);
    	percent = percent > 100 ? 100 : percent;
    	
    	$("#progress .bar").css("width",percent + "%").html(percent+"%");
    }

	function setRow(li, word){
		var parent = jQuery(li.parentNode);
		var self = jQuery(li);
    	if(!self.hasClass("selected")){
    		parent.children(".selected").removeClass('selected');
    		self.addClass('selected');
    	};
    	//alert(list.parent().children(".selected").html());
		$("#pageinfo").html("当前第" + ctx.wordIndex + "&nbsp;/&nbsp;" + ctx.rowCount +"条记录，每页20条&nbsp;");
		if(word) {
			dataBind(word);
		} else {
			//var searchText = self.data("itemid");
			jQuery.post("_wordcontroller.php?op=search" , {"SearchText":self.data("itemid")}, function (data) {
				var row = eval("(" + data + ")");
				//if(!row.ItemId) row.ItemId = self.data("itemid");
				
				row.Status = self.data("status");
				dataBind(row);
			});
		}
	};

	function getLatin(s){
		var latin = '';
    	var p = -1, q = -1, start = false;
    	for (var i = 0, j = s.length; i < j; i++) {
    		var c = s.charAt(i);
    		var charCode=  c.charCodeAt(0);// charCode < 6144
    		if(!start && charCode < 127 && charCode > 47){
	    		p = i;
	    		//q = i;
	    		start = true;
	    		continue;
    		}
    		if(start && charCode < 127 && charCode > 47){
	    		q = i;
	    		//pre = true;
	    		continue;
    		}
    		if(start && charCode > 127) {
    			q = i;
    			break;
    		}
    	}
    	if(p > -1 && (q - p > 1)) {
    		latin = s.substring(p, q);
    	}
    	return latin;
	}

	function dataBind(rows){
		var html ='';
		var maxHeight = 200;
		var htmPinyin = "", htmMongolianLatin = "", htmMongolianCyrillic = "", htmEnglish = "", htmJapanese = "", htmMongolian = "" ;
		// <label class='radio'><input type='radio' name='English' id='English1' value='' checked><span></span></label>
		itemIds = [];
		ctx.items = [];
		
		if(!rows) return;
		ctx.items = rows;
		//console.log(ctx.items);
		var latin = '';
		if(rows && rows.length > 0 ){
			latin = getLatin(rows[0].Chinese);
		}
		for(var i = 0, j = rows.length; i < j ; i++){
	    	var index = i + 1, row = rows[i];
	    	itemIds.push(row.WordId);
	    	row.Pinyin = row.Pinyin ? row.Pinyin : "";
	    	row.MongolianLatin = row.MongolianLatin ? row.MongolianLatin : "";
	    	row.MongolianCyrillic = row.MongolianCyrillic ? row.MongolianCyrillic : "";
	    	row.English = row.English ? row.English : "";
	    	row.Japanese = row.Japanese ? row.Japanese : "";
	    	row.Mongolian = row.Mongolian ? row.Mongolian : "";
	    	
	    	if(row.Mongolian && row.Mongolian.length > 15)
			{
				var h = row.Mongolian.length * 15;
				if(latin.length > 0){
					h = (row.Mongolian.length - latin.length) * 15 + latin.length * 5;
				}
				if(row.Mongolian.length > 15 && row.Mongolian.length < 20){
					h = 300;
				}
				if(row.Mongolian.length > 30) {
					h = row.Mongolian.length * 11;
				}
				if(row.Mongolian.length > 40) {
					h = row.Mongolian.length * 11;
				}
				if(maxHeight < h) { maxHeight = h; }
			}
	    	if(latin.length > 0){
	    		row.Mongolian = row.Mongolian.replace(latin, '<span style="font-size:16px;">' + latin + '</span>')
	    	}

	    	if(i == 0) {
	    		document.getElementById("WordId").value = row.WordId;
	    		document.getElementById("Chinese").innerHTML = row.Chinese ? row.Chinese :"";
	    		document.getElementById("QueryCode").value = row.QueryCode;
	    		document.getElementById("WordCategory").value = row.WordCategory;

	    		htmPinyin += "<label class='radio'><input type='radio' name='Pinyin' id='Pinyin" + index + "' value='" + row.WordId + "' checked /><span>" + row.Pinyin + "</span></label>";
	    		htmMongolianLatin += "<label class='radio'><input type='radio' name='MongolianLatin' id='MongolianLatin" + index + "' value='" + row.WordId + "' checked /><span>" + row.MongolianLatin + "</span></label>";
	    		htmMongolianCyrillic += "<label class='radio'><input type='radio' name='MongolianCyrillic' id='MongolianCyrillic" + index + "' value='" + row.WordId + "' checked /><span>" + row.MongolianCyrillic + "</span></label>";
	    		htmEnglish += "<label class='radio'><input type='radio' name='English' id='English" + index + "' value='" + row.WordId + "' checked /><span>" + row.English + "</span></label>";
	    		htmJapanese += "<label class='radio'><input type='radio' name='Japanese' id='Japanese" + index + "' value='" + row.WordId + "' checked /><span>" + row.Japanese + "</span></label>";
	    		//htmMongolian += "<div style='margin:20px;'><div class='word'><a href='javascript:void(0)'><i class='icon-ok-sign' style='margin-left:8px; margin-bottom:16px;'></i></a>" + (row.Mongolian ) + "</div></div>";
	    		htmMongolian += "<div style='margin:20px;'><div class='word'><input type='radio' name='Mongolian' id='Mongolian" + index + "' value='" + row.WordId + "' data-sourcedictionary='" + row.SourceDictionary + "' data-wordcategory='" + row.WordCategory + "' data-examinegroup='" + row.ExamineGroup + "' style='margin-left:8px; margin-bottom:10px;' checked />" + (row.Mongolian ) + "</div></div>";
	    		// 
	    		
	    	} else {
	    		htmPinyin += "<label class='radio'><input type='radio' name='Pinyin' id='Pinyin" + index + "' value='" + row.WordId + "' /><span>" + row.Pinyin + "</span></label>";
	    		htmMongolianLatin += "<label class='radio'><input type='radio' name='MongolianLatin' id='MongolianLatin" + index + "' value='" + row.WordId + "' /><span>" + row.MongolianLatin + "</span></label>";
	    		htmMongolianCyrillic += "<label class='radio'><input type='radio' name='MongolianCyrillic' id='MongolianCyrillic" + index + "' value='" + row.WordId + "' /><span>" + row.MongolianCyrillic + "</span></label>";
	    		htmEnglish += "<label class='radio'><input type='radio' name='English' id='English" + index + "' value='" + row.WordId + "' /><span>" + row.English + "</span></label>";
	    		htmJapanese += "<label class='radio'><input type='radio' name='Japanese' id='Japanese" + index + "' value='" + row.WordId + "' /><span>" + row.Japanese + "</span></label>";
	    		//htmMongolian += "<div style='margin:20px;'><div class='word'><a href='javascript:void(0)'><i class='icon-remove-sign' style='margin-left:8px; margin-bottom:16px;'></i></a>" + (row.Mongolian ) + "</div></div>";
	    		htmMongolian += "<div style='margin:20px;'><div class='word'><input type='radio' name='Mongolian' id='Mongolian" + index + "' value='" + row.WordId + "' data-sourcedictionary='" + row.SourceDictionary + "' data-wordcategory='" + row.WordCategory + "' data-examinegroup='" + row.ExamineGroup + "' style='margin-left:8px; margin-bottom:10px;'/>" + (row.Mongolian ) + "</div></div>";
	    	}
	    	
			//document.getElementById("Pinyin" + index).nextSibling.innerHTML = row.Pinyin;
			//document.getElementById("MongolianLatin" + index).nextSibling.innerHTML = row.MongolianLatin;
			//document.getElementById("MongolianCyrillic" + index).nextSibling.innerHTML = row.MongolianCyrillic;
			//document.getElementById("English" + index).nextSibling.innerHTML = row.English;
			//document.getElementById("Japanese" + index).nextSibling.innerHTML = row.Japanese;//row.Japanese?row.Japanese:"";

			
			
    	}
		

		var pinyin = $("#Pinyin");
		pinyin.html('');
		pinyin.html(htmPinyin);

		var mongolianLatin = $("#MongolianLatin");
		mongolianLatin.html("");
		mongolianLatin.html(htmMongolianLatin);

		var mongolianCyrillic = $("#MongolianCyrillic");
		mongolianCyrillic.html("");
		mongolianCyrillic.html(htmMongolianCyrillic);

		var english = $("#English");
		english.html("");
		english.html(htmEnglish);

		var japanese = $("#Japanese");
		japanese.html("");
		japanese.html(htmJapanese);
		
		var mongolians = $("#Mongolian");
		mongolians.html('');
		mongolians.html(htmMongolian);
		mongolians.height(maxHeight)
		//$("#word", mongolians).height(maxHeight);
		//console.log(maxHeight);
		//console.log(itemIds);
		
	}

	function nextWord(){
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
	}
	$("#btnSkip").click(function(){
		nextWord();
	});

	$("#btnAdd" ).click(function(){
		var rdoMongolian = jQuery("input[type='radio'][name='Mongolian']:checked");
		var postData = {
				"id":document.getElementById("WordId").value,
				"chinese": document.getElementById("Chinese").innerHTML,
				"pinyin": jQuery("input[type='radio'][name='Pinyin']:checked").next().html(),//document.getElementById("Pinyin").value,
				"mongolian": rdoMongolian.parent().text(),//$.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"mongolianlatin": jQuery("input[type='radio'][name='MongolianLatin']:checked").next().html(),
				"mongoliancyrillic": jQuery("input[type='radio'][name='MongolianCyrillic']:checked").next().html(),
				"english": jQuery("input[type='radio'][name='English']:checked").next().html(),//document.getElementById("English").value,
				"japanese": jQuery("input[type='radio'][name='Japanese']:checked").next().html(),
				"category":rdoMongolian.data("wordcategory"),
				"type":"approve",
				"page":ctx.page,
			    "packageid":ctx.packageId
			};
		/*
		var packageid = ctx.packageId;
		if(packageid){
			location = 'newword.php?type=approve&packageid=' + packageid + '&page=' + ctx.page + '&category=' + document.getElementById("WordCategory").value+ '&id=' + document.getElementById("WordId").value;
			location = 'newword.php?' + jQuery.param(postData);
		} else {
			location = 'newword.php?type=approve&page=' + ctx.page+ '&category=' + document.getElementById("WordCategory").value+ '&id=' + document.getElementById("WordId").value;
		}
		*/
		location = 'newword.php?' + jQuery.param(postData);
	});// 
	
	$("#btnSave").click(function(){
		if(!ctx.word) {
			alert("当前没有可保存的词条");
			return false;
		}
		var rdoMongolian = jQuery("input[type='radio'][name='Mongolian']:checked");
		var postData = {
				"WordId":document.getElementById("WordId").value,
				"OriginalId": rdoMongolian.val(),
				"WordIds": itemIds.toString(),
				"Chinese": document.getElementById("Chinese").innerHTML,
				"QueryCode": document.getElementById("QueryCode").value,
				"Pinyin": jQuery("input[type='radio'][name='Pinyin']:checked").next().html(),//document.getElementById("Pinyin").value,
				"Mongolian": rdoMongolian.parent().text(),//$.browser.msie? document.getElementById("txtMongolian").GetUnicodeText(): document.getElementById("Mongolian").value,
				"MongolianLatin": jQuery("input[type='radio'][name='MongolianLatin']:checked").next().html(),
				"MongolianCyrillic": jQuery("input[type='radio'][name='MongolianCyrillic']:checked").next().html(),
				"English": jQuery("input[type='radio'][name='English']:checked").next().html(),//document.getElementById("English").value,
				"Japanese": jQuery("input[type='radio'][name='Japanese']:checked").next().html(),
				"SourceDictionary":rdoMongolian.data("sourcedictionary"), // 为添加词条服务的数据
				"WordCategory":rdoMongolian.data("wordcategory"),
				"ExamineGroup":rdoMongolian.data("examinegroup"),
				"CreatedBy":<?php echo $_SESSION["UserId"] ?>,
				"LastModifiedBy":<?php echo $_SESSION["UserId"] ?>,
				"Description":"",
			    "Status": 4, // 8表示 编辑完成
				"PackageStatus":ctx.packageStatus,
			    "PackageId":ctx.packageId
			};
		//console.log(JSON.stringify(postData));
		jQuery.post("_approvecontroller.php?action=approve", postData, function (data) {
			var response = eval("(" + data + ")");
			var saveInfo = document.getElementById("saveInfo");
			if(response.success){
				saveInfo.innerHTML = "保存成功";
				
				ctx.navPage = 10;
				if( document.getElementById("Status").value == 0){
					ctx.countHandled = ctx.countHandled + 1;
					document.getElementById("Status").value = 2;
				}
				setProgress(ctx.total, ctx.countHandled);
			} else {
				saveInfo.innerHTML = "审定失败";
			}

			setTimeout(function(){saveInfo.innerHTML = " "; },2000);
		});
	});

	function refreshWordList(packageId, page){
		var opts = {//"getCountUrl":"_get_editpackage_count.php",
				"getDataUrl":"_get_approveword_pagination.php?first=1",
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
					items = rows;
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
						if( role > 2){ 
							//alert(rows[i].Status);
							/*
							if(rows[i].Status == 2 || rows[i].Status == 4) { 
								html += "<li class='text-info' tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span><a href='javascript:void(0)' title='删除' data-itemid='" + rows[i].Chinese + "'><i class='icon-remove'></i></a></li>";
							}else if(rows[i].Status == 6) {
								html += "<li class='text-warning' tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span><a href='javascript:void(0)' title='删除' data-itemid='" + rows[i].Chinese + "'><i class='icon-remove'></i></a></li>";
							}else {
								html += "<li tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span><a href='javascript:void(0)' title='删除' data-itemid='" + rows[i].Chinese + "'><i class='icon-remove'></i></a></li>";
							}
							*/
							if(rows[i].Status == 2 || rows[i].Status == 4) { 
								html += "<li class='text-info' tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span></li>";
							}else if(rows[i].Status == 6) {
								html += "<li class='text-warning' tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span></li>";
							}else {
								html += "<li tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span></li>";
							}
				        }else{
				        	html += "<li tag='" + i + "' data-itemid='" + rows[i].Chinese + "'><span>" + headWord + "</span></li>";
				        }
				    }
				    
				    var ul = $("#words");
				    ul.html();
				    var list = ul.html(html).children();
				    //alert(list.get(0));
				    if(list.length > 0) {
				    	ctx.word = list.get(0);
				    	setRow(ctx.word, o.first);
				    }

				    list.each(function(i){
				    	jQuery(this).on("click", function(){
					    	var index = parseInt(this.getAttribute("tag"));
					    	ctx.index = index;
				    		//ctx.index = i;
				    		ctx.word = this;
				    		ctx.wordIndex = (o.startRowIndex + index + 1);
				    		ctx.rowCount = o.count;
				    		setRow(this);
				    	});
					});
					/*
				    list.on("mouseover",
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
					    }).on("mouseout",// .children().get(1).css("display","inline")
				        function () { 
					        var btnDelete = jQuery(this).css('background-color','whiteSmoke').children().get(1);
					        btnDelete.style.display = "none";
					    	btnDelete.onclick = null;
					    } // .children().get(1).css("display","none")
					);
					*/
					//list.
				} // end OnFill
		};
		pagination.paging(opts);	
	};// end refreshWordList
});
</script>
</body>
</html>
      