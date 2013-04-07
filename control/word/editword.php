<?php

session_start ();
if (! isset ( $_SESSION ["UserId"] )) {
	header ( 'Location: ../../login.htm?returnurl=' . $_SERVER ['REQUEST_URI'] );
	die ();
	exit ();
}
$roleId = $_SESSION ['RoleId'];
$currentPackageId = $_SESSION ['CurrentpackageId'];
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

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>词典录入窗口</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="../../css/bootstrap.min.css" rel="stylesheet">
<link href="../../css/bootstrap-responsive.min.css" rel="stylesheet">
<!-- 
<link href="../../css/editword.css" rel="stylesheet">
 -->

<!--[if lte IE 6]>
 <link rel="stylesheet" type="text/css" href="../../css/bootstrap-ie6.css">
<![endif]-->
<!--[if lte IE 7]>
 <link rel="stylesheet" type="text/css" href="../../css/ie.css">
<![endif]-->

<style type="text/css">
.mongol_write
{
	-moz-writing-mode: vertical-lr; /*for mozilla*/
	-webkit-writing-mode: vertical-lr; /*for chrome*/
	-o-writing-mode: vertical-lr; /*for safari?*/
	-ms-writing-mode: tb-lr; /*not known*/
	writing-mode: tb-lr; /*IE 8 or up*/
}
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#toolbar {margin-top:6px;}
#txtSearch{width:120px;}

