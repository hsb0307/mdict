<?php
require_once '../../lib/dao/ApprovePackageDao.php';

$where ='';
$userId = '';
$roleId = -1;

$userId = 101;
$_POST["Edit"] = 0;

if(isset($_POST["UserId"]))
{
	$userId = $_POST["UserId"];
}
if(isset($_POST["RoleId"]))
{
	$roleId = $_POST["RoleId"];
}
if(!empty($userId)){
	$where = ' AND e.UserId =' . $userId;
}
if(isset($_POST["Edit"])){
	$where.= ' AND e.Status < 3 ';
}
if($roleId > 82 && $roleId < 92 ){
	$where = ' AND e.Status = 0';
}
$orderBy = "CreateDate DESC ";

$pageSize = 30;
if(isset($_POST["PageSize"]))
{
	$pageSize = $_POST["PageSize"];
}
$currentPage = 1;
if(isset($_GET["page"]) == true){
	$currentPage = $_GET["page"];
}
$startRowIndex = ($currentPage - 1) * $pageSize;

$dao = new ApprovePackageDao();
$count = $dao->GetCount($where);
$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
$array = array('Count'=>$count, 'Rows'=>$rows);
echo json_encode( $array);
?>