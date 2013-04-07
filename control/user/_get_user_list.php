<?php
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/common/_common.php';

$postData = array (
		"StartDate" => "",
		"EndDate" => "",
		'PageSize' => 5
);
if (isset ( $_POST ["StartDate"] )) {
	$postData ["StartDate"] = Encode ( $_POST ["StartDate"] );
}
if (isset ( $_POST ["EndDate"] )) {
	$postData ["EndDate"] = Encode ( $_POST ["EndDate"] );
}
if (isset ( $_POST ["PageSize"] )) {
	$postData ["PageSize"] = Encode ( $_POST ["PageSize"] );
}

$where = "";
if (! empty ( $postData ["StartDate"] )) {
	$where = " AND CREATEDATE >= TO_DATE('" . $postData ["StartDate"] . "','yyyy-mm-dd') ";
}
$EndDate = $postData ["EndDate"];
if (! empty ( $postData ["EndDate"] )) {
	$where = " AND CREATEDATE < '" . date ( "Y-m-d", strtotime ( "$EndDate +1 day" ) ) . "' ";
}
if (! empty ( $where )) {
	$where = " WHERE " . substr ( $where, 4 );
}
$orderBy = " IsApproved, CreateDate DESC ";
if (isset ( $_POST ["OrderBy"] )) {
	$orderBy = Encode ( $_POST ["OrderBy"] );
}

$currentPage = 1;
if(isset($_GET["page"]) == true){
	$currentPage = $_GET["page"];
}
$pageSize = $postData["PageSize"];
$startRowIndex = ($currentPage - 1) * $pageSize;

$dao = new UserDao ();
$count = $dao->GetCount( $where);
$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);

echo json_encode ( array('Count'=>$count, 'Rows'=>$rows) );
?>