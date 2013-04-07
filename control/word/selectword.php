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
<title>词条检索</title>
<?php include("../../css.php"); ?>
<link href="../../css/jquery/theme/ui-lightness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }
#toolbar {margin-top:6px;}
#txtSearch{width:220px;}

.source{font-family:' \5b8b\4f53';font-size: 14px; margin:4px 0 0 20px; letter-spacing:2px;  word-spacing:4px;}


</style>
</head>

<body>
	<?php include("../../header.php"); ?>
	<div id="toolbar" class="container">
		<div class="row">
			<div class="span12">
				<div>
				<!-- 数据包：<select id="packages"></select>  -->
					中文词条:<input type="text" id="txtSearch" value=""> <a id="btnSearch" href="javascript:void(0)"><i class="icon-search"></i></a>&nbsp;&nbsp;
					<a id="btnClear" href="javascript:void(0)"><i class="icon-remove"></i></a>
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="container">
		<div class="row">
			<div id="words" class=" span12 mongolian ">
			
			</div>
		</div>
	</div>

<?php include("../../footer.php"); ?>

<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.0.custom.min.js"></script>

<script type="text/javascript">
$(function () {
	function searchWord(word){
		var postData = {"SearchText":document.getElementById("txtSearch").value};
		jQuery.post("_wordcontroller.php?op=search" , postData, function (data) {
			var rows = eval("(" + data + ")");
			var html ='';
			for (var i = 0; i < rows.length; i++) {
				html += "<div class='well' style='margin:10px;'><div class='word'>" + (rows[i].Mongolian ? ($.trim(rows[i].Mongolian).length == 0 ? latinToMongolian(rows[i].MongolianLatin) : rows[i].Mongolian ) : latinToMongolian(rows[i].MongolianLatin)) + "</div>";
				html += "<div class='source'>" +  enumerableData.getName(rows[i].SourceDictionary?rows[i].SourceDictionary:"", enumerableData.sourceDictionary, true) + "</div></div>"
			}
			var tableBody = $("#words");
		    tableBody.html();
		    tableBody.html(html);
		});
	}
	var word = decodeURIComponent(getQueryStringByName("w"));
	word = decodeURIComponent(word);// decodeURIComponent和decodeURI
	if(word && word.length > 0){
		$("#txtSearch").val(word);
		searchWord(word);
	}
	$("#btnSearch").click(function() {
		var word = document.getElementById("txtSearch").value;
		if(word.length == 0){
			return false;
		} else {
			searchWord(word);
		}
		
	});
	$("#btnClear").click(function() {
		//document.getElementById("txtSearch").value = "";
		$( "#txtSearch" ).val("").focus();
	});

	$(document).keyup(function(event){
		  if(event.keyCode ==13){
			$("#txtSearch").autocomplete( "close" );
		    $("#btnSearch").trigger("click");
		  }
	});
	$( "#txtSearch" ).autocomplete({
        source: "_wordcontroller.php?op=autocomplete",
        minLength: 2,
        select: function( event, ui ) {
        	$( "#txtSearch" ).val(ui.item.label);
        	event.preventDefault();

        	searchWord(ui.item.label);
        }, // end select
        //close: function( event, ui ) {$( "#Chinese" ).val(ui.item.label);}
        focus: function( event, ui ) {
            //console.log(ui.item.label);
        	$( "#txtSearch" ).val(ui.item.label);
        	event.preventDefault();
        }
    
    });
});
</script>
</body>
</html>