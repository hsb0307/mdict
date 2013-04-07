<?php
require_once '../../lib/dao/LogDao.php';
//require_once '../../lib/dao/RevisePackageDao.php';

session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	echo json_encode($result);
	die();
	exit();
}
$userId = $_SESSION["UserId"];
$roleId = $_SESSION["RoleId"];
$op = '';
if(isset($_GET["op"])) { $op = $_GET["op"]; }
if($op == "paged"){
	$pageSize = 30;
	if(isset($_POST["PageSize"]))
	{
		$pageSize = $_POST["PageSize"];
	}
	/* $roleId = 91;
	if(isset($_POST["RoleId"]))
	{
		$roleId = $_POST["RoleId"];
	} */
	
	$where ='';
	switch ($roleId) {
		case 1 :
		case 2 :
		case 3 :
		case 71 :
			$where = ' AND l.UserId = ' . $userId;
			break;
		case 81:
		case 82:
		case 83:
		case 81:
			$where = ' AND l.UserId IN (SELECT UserId FROM Users WHERE RoleId = ' . $roleId . ' )';
			break;
		case 91:
			//$where = ' AND l.UserId IN (SELECT UserId FROM Users WHERE RoleId = ' . $roleId . ' )';
			break;
		default:
			$where = '';
	}
	//if(isset($_POST['Username']) && !empty($_POST['Username'])){$pageSize = $_POST['Username'];}
	if(isset($_POST['UserId']) && !empty($_POST['UserId'])){$where .= ' AND l.UserId = ' . $_POST['UserId'];}
	//  CategoryId":categoryId , "ModuleId":moduleId, "OperationId"
	if(isset($_POST['CategoryId']) && !empty($_POST['CategoryId']) && $_POST['CategoryId'] > 0){$where .= ' AND CategoryId ='.$_POST['CategoryId'];}
	if(isset($_POST['ModuleId']) && !empty($_POST['ModuleId']) && $_POST['ModuleId'] > 0){$where .= ' AND ModuleId ='.$_POST['ModuleId'];}
	if(isset($_POST['OperationId']) && !empty($_POST['OperationId']) && $_POST['OperationId'] > 0){$where .= ' AND OperationId ='.$_POST['OperationId'];}
	$orderBy = " l.CreateDate DESC ";
	$currentPage = 1;
	if(isset($_GET["page"]) == true){
		$currentPage = $_GET["page"];
	}
	$startRowIndex = ($currentPage - 1) * $pageSize;
	//setcookie("currentSQL", $where, time()+30, '/');
	$dao = new LogDao();
	$count = $dao->GetCount($where);
	$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
	$array = array('Count'=>$count, 'Rows'=>$rows);
	echo json_encode( $array);
	die();
	exit();
}