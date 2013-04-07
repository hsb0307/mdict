<?php
// _gbcontroller.php
require_once '../../lib/dao/TopicDao.php';
//require_once '../../lib/dao/RevisePackageDao.php';
require_once '../../lib/common/_common.php';

session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	//echo json_encode($result);
	echo '<script> location = "../../login.htm";</script>';
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
			$where = ' AND t.UserId = ' . $userId;
			break;
		case 81:
		case 82:
		case 83:
		case 81:
			$where = ' AND t.UserId IN (SELECT UserId FROM Users WHERE RoleId = ' . $roleId . ' )';
			break;
		default:
			$where = '';
	}
	$currentPage = 1;
	if(isset($_GET["page"]) == true){
		$currentPage = $_GET["page"];
	}
	$startRowIndex = ($currentPage - 1) * $pageSize;

	$orderBy = " t.DateCreated DESC ";
	$dao = new TopicDao();
	$count = $dao->GetCount($where);
	$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
	$array = array('Count'=>$count, 'Rows'=>$rows);
	echo json_encode( $array);
	die();
	exit();
}

if($op == 'createtopic'){
	$array = array('UserId'=>$userId, 'Title'=>Encode($_POST["Title"]), 'FullText'=>'');
	//if(isset($_POST["Title"])){$title = $_POST["Title"];}
	if(isset($_POST["FullText"])){$array ['FullText'] = Encode($_POST["FullText"]);}
	$dao = new TopicDao();
	$result = $dao->Create($array);
	echo json_encode($result);
	die();
	exit();
}
if($op == 'posts'){
	$topicId = $_POST["TopicId"];
	$pageSize = 30;
	if(isset($_POST["PageSize"])){$pageSize = $_POST["PageSize"];}
	$where ='';
	$currentPage = 1;
	if(isset($_GET["page"]) == true){$currentPage = $_GET["page"];}
	$startRowIndex = ($currentPage - 1) * $pageSize;
	$orderBy = " p.DateCreated DESC ";
	$dao = new TopicDao();
	$array = $dao->GetPostPaged($topicId, $startRowIndex, $pageSize, $where, $orderBy);
	echo json_encode( $array);
	die();
	exit();
}
if($op == 'createpost'){
	$array = array('TopicId'=>$_POST['TopicId'], 'UserId'=>$userId, 'Title'=>$_POST["Title"], 'FullText'=>'');
	$title = Encode($_POST["Title"]);
	//if(isset($_POST["Title"])){$title = $_POST["Title"];}
	if(isset($_POST["FullText"])){$array ['FullText'] = Encode($_POST["FullText"]);}
	$dao = new TopicDao();
	$result = $dao->CreatePost($array);
	echo json_encode($result);
	die();
	exit();
}
?>