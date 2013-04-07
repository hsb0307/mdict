<?php
require_once '../../lib/dao/EditTaskDao.php';
require_once '../../lib/common/_common.php';

$result = array();
session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	echo json_encode($result);
	exit();	
}
$userId = $_SESSION["UserId"];

$editTaskDao = new EditTaskDao();
$dataPackages = $editTaskDao->GetDataPackageByUserId($userId);
echo json_encode($dataPackages);



?>