<?php

session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	echo json_encode($result);
	die();
	exit();
}

$op = 'revisesubmit';
if(isset($_GET["op"])) { $op = $_GET["op"]; }
if($op == "revisesubmit"){
	$packageId = $_POST["PackageId"];
	$packageName = $_POST["PackageName"];
	require_once '../../lib/dao/RevisePackageDao.php';
	$dao = new RevisePackageDao();
	try {
		$row = $dao->SubmitPackage($packageId, $packageName);
		echo json_encode($row);
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"提交数据包失败"));
	}
}
if($op == "revisereject"){
	$packageId = $_POST["PackageId"];
	$packageName = $_POST["PackageName"];
	require_once '../../lib/dao/RevisePackageDao.php';
	$dao = new RevisePackageDao();
	try {
		$row = $dao->RejectPackage($packageId, $packageName);
		echo json_encode($row);
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"撤销数据包失败"));
	}
}
if($op == "expire"){
	//session_start();
	$userId = $_SESSION["UserId"];
	$days = $_POST["Days"];
	require_once '../../lib/dao/RevisePackageDao.php';
	$dao = new RevisePackageDao();
	try {
		$count = $dao->GetExpireCount($userId, $days);
		echo $count;
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"撤销数据包失败"));
	}
}


?>