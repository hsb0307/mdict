<?php
require_once '../../lib/dao/RevisePackageDao.php';

$postData = array("StartDate"=>"", "EndDate"=>"", "OperationType"=>"", "CreatedBy"=>"", "PageSize"=>20, "RecordCount"=>0);
if(isset($_POST["PageSize"]))
{
	$postData["PageSize"] = $_POST["PageSize"];
}
if(isset($_POST["RecordCount"]))
{
	$postData["RecordCount"] = $_POST["RecordCount"];
}
$userId = '';
if(isset($_POST["UserId"]))
{
	$userId = $_POST["UserId"];
}
$roleId = -1;
if(isset($_POST["RoleId"]))
{
	$roleId = $_POST["RoleId"];
}

$where ='';
if(!empty($userId)){
	$where = ' AND e.UserId =' . $userId;
}
if(isset($_POST["Edit"])){
	$where.= ' AND e.Status < 3 ';
}
if($roleId > 82 && $roleId < 92 ){
	$where = ' AND e.Status = 0';
}

$currentPage = 1;
if(isset($_GET["page"]) == true){
	$currentPage = $_GET["page"];
}
$pageSize = $postData["PageSize"];
$startRowIndex = ($currentPage - 1) * $pageSize;

$orderBy = "  CreateDate ASC ";
$taskDao = new RevisePackageDao();
$count = $taskDao->GetCount($where);
$rows = $taskDao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
$array = array('Count'=>$count, 'Rows'=>$rows);
echo json_encode( $array);
?>