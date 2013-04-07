<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
$userId = $_SESSION["UserId"];

require_once '../../lib/dao/RevisePackageDao.php';
require_once '../../lib/common/_common.php';

$categoryId = 1;
if(isset($_POST["categoryId"]))
{
	$categoryId = $_POST["categoryId"];
}
$packageName = "";
if(isset($_POST["packageName"]))
{
	$packageName = Encode($_POST["packageName"]);
}
$itemCount = 500;
if(isset($_POST["itemCount"]))
{
	$itemCount = $_POST["itemCount"];
}
 
$taskDao = new RevisePackageDao();
$flag = $taskDao->Create($userId, $categoryId, $packageName, $itemCount);
if($flag > 0) {
	setcookie("currentPackageId", $flag, time()+3600*24*30, '/');
}
echo $flag;
?>