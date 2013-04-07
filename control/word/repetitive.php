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
#Mongolian{height:100px;}
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
					数据包：<select id=WordCategory></select>
					<input type="text" id="txtSearch" value=""> <a id="btnSearch" href="javascript:void(0)"><i class="icon-search"></i></a>&nbsp;&nbsp;
					<a id="btnClear" href="javascript:void(0)"><i class="icon-remove"></i></a>&nbsp;&nbsp;
					<a id="aSearch" href="selectword.php?w=" target="_blank">在整个词典中查询</a>
					<button id="btnAdd" class="btn">增加词条</button>
					<span id="imgNav">
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
			
			<form class="form-horizontal">
			<fieldset>
		<input type="hidden" id="ItemId" value="" />
        <input type="hidden" id="WordId" value="" />
        <input type="hidden" id="SourceDictionary" value="" />
        <input type="hidden" id="Status" value="" />
		
        <div class="control-group">
            <label class="control-label" for="Chinese">中文:</label>
            <div class="controls"><label class="radio" style="padding-left: 0;font-weight:bold;"><span id="Chinese"></span></label></div>
        </div> 
          
        <div class="control-group">
            <label class="control-label" for="SourceDictionary">词条来源:</label>
            <div class="controls"><label class="radio" style="padding-left: 0; "><span id="txtSourceDictionary"></span></label></div>
          </div>
        <div class="control-group">
            <label class="control-label">拼音:</label>
            <div class="controls">
            	<label class="radio">
                	<input type="radio" name="Pinyin" id="Pinyin1" value="" checked><span></span>
              	</label>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">英文:</label>
            <div class="controls"><label class="radio"><input type="radio" name="English" id="English1" value="" checked><span></span></label></div>
          </div> 
          <div class="control-group">
            <label class="control-label" for="Japanese">日文:</label>
            <div class="controls"><label class="radio"><input type="radio" name="Japanese" id="Japanese1" value="" checked><span></span></label></div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianCyrillic">西里尔蒙文:</label>
            <div class="controls"><label class="radio"><input type="radio" name="MongolianCyrillic" id="MongolianCyrillic1" value="" checked><span></span></label></div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">蒙文拉丁转写:</label>
            <div class="controls"><label class="radio"><input type="radio" name="MongolianLatin" id="MongolianLatin1" value="" checked><span></span></label></div>
          </div>
          <div class="control-group">
            <label class="control-label" for="MongolianLatin">传统蒙古文:</label>
            <div class="controls " >
            	<div class="radio"><input type="radio" name="Mongolian" id="Mongolian1" style="margin-left: 1px;" value="" checked><div class="mongolian" style="margin-top: 26px;  " ></div></div>
           	</div>
          </div>
          
          
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">提交</button>
            <button class="btn">取消</button>
            
            <button type="submit" class="btn btn-inverse" style="margin-left: 60px;">修改</button>
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

	var items = [];
	function indexOfWord(text, words){
		if(!words && words.length < 1) return -1;
		for(var j = words.length, i = j-1;i >= 0; i--){
	    	if(words[i].Chinese == text) {
	    		return i;
	    	}
    	}
    	return -1;
	}

	function indexOfById(id, words){
		if(!words && words.length < 1) return -1;
		for(var j = words.length, i = j-1;i >= 0; i--){
	    	if(words[i].WordId == id) {
	    		return i;
	    	}
    	}
    	return -1;
	}

	function fillWordCategory(wordCategory){
		var html = "";
		var rows = enumerableData.wordCategories;
		for (var i = 0; i < rows.length; i++) {
			html += "<option label='"+ rows[i].name +"' value='"+ rows[i].id +"'>" + rows[i].name + "</option>";
		}
		//var wordCategory = document.getElementById("WordCategory");
		var c = $(wordCategory);
	    c.html();
	    c.html(html);

	    c.on("change", function(){
	    	refreshWordList(this.options[this.selectedIndex].value, 1);
	    });
	}
	function setWordCategory(categoryId, wordCategory){
		var rows = enumerableData.wordCategories;
		//var wordCategory = document.getElementById("WordCategory");
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
	var wordCategory = document.getElementById("WordCategory");
	fillWordCategory(wordCategory);

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
		//$("#pageinfo").html("当前第" + ctx.wordIndex + "&nbsp;/&nbsp;" + ctx.rowCount +"条记录，每页20条&nbsp;");
		if(word) {
			dataBind(word);
		} else {
			jQuery.post("_wordcontroller.php?op=getword&id=" + li.id, {}, function (data) {
				var row = eval("(" + data + ")");
				if(!row.ItemId) row.ItemId = self.data("itemid");
				
				row.Status = self.data("status");
				dataBind(row);
			});
		}
	};

	function dataBind(row){
		if(!row) return;
		document.getElementById("ItemId").value = row.ItemId;
		document.getElementById("WordId").value = row.WordId;
		document.getElementById("Chinese").innerHTML = row.Chinese ? row.Chinese :"";
		document.getElementById("Pinyin1").value = row.Pinyin;
		document.getElementById("Pinyin1").nextSibling.innerHTML = row.Pinyin;
		document.getElementById("MongolianLatin1").value = row.MongolianLatin;
		document.getElementById("MongolianLatin1").nextSibling.innerHTML = row.MongolianLatin;
		document.getElementById("MongolianCyrillic1").value = row.MongolianCyrillic;
		document.getElementById("MongolianCyrillic1").nextSibling.innerHTML = row.MongolianCyrillic;
		document.getElementById("English1").value = row.English;
		document.getElementById("English1").nextSibling.innerHTML = row.English;
		document.getElementById("Japanese1").value = row.Japanese;//row.Japanese?row.Japanese:"";
		document.getElementById("Japanese1").nextSibling.innerHTML = row.Japanese;
		document.getElementById("SourceDictionary").value = row.SourceDictionary;

		document.getElementById("txtSourceDictionary").innerHTML = enumerableData.getName(row.SourceDictionary, enumerableData.sourceDictionary, true) ;
		//setWordCategory(row.WordCategory);
		//document.getElementById("txtSourceDictionary").innerHTML = enumerableData.getName(row.SourceDictionary?row.SourceDictionary:"", enumerableData.sourceDictionary, true) ;
		document.getElementById("Status").value = row.Status;

		document.getElementById("Mongolian1").value = row.Mongolian;
		document.getElementById("Mongolian1").nextSibling.innerHTML = row.Mongolian;
		//console.log(row.Mongolian.length);
		//$(document.getElementById("Mongolian1").nextSibling).next().width(200).height(100);
		if(row.Mongolian){
			if( Sys.ie) {
				$(document.getElementById("Mongolian1").nextSibling).height(row.Mongolian.length * 10);
			}
		} else {
			$(document.getElementById("Mongolian1").nextSibling).height(30);
		}
	}

	function refreshWordList(categoryId, page){
		var opts = {//"getCountUrl":"_get_editpackage_count.php",
				"getDataUrl":"_wordcontroller.php?op=repetitive",
				"init":true,
				"page":page,
				"pageSize":40,
				"postData":{"WordCategory":categoryId },
				"onFill":function(rows, o){
					if(o.count == 0){
						alert("没有发现数据！");
						return;
					}
					if(!rows) return false;
					items.length = 0;
					var previous = null;
					for (var i = 0; i < rows.length; i++) {
						if(rows[i].Chinese == previous){
							//if(!rows[i])
							var index = indexOfWord(rows[i].Chinese, items);
							items[index].Others.push(rows[i]);
						} else {
							rows[i].Others = [];
							items.push(rows[i]);
						}
						previous = rows[i].Chinese;
					}
					
					var role = <?php echo $_SESSION ['UserId']; ?>;
					var html ='';// items  rows
					for (var i = 0; i < items.length; i++) {
						var headWord = "";
				        if (items[i].Chinese && rows[i].Chinese.length > 30) {
				            //headWord = rows[i].Chinese.substr(0, 15) + "...";
				        	headWord = items[i].Chinese;
				        } else {
				            headWord = items[i].Chinese;
				        }
						if( role > 1){ 
							//alert(rows[i].Status);
							if(items[i].Status == 2 || items[i].Status == 4) { 
								html += "<li id='" + items[i].WordId + "' class='text-info' tag='" + i + "' data-itemid='" + items[i].ItemId + "' data-status='" + items[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + items[i].WordId + "' title='删除' data-itemid='" + items[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}else if(items[i].Status == 6) {
								html += "<li id='" + items[i].WordId + "' class='text-warning' tag='" + i + "' data-itemid='" + items[i].ItemId + "' data-status='" + items[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + items[i].WordId + "' title='删除' data-itemid='" + items[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}else {
								html += "<li id='" + items[i].WordId + "' tag='" + i + "' data-itemid='" + items[i].ItemId + "' data-status='" + items[i].Status + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + items[i].WordId + "' title='删除' data-itemid='" + items[i].ItemId + "'><i class='icon-remove'></i></a></li>";
							}
				        }else{
				        	html += "<li id='" + items[i].WordId + "' tag='" + i + "' data-itemid='" + items[i].ItemId + "' data-status='" + items[i].Status + "'><span>" + headWord + "</span></li>";
				        }
				    }
				    
				    var ul = $("#words");
				    ul.html();
				    var list = ul.html(html).children();
				    if(list.length > 0) {
				    	//ctx.word = list.get(0);
				    	//setRow(ctx.word, o.first);
				    	setRow(list.get(0), o.first);
				    }

				    list.each(function(i){
				    	jQuery(this).on("click", function(){
					    	var index = parseInt(this.getAttribute("tag"));
					    	//ctx.index = index;
				    		//ctx.word = this;
				    		//ctx.wordIndex = (o.startRowIndex + index + 1);
				    		//ctx.rowCount = o.count;
				    		setRow(this);
				    	});
					});

				    list.mouseover(
				    	//function () { jQuery(this).addClass('active'); },
				        //function () { jQuery(this).removeClass('active'); }
				    	function () {
					    	var li =  jQuery(this).css('background-color','#e3e3e3');
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
					    }).mouseout(function () { 
					        var btnDelete = jQuery(this).css('background-color','whiteSmoke').children().get(1);
					        btnDelete.style.display = "none";
					    	btnDelete.onclick = null;
					    });
				} // end OnFill
		};// end opts
		pagination.paging(opts);	
	};// end refreshWordList

	refreshWordList($("#WordCategory").val(), 1);
});
</script>
</body>
</html>
      