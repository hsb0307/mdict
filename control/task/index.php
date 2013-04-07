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
<title>编辑数据包管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {position: relative; }
#userinfo ul {position: absolute; top:30px; padding-right:40px; }

</style>
</head>

<body>
<?php include("../../header.php"); ?>
<div class="container">
<div class="row">
<div class="span4">
<ul>
<li><a href='editpackage.php' target="_blank" >录入数据包管理</a> </li>
<li><a href='revisepackage.php' target="_blank" >编辑数据包管理</a> </li>
<li><a href='approvepackage.php' target="_blank" >审定数据包管理</a> </li>
</ul>

</div>
</div>
</div>

<?php include("../../footer.php"); ?>

<script type="text/javascript">
$(function () {
	
});
</script>
</body>
</html>