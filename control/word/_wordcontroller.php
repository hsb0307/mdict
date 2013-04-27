<?php

require_once '../../lib/dao/WordDao.php';

session_start();
/*
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}
*/
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	echo json_encode($result);
	die();
	exit();
}

$op = "";
if(isset($_GET["op"])) { $op = $_GET["op"]; }

if($op == "getword"){
	if(isset($_GET["id"])){
		$wordDao = new WordDao();
		$rowId = $_GET["id"];
		$row = $wordDao->GetById($rowId);
		echo json_encode($row);
	}else{
		echo json_encode("");
	}
	die();
	exit();
}
/*
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/dao/WordDao.php';
require_once '../../lib/dao/RevisePackageDao.php';
*/
/*
require_once '../../lib/dao/UserDao.php';
$userId = $_SESSION["UserId"];
$userDao = new UserDao();
$user = $userDao->GetById($userId);
*/
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/RevisePackageDao.php';

$word = array();
if($op == "edit" || $op == "create" || $op == "add" || $op == "delete" || $op == "existword" || $op == "update"){
if(isset($_POST['WordId'])) $word['WordId'] = $_POST['WordId'];
if(isset($_POST['Chinese'])) $word['Chinese'] = Encode($_POST['Chinese']);
if(isset($_POST['QueryCode'])) $word['QueryCode'] = Encode($_POST['QueryCode']);

if(isset($_POST['Pinyin'])) $word['Pinyin'] = Encode($_POST['Pinyin']);
if(isset($_POST['Mongolian'])) $word['Mongolian'] = Encode($_POST['Mongolian']);
if(isset($_POST['MongolianLatin'])) $word['MongolianLatin'] = Encode($_POST['MongolianLatin']);
if(isset($_POST['MongolianCyrillic'])) $word['MongolianCyrillic'] = Encode($_POST['MongolianCyrillic']);
if(isset($_POST['English'])) $word['English'] = Encode($_POST['English']);
if(isset($_POST['Japanese'])) $word['Japanese'] = Encode($_POST['Japanese']);
if(isset($_POST['Status'])) $word['Status'] = $_POST['Status'];
if(isset($_POST['PackageId'])) $word['PackageId'] = $_POST['PackageId'];
if(isset($_POST['SourceDictionary'])) $word['SourceDictionary'] = $_POST['SourceDictionary'];
if(isset($_POST['WordCategory'])) $word['WordCategory'] = $_POST['WordCategory'];
if(isset($_POST['PackageStatus'])) $word['PackageStatus'] = $_POST['PackageStatus'];
if(isset($_POST['LastModifiedBy'])) {
	$word['LastModifiedBy'] = $_POST['LastModifiedBy'];
} else {
	$word['LastModifiedBy'] = $_SESSION["UserId"];
}

}
if($op == "repetitive"){
	$wordCategory = $_POST['WordCategory'];
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
	
	$dao = new WordDao();
	$count = $dao->GetRepetitiveWordCount($wordCategory);
	$rows = $dao->GetRepetitiveWordPaged($wordCategory, $startRowIndex, $pageSize);
	$array = array('Count'=>$count, 'Rows'=>$rows);
	$firstRow = 0;
	if(isset($_GET["first"]) == true){
		$firstRow = 1;
	}
	if($firstRow == 1 &&  count($rows) > 0) {
		$wordDao = new WordDao();
		$first = $wordDao->GetById($rows[0]['WordId']);
		$array['First'] = $first;
	}
	echo json_encode( $array);
	die();
	exit();
}

if($op == "edit"){
	if(!empty($word["WordId"])){
		$wordDao = new RevisePackageDao();
		try {
			//$word['Status'] = 6; // 暂不改成编辑完成，但数据包确认时再改成 8;// 编辑完成
			$wordDao->UpdateWord($word);
			echo json_encode(array("success"=>true,"msg"=>"更新词条成功"));
		} catch (Exception $e) {
			echo json_encode(array("success"=>false,"msg"=>"更新词条失败"));
		}
	}else{
		echo json_encode(array("success"=>false,"msg"=>"无法获取词条"));
	}
}

if($op == "create"){
	//$word['Status'] = 8;// 编辑完成
	$dao = new RevisePackageDao();
	try {
		$dao->CreateWord($word);
		echo json_encode(array("success"=>true,"msg"=>"添加词条成功"));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"添加词条失败"));
	}
}

