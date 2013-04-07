<?php
require_once '../../lib/dao/EditTaskDao.php';

$postData = array("StartDate"=>"", "EndDate"=>"", "OperationType"=>"", "CreatedBy"=>"", "PageSize"=>20);
$userId = "";
$orderBy = "";
$isAsc = "true";

if(isset($_POST["PageSize"]))
{
	$postData["PageSize"] = $_POST["PageSize"];
}
if(isset($_POST["UserId"]))
{
	$postData["UserId"] = $_POST["UserId"];
	$userId =$postData["UserId"];
}
if(isset($_POST["orderBy"]))
{
	$orderBy = $_POST["orderBy"];
}
if(isset($_POST["isAsc"]))
{
	$isAsc = $_POST["isAsc"];
}


$where ="";
$currentPage = 1;
if(isset($_GET["page"]) == true){
	$currentPage = $_GET["page"];
}
$pageSize =$postData["PageSize"];

$startRowIndex = ($currentPage - 1) * $pageSize;

if(!empty($userId)){
	$where = " and u.userid = ".$userId;
}

//$orderBy = "  CreateDate ASC ";
if($isAsc == "true"){
	$orderBy = $orderBy." ASC";	
}else{
	$orderBy = $orderBy." DESC";
}

$taskDao = new EditTaskDao();
$count = $taskDao->GetCount($where);
$rows = $taskDao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
$array = array("Count"=>$count,"Rows"=>$rows);
echo json_encode( $array);

?>