.span6 #user {display:inline;}
.span6 #add,.span6 #left,.span6 #right,.span6 #history,.span6 #user{margin-left:30px;}
.span9 {margin-left:10px; width:810px; } /* position: relative; */
.nav-list li{margin:6px 0px;}
.nav-list li:hover {color: #08C;cursor: pointer;cursor: hand;}
.wordcolor{	color:red;}
#bottomPager {margin-top:10px;}
#bottomPager #pageinfo,#bottomPager #pagination{display:inline;}
#pagination a.first, #pagination a.last, #pagination a.prev,#pagination a.next{font-family:\5b8b\4f53;}
#txtMongolian{top:0px;left:500px;position: absolute;width:100px; height: 380px;}
.form-horizontal .control-group{margin-bottom:10px;}
.form-horizontal .control-label{width:120px;}
.well{}
.nav-list{padding-left:0px;}

.span3 {
	width:270px\0;
	width:270px\9\0;
	*width:270px;
	_width:270px;
}

/*
.span9 {
	width:830px\0;
	width:856px\9\0;
	*width:810px;
	_width:790px;
}*/

#entry{position: relative;}
.Mongolian{position: absolute;top:0px;left:500px; }
.convert{position: absolute;top:300px;left:450px; }
.form-horizontal .controls{margin-left: 130px;}

</style>
</head>

<body>
	<?php include("../../header.php"); ?>
	
	<div id="toolbar" class="container">
		<div class="row">
			<div class="span6" style="width:570px">
				<div>
					数据包：<select id="dataPackageList"></select><span id="currentPackage" style="display: none"><?php echo $currentPackageId; ?></span>
					<input type="text" id="txtSearch" value=""> <a id="btnSearch" href="javascript:void(0)"><i class="icon-search"></i></a>&nbsp;&nbsp;
					<a id="aSearch" href="selectword.php" target="_blank">在整个词典中查询</a>					
				</div>
			</div>
			<!-- <div class="span6"><button id="add" class="btn">增加词条</button>  -->
				<a id="left" href="javascript:void(0)" style="margin-left:10px"><i class="icon-arrow-left"></i></a>
				<a id="right" href="javascript:void(0)" style="margin-left:10px"><i class="icon-arrow-right"></i></a>
				<button id="history" class="btn" style="margin-left:10px">历史记录</button>
				<div id="user" style="display: inline;margin-left:10px">
					<i class="icon-user"></i>
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="container">
		<div class="row">
			<div class="well sidebar-nav span3">
				<ul id="words" class="nav nav-list">
				</ul>
			</div>
			<div class="well sidebar-nav span9">
				<div id="entry">				
					<div class="alert alert-error" style="width:120px; z-index:100;display:none;position:absolute;top:0px;right:20px;">
  						<button type="button" class="close" data-dismiss="alert">&times;</button>
  						<strong>警告!</strong> 您有数据包<a href="../task/myeditpackage.php" target="_blank">已经超期</a>，请尽快提交。
					</div>								
					<div data-bind="with: wordItem" class="form-horizontal"
						id="entryInput">
						<div id="WordId" data-bind="text: WordId" style="display: none"></div>
						<div class="control-group">
							<label class="control-label" for="textarea">中文:</label>
							<div class="controls">
								<input type="text" id="Chinese"
									data-bind="value:Chinese,enable: $root.wordChinese"
									class="input-xlarge" />
							</div>
						</div>						
						<div class="control-group">
							<label class="control-label" for="textarea">拼音:</label>
							<div class="controls">
								<input type="text" data-bind="value:Pinyin" class="input-xlarge" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="textarea">英文:</label>
							<div class="controls">
								<input type="text" data-bind="value:English"
									class="input-xlarge" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="textarea">日文:</label>
							<div class="controls">
								<input type="text" data-bind="value:Japanese" id="Japanese"
									class="input-xlarge" />
							</div>
						</div>						
						<div class="control-group">
							<label class="control-label" for="textarea"> 西里尔蒙古文:</label>
							<div class="controls">
								<textarea data-bind="value:MongolianCyrillic" rows="3"
									class="input-xlarge"></textarea>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="textarea">蒙古文拉丁转写:</label>
							<div class="controls">
								<textarea data-bind="value:MongolianLatin" rows="3"
									class="input-xlarge" id="MongolianLatin"></textarea>
							</div>
						</div>
						<div class="convert">
						<a id="convert" href="javascript:void(0)"><i class="icon-arrow-right"></i></a>
						</div>
						<div class="control-group">
							<label class="control-label" >词条类别:</label>
							<div class="controls">
								<select data-bind="options: $root.wordCategories,optionsText: 'name',optionsValue:'id',value:WordCategory,optionsCaption: '请选择...' "></select>								
								<!--dynamic radio  <div data-bind="foreach:$root.wordCategories">								
									<input type="radio" data-bind="value:id,checked: $parent.WordCategory" name="words" /><span data-bind="text:name"></span><br/>
								</div>-->								
							</div>
						</div>
						<div class="control-group">
						<label class="control-label" > 词条来源:</label>
							<div class="controls" style="margin-top: 5px;">
								<span data-bind="html:SourceDictionary"></span>
							</div>
						</div>
 						<div class="Mongolian">
							<textarea data-bind="value:Mongolian" rows="5"
								class="input-xlarge mongol_write" id="MONGOLIANWord"
								style="writing-mode: tb-lr;font-size: 20px;font-family: Mongolian Baiti;height: 370px;width:80px;"></textarea>
						     <!-- font-family:Menksoft2007 writing-mode: tb-lr; -->
 						</div>	
						<div  class="form-actions">
							<button type="button" class="btn btn-primary"
								data-bind="click: $root.updateWordItem,enable: $root.wordQuery"
								id="btnModify">确定</button>								
								<span class="alert alert-success fade in" style="width:50px;display:none">操作成功 </span>								
								<a href="javascript:void(0)" id="btnUp" style="margin-left:10px;"><i class="icon-arrow-up"></i>上一条</a>
								<a href="javascript:void(0)" id="btnDown"><i class="icon-arrow-down"></i>下一条</a>
							<!-- <button id="btnTest">test</button> -->							
						</div>						
					</div>				
				<OBJECT classid="clsid:537BF42E-B49F-444F-B8AD-F6A862504B32" 
					codebase="HgUnicodeSgOcx.cab#version=1,0,0,1" id='txtMongolian' 
					style="font-family: Mongolian Baiti;"
					align="center" hspace=0 vspace=0>
 				</OBJECT> 
				<div class="progress" id="progress"
					data-bind="visible:$root.wordQuery">
					<div class="bar" style="width: 0;"></div>
				</div>
				<div id="bottomPager">
					<div id="pageinfo"></div>
					<div class="ypager toppagination" id="pagination"></div>
				</div>									
				</div>				
			</div>
		</div>
	</div>

	<!-- <div class="clearfix"></div> -->
	<div id="footer" class="container">
	
	</div>
<script type="text/javascript">

//$(function(){if($.browser.msie&&parseInt($.browser.version,10)===6){$('.row div[class^="span"]:last-child').addClass("last-child");$('[class="span"]').addClass("margin-left-20");$(':button[class="btn"], :reset[class="btn"], :submit[class="btn"], input[type="button"]').addClass("button-reset");$(":checkbox").addClass("input-checkbox");$('[class^="icon-"], [class=" icon-"]').addClass("icon-sprite");$(".pagination li:first-child a").addClass("pagination-first-child")}})
</script>

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap-ie.js"></script>

<script type="text/javascript" src="../../js/jquery.paging.js"></script>
<script type="text/javascript" src="../../js/knockout-2.0.0.js"></script>
<!-- <script type="text/javascript" src="../../js/Lib.js"></script> -->
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/util.js"></script>
<script type="text/javascript" src="editword.js"></script>

</body>
</html>