if($op == "add"){
	//$word['Status'] = 8;// 编辑完成
	$dao = new WordDao();
	try {
		if(isset($_POST['UserId'])) $word['UserId'] = $_POST['UserId'];
		$word['Status'] = 9; // 新增词条

		$word['OriginalCategory'] = $word['WordCategory'];
		//$word['SourceDictionary'] = $word['UserId'];
		$dao->Create($word);
		echo json_encode(array("success"=>true,"msg"=>"添加词条成功"));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"添加词条失败:".$e->getMessage()));
	}
}
if($op == "delete"){
	//$word['Status'] = 8;// 编辑完成
	$dao = new RevisePackageDao();
	try {
		if(isset($_POST['UserId'])) $word['UserId'] = $_POST['UserId'];
		$dao->DeleteWord($word['PackageId'], $word['WordId'], $word['Chinese']);
		echo json_encode(array("success"=>true,"msg"=>"删除词条成功"));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"删除词条失败:".$e->getMessage()));
	}
}

if($op == "nav"){
	//$word['Status'] = 8;// 编辑完成
	$packageId = $_POST["PackageId"];
	$currentPage = $_POST["CurrentPage"];
	$pageSize = 1;
	$startRowIndex = ($currentPage - 1) * $pageSize;
	$dao = new RevisePackageDao();
	try {
		$row = $dao->GetLastItem($packageId, $startRowIndex, $pageSize);
		echo json_encode(array("Row"=>$row,"startRowIndex"=>$startRowIndex));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"添加词条失败"));
	}
}

if($op == "jp"){
	$word = $_GET["q"];
	//url: "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=2ALMz6WqUEcsBg4BS91Eppq3&from=auto&to=auto" ,
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://openapi.baidu.com/public/2.0/bmt/translate?client_id=2ALMz6WqUEcsBg4BS91Eppq3&from=zh&to=jp&q='.$word);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
	$result=curl_exec($ch);
	curl_close($ch);
	echo $result;
	die();
	exit();
}
if($op == "en"){
	$word = $_GET["q"];
	//url: "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=2ALMz6WqUEcsBg4BS91Eppq3&from=auto&to=auto" ,
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://openapi.baidu.com/public/2.0/bmt/translate?client_id=2ALMz6WqUEcsBg4BS91Eppq3&from=zh&to=en&q='.$word);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出
	$result=curl_exec($ch);
	curl_close($ch);
	echo $result;
	die();
	exit();
}

if($op == "search"){
	$searchText = $_POST["SearchText"];
	$id = 0;
	if(isset($_POST["WordId"])) { 
		$id = $_POST["WordId"]; 
	}
	$wordDao = new WordDao();
	$row = $wordDao->GetByChinese($searchText, $id);
	echo json_encode($row);
	
	die();
	exit();
}

if($op == "refer"){
	$searchText = $_POST["SearchText"];
	$array = array('Refer'=>null, 'Existed'=>null);
	require_once '../../lib/dao/EnglishDao.php';
	$englishDao = new EnglishDao();
	$array['Refer'] = $englishDao->GetByChinese($searchText);
	/*
	switch ($searchText) {
		case '登山俱乐部' :
			$array['Refer'] = array(array('Chinese'=>$searchText, 'English'=>'Aloine Club'));
			break;
		case '业余篮球协会' :
			$array['Refer'] = array(array('Chinese'=>$searchText, 'English'=>'Amalteur basketball assciation'));
			break;
		case '美式' :
			$array['Refer'] = array(array('Chinese'=>$searchText, 'English'=>'American style'));
			break;
		case '笛卡儿坐标' :
			$array['Refer'] = array(array('Chinese'=>$searchText, 'English'=>'Cartesian coordinate'));
			break;
		case '计算机' :
			$array['Refer'] = array(array('Chinese'=>$searchText, 'English'=>'computer'));
			break;
		default:
			$array['Refer'] = '';
	}
	*/
	//$array = array(array('Chinese'=>$searchText, 'English'=>'ABC'));
	$wordDao = new WordDao();
	$array['Existed'] = $wordDao->GetByChinese($searchText);
	
	echo json_encode($array);

	die();
	exit();
}
//$op = "unassignedcount";
if($op == "unassignedcount"){
	$wordCategory = $_POST["WordCategory"];
	//$wordCategory = 19;
	$wordDao = new WordDao();
	$row = $wordDao->GetCountByWordCategory($wordCategory, 4);// 4:录入完成
	echo $row;// json_encode($row);
	die();
	exit();
}

if($op == "approvecount"){
	$wordCategory = $_POST["WordCategory"];
	$wordDao = new WordDao();
	$row = $wordDao->GetCountByWordCategory($wordCategory, 8, true);// 8:编辑完成
	echo $row;// json_encode($row);
	die();
	exit();
}

