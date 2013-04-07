<?php
require_once '../../lib/dao/EditWordDao.php';
require_once '../../lib/common/_common.php';

$postData = array("StartDate"=>"", "EndDate"=>"", "OperationType"=>"", "CreatedBy"=>"");
$dataPackageId = "";
if(isset($_POST["txtSearch"]))// == true
{
	$postData["txtSearch"] = Encode($_POST["txtSearch"]);//String2Array($_POST["txtSearch"]);
}
if(isset($_POST["StartDate"]) == true)
{
	$postData["StartDate"] = Encode($_POST["StartDate"]);
}
if(isset($_POST["EndDate"]) == true){
	$postData["EndDate"] = Encode($_POST["EndDate"]) ; //$_POST["Username"];
}
if(isset($_POST["OperationType"]) == true)
{
	$postData["OperationType"] = Encode($_POST["OperationType"]);
}
if(isset($_POST["CreatedBy"]) == true)
{
	$postData["CreatedBy"] = Encode($_POST["CreatedBy"]);
}

if(isset($_POST["DataPackageId"])){
	$dataPackageId = $_POST["DataPackageId"];
}

$txtSearch = $postData["txtSearch"];
$StartDate = $postData["StartDate"];
$EndDate   = $postData["EndDate"];
$OperationType = $postData["OperationType"];
$CreatedBy   = $postData["CreatedBy"];
$postDataString = Array2String($postData);

$where ="";
$whereCount = "";
$whereEdit = "";

if(!empty($dataPackageId) && $dataPackageId != "all"){
	$where = " AND editpackage.packageid = '" . $dataPackageId . "'";
}

if( !empty($txtSearch))
{
	$where = " AND Chinese like '%" . $txtSearch . "%'";
}


if( !empty($StartDate))
{
	$where = " AND dCreateDate >= '" . $StartDate . "'";
}
if(!empty($EndDate))
{
	$where = " AND dCreateDate < '" . date("Y-m-d",strtotime("$EndDate +1 day"))  . "'";
}
if(!empty($OperationType))
{
	$where = " AND sType = '" . $OperationType  . "'";
}
if(!empty($CreatedBy))
{
	//$where = " AND id_tbaseusers_new = " . $CreatedBy;
	// u.sName
	$where = " AND u.sName LIKE '%" . $CreatedBy . "%'";
}
if(!empty($where))
{
	$whereCount = " WHERE ".substr($where, 4);
	$whereEdit = " WHERE ".substr($where, 4)." and edititems.status = 1 ";
}else{
	$whereEdit = " WHERE edititems.status = 1";
}



//echo $where;

$editWordDao = new EditWordDao();
$rowCount = $editWordDao->GetCount($whereCount);
$editCount = $editWordDao->GetEditEntryCount($whereEdit);
echo json_encode(array("recordCount"=>$rowCount,"ecount"=>$editCount));
?>