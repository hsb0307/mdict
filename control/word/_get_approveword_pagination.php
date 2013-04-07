<?php
require_once '../../lib/dao/ApprovePackageDao.php';
require_once '../../lib/dao/WordDao.php';

$where ='';
$userId = '';
$roleId = -1;
$packageId = '1';
if(isset($_POST["UserId"]))
{
	$userId = $_POST["UserId"];
}
if(isset($_POST["PackageId"]))
{
	$packageId = $_POST["PackageId"];
}

if(isset($_POST["RoleId"]))
{
	$roleId = $_POST["RoleId"];
}

if(isset($_POST["Filter"])){
	$filter = $_POST["Filter"];
	if(!empty($filter)) {
		$where =str_replace('@Filter', $filter, ' AND ( d.Chinese LIKE \'%@Filter%\' OR d.Pinyin LIKE \'%@Filter%\' OR d.Mongolian  LIKE \'%@Filter%\' OR d.MongolianLatin LIKE \'%@Filter%\' OR d.MongolianCyrillic LIKE \'%@Filter%\' OR d.English LIKE \'%@Filter%\' OR d.Japanese LIKE \'%@Filter%\' )');
	}
}

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
$orderBy = "  CreateDate DESC ";

$dao = new ApprovePackageDao();
$count = $dao->GetItemCount($packageId, $where);
$rows = $dao->GetItemPaged($packageId, $startRowIndex, $pageSize, $where, '');
$countRevised = $dao->GetCountApproved($packageId);
$total = $count; // 有过滤条件时，也确保数据包的总条数。
if(!empty($where)) {
	$total = $dao->GetItemCount($packageId, '');
}
$array = array('Count'=>$count, 'Rows'=>$rows, 'CountHandled'=>$countRevised, 'Total'=>$total);
$firstRow = 0;
if(isset($_GET["first"]) == true){
	$firstRow = 1;
}
if($firstRow == 1 &&  count($array['Rows']) > 0) {
	$wordDao = new WordDao();
	$first = $wordDao->GetById($array['Rows'][0]['WordId']);
	$array['First'] = $first;
}
echo json_encode( $array);
?>