if($op == "autocomplete"){
	//$word['Status'] = 8;// 编辑完成
	try {

		$where ='';
		if(isset($_GET["term"])){
			//$where = ' WHERE LOWER(QueryCode) LIKE \'%' . $_GET["term"] . '%\' OR Chinese LIKE \'%'. $_GET["term"] . '%\'';
			$where = ' WHERE LOWER(QueryCode) LIKE \'' . $_GET["term"] . '%\' OR Chinese LIKE \''. $_GET["term"] . '%\'';
		}
		$currentPage = 1;
		$pageSize = 20;
		$startRowIndex = 0;

		$orderBy = " Chinese, CreatedDate DESC ";
		//require_once '../../lib/dao/UserWordDao.php';
		$dao = new WordDao();
		//$count = $dao->GetCount($where);
		$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
		$lines = array();
		foreach ($rows as $value) {
			$lines[] =  array('label'=>$value['Chinese'], 'value'=>$value['WordId'],
					'Mongolian'=>$value['Mongolian'],
					'MongolianLatin'=>$value['MongolianLatin'],
					'MongolianCyrillic'=>$value['MongolianCyrillic'],
					'WordCategory'=>$value['WordCategory']
			);
		}

		//$array = array('Count'=>$count, 'Rows'=>$rows);
		echo json_encode($lines); //$array
		//echo json_encode(array("success"=>true,"msg"=>"添加词条成功"));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"错误:".$e->getMessage()));
	}
	die();
	exit();
}
if($op == "existword"){
	$dao = new WordDao();
	try {
		if(isset($_POST['UserId'])) $word['UserId'] = $_POST['UserId'];
		$count = $dao->ExistWord($word['UserId'], $word['Chinese']);
		echo json_encode(array("success"=>true,"count"=>$count));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>$e->getMessage()));
	}
}


if($op == "mywords"){
	$postData = array("PageSize"=>20);
	if(isset($_POST["PageSize"]))
	{
		$postData["PageSize"] = $_POST["PageSize"];
	}
	$userId = "";
	if(isset($_POST["UserId"]))
	{
		$postData["UserId"] = $_POST["UserId"];
		$userId =$postData["UserId"];
	}
	$where =' WHERE Status < 999 ';
	if(!empty($userId)){
		$where .= ' AND SourceDictionary =' . $userId;
	}

	$currentPage = 1;
	if(isset($_GET["page"]) == true){
		$currentPage = $_GET["page"];
	}
	$pageSize = $postData["PageSize"];
	$startRowIndex = ($currentPage - 1) * $pageSize;

	$orderBy = "  CreatedDate DESC ";
	$dao = new WordDao();
	$count = $dao->GetCount($where);
	$rows = $dao->GetPaged($startRowIndex, $pageSize, $where, $orderBy);
	$array = array('Count'=>$count, 'Rows'=>$rows);
	echo json_encode( $array);

}
if($op == "mydelete"){
	$dao = new WordDao();
	try {
		$dao->Delete($_POST["WordId"]);
		echo json_encode(array("success"=>true,"msg"=>'成功删除词条'));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>$e->getMessage()));
	}
}
if($op == "update"){
	if(isset($_POST['UserId'])) $word['UserId'] = $_POST['UserId'];
	$word['Status'] = 0;
	$word['OriginalCategory'] = $word['WordCategory'];
	$dao = new WordDao();
	try {
		$dao->Update($word);
		echo json_encode(array("success"=>true,"msg"=>"添加词条成功"));
	} catch (Exception $e) {
		echo json_encode(array("success"=>false,"msg"=>"添加词条失败:".$e));
	}
}

if($op == "paged"){
	$pageSize = 30;
	if(isset($_POST["PageSize"])) {
		$pageSize =  $_POST["PageSize"];
	}
	$userId = "";
	if(isset($_POST["UserId"])){
		$userId =$_POST["UserId"];
	}
	$where =' AND i.Status <> 6 ';
	if(!empty($userId)){
		$where .= ' AND p.UserId =' . $userId;
	}

	$currentPage = 1;
	if(isset($_GET["page"]) == true){
		$currentPage = $_GET["page"];
	}
	$startRowIndex = ($currentPage - 1) * $pageSize;

	//$orderBy = "  CreatedDate DESC ";
	$dao = new RevisePackageDao();
	$count = $dao->GetItemsCount($where);
	$rows = $dao->GetItemsPaged($startRowIndex, $pageSize, $where);
	$array = array('Count'=>$count, 'Rows'=>$rows);
	echo json_encode( $array);
}

?>