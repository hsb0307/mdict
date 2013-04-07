<?php
require_once '../../lib/dao/RevisePackageDao.php';
require_once '../../lib/dao/WordDao.php';

$postData = array("StartDate"=>"", "EndDate"=>"", "OperationType"=>"", "CreatedBy"=>"", "PageSize"=>20, "RecordCount"=>0);
if(isset($_POST["PageSize"]))
{
	$postData["PageSize"] = $_POST["PageSize"];
}
if(isset($_POST["RecordCount"]))
{
	$postData["RecordCount"] = $_POST["RecordCount"];
}
//$userId = '167';
if(isset($_POST["UserId"]))
{
	$userId = $_POST["UserId"];
}

$packageId = '1';
if(isset($_POST["PackageId"]))
{
	$packageId = $_POST["PackageId"];
}

$where ='';
//if(!empty($userId)){
	//$where = ' AND u.UserId =' . $userId;
//}

if(isset($_POST["Filter"])){
	$filter = $_POST["Filter"];
	if(!empty($filter)) {
		$where =str_replace('@Filter', $filter, ' AND ( d.Chinese LIKE \'%@Filter%\' OR d.Pinyin LIKE \'%@Filter%\' OR d.Mongolian  LIKE \'%@Filter%\' OR d.MongolianLatin LIKE \'%@Filter%\' OR d.MongolianCyrillic LIKE \'%@Filter%\' OR d.English LIKE \'%@Filter%\' OR d.Japanese LIKE \'%@Filter%\' )');
	}
	//$where = ' AND u.UserId =' . $userId;
}
$currentPage = 1;
if(isset($_GET["page"]) == true){
	$currentPage = $_GET["page"];
}
//PackageId=1&UserId=167&PageSize=20
$pageSize =$postData["PageSize"];
$pageSize = 20;
$startRowIndex = ($currentPage - 1) * $pageSize;

$orderBy = "  CreateDate ASC ";

$taskDao = new RevisePackageDao();
$count = $taskDao->GetCountByPackageId($packageId, $where);
$rows = $taskDao->GetPagedByPackageId($packageId, $startRowIndex, $pageSize, $where, '');
$countRevised = $taskDao->GetCountRevised($packageId);
$total = $count;
if(!empty($where)) {
	$total = $taskDao->GetTotalByPackageId($packageId);
}
$array = array('Count'=>$count, 'Rows'=>$rows, 'CountHandled'=>$countRevised, 'Total'=>$total);
$firstRow = 0;
if(isset($_GET["first"]) == true){
	$firstRow = 1;
}
if($firstRow == 1 &&  count($array['Rows']) > 0) {
	$wordDao = new WordDao();
	$first = $wordDao->GetById($array['Rows'][0]['WordId']);
	
// 	$pinyin = trim($first->Pinyin);
// 	if(empty($pinyin) ){
// 		require_once '../../lib/common/_pinyin1.php';
// 		$first->Pinyin = Pinyin($first->Chinese,0,1);
// 	}
	
	$array['First'] = $first;
}
echo json_encode( $array);
?>