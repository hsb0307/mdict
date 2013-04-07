<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
$userId = $_SESSION["UserId"];
$roleId = $_SESSION["RoleId"];
require_once '../../lib/dao/EditTaskDao.php';
require_once '../../lib/dao/UserDao.php';

$op = "";
$packageId = "";
$category = "";
$packageName = "";
$applyCount = "";
if(isset($_GET["op"])){
	$op = $_GET["op"];
}

if(isset($_POST["packageId"])){
	$packageId = $_POST["packageId"];
}

if(isset($_POST["category"])){
	$category = $_POST["category"];
}

if(isset($_POST["packageName"])){
	$packageName = $_POST["packageName"];
}

if(isset($_POST["applyCount"])){
	$applyCount = $_POST["applyCount"];
}

if($op == "create"){//创建数据包
	$editTaskDao = new EditTaskDao();
	//判断不能申请三个以上的数据包
	$where = " and userid = '".$userId."' and status = 0 ";
	$count = $editTaskDao->GetCount($where);
	if($count > 300){//3
		$result["success"] = false;
		$result["msg"] = "没有提交的数据包大于3个,不能申请";
	}else{
		if(empty($applyCount)){$applyCount = "500";}
		$result1 = $editTaskDao->Create($userId,$category,$packageName,$applyCount);
		if($result1 == 1){
			$userDao = new UserDao();
			$_SESSION['CurrentpackageId'] = $userDao->CurrentEditPackage($userId, $roleId);
			$result["success"] = true;
			$result["msg"] = "操作成功";
		}else{
			$result["success"] = false;
			$result["msg"] = "没有可用词条";
		}
			
		
	}
	
	/*
	echo "<script>";
	//echo "alert(\"操作成功\"); ";
	echo "window.location.href = \"myeditpackage.php\" ";
	echo "</script>";
	exit();
	*/
	
	echo json_encode($result);
}

if($op == "update"){
	if(empty($packageId)){
		$result["success"] = false;
		$result["msg"] = "无法获取数据包";
	}else{
		$editTaskDao = new EditTaskDao();
		$editTaskDao->Update($packageId,$packageName);
		$userDao = new UserDao();
		$_SESSION['CurrentpackageId'] = $userDao->CurrentEditPackage($userId, $roleId);
		$result["success"] = true;
		$result["msg"] = "操作成功";
	}
	echo json_encode($result);
}

if($op == "cancel"){
	if(empty($packageId)){
		$result["success"] = false;
		$result["msg"] = "无法获取数据包";
	}else{
		$editTaskDao = new EditTaskDao();
		$editTaskDao->Cancel($packageId,$packageName);
		$userDao = new UserDao();
		$_SESSION['CurrentpackageId'] = $userDao->CurrentEditPackage($userId, $roleId);
		$result["success"] = true;
		$result["msg"] = "操作成功";
	}
	echo json_encode($result);
}


if($op == "wordcount"){
	$editTaskDao = new EditTaskDao();
	$count = $editTaskDao->GetWordCount($category);
	$result["wordcount"] = $count == null ? 0:$count;
	echo json_encode($result);	
}


/*
if($op == "info"){
	$result["success"] = true;
	$result["msg"] = "用户名:test";
	echo json_encode($result);
}


*/



?>

