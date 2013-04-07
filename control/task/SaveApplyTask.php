<?php
require_once '../../lib/dao/TaskDao.php';
require_once '../../lib/dao/EditTaskDao.php';

$op="";
if(isset($_POST["op"])){
	$op = $_POST["op"];
}
$op = "apply";
if($op == "apply"){
	try {
		$result = array();
		session_start ();
		$userId = 101;//$_SESSION["UserId"];
		//$userId = '101';
		
		if(!isset($userId)){
			$result["success"] = false;
			$result["msg"] = "SESSION已过期";
		}else{
			$editTaskDao = new EditTaskDao();
			$editTaskDao->Create($userId);
			$result["success"] = true;
			$result["msg"] = "操作成功";
			/*
			$taskDao = new TaskDao();
			$taskDao->CreateDataPackByUserId($userId);
			$result["success"] = true;
			$result["msg"] = "操作成功";
			*/
		}
		echo json_encode($result);
	} catch (Exception $e) {
		$result["success"] = false;
		$result["msg"] = $e->getMessage();
		echo json_encode($result);
	}
}
?>