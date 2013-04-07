<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
$userId = $_SESSION["UserId"];

require_once '../../lib/dao/ApprovePackageDao.php';
require_once '../../lib/common/_common.php';

$categoryId = 1;
$packageName = "";
$itemCount = 500;

// $categoryId = 10;
// $packageName = '新闻学3条';
// $itemCount = 3;

if(isset($_POST["categoryId"]))
{
	$categoryId = $_POST["categoryId"];
}
if(isset($_POST["packageName"]))
{
	$packageName = Encode($_POST["packageName"]);
}
if(isset($_POST["itemCount"]))
{
	$itemCount = $_POST["itemCount"];
}
$taskDao = new ApprovePackageDao();
$flag = $taskDao->Create($userId, $categoryId, $packageName, $itemCount);
if($flag > 0) {
	setcookie("currentPackageId", $flag, time()+3600*24*30, '/');
}
echo $flag;
?>