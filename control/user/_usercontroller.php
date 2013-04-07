<?php
require_once '../../lib/dao/UserDao.php';
//require_once '../../lib/dao/RevisePackageDao.php';


session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "您已经退出登录状态了！请重新登录到系统。";
	echo json_encode($result);
	die();
	exit();
}
$op = '';
if(isset($_GET["op"])) { $op = $_GET["op"]; }
if($op == "exist"){
	$dao = new UserDao();
	$username = $_POST["Username"];
	$count = $dao->IsUserExist($username);
	echo json_encode($result);
	die();
	exit();
}

if($op == "delete"){
	$userId = $_POST["UserId"];
	$roleId = $_POST["RoleId"];
	$dao = new UserDao();
	$count = $dao->HasPackageExist($userId, $roleId);
	if($count > 0 ){
		echo 0;
	} else {
		//echo json_encode(0);
		try{
			$dao->Delete($userId);
			echo 1;
		} catch (Exception $e) {
			echo $e;
		}
		
	}
	die();
	exit();
}
if($op == "approval"){
	$userId = $_POST["UserId"];
	$dao = new UserDao();
	try{
		$dao->Approval($userId);
		echo 1;
	} catch (Exception $e) {
		echo $e;
	}
	die();
	exit();
	//
}
if($op == "exist"){
	
}
if($op == "exist"){
	
}


?>