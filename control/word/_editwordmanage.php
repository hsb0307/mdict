<?php
require_once '../../lib/dao/EditWordDao.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';

/*
 * 0未分配、1录入中、2录入完成、3编辑中、4编辑完成、5审核中、6审核完成、7公布、8已删除
 */

$result = array ();
session_start ();
if (! isset ( $_SESSION ["UserId"] )) {
	$result ["success"] = false;
	$result ["msg"] = "SESSION已过期";
	echo json_encode ( $result );
	exit ();
}
/* 用户信息 */
$userId = $_SESSION ["UserId"];
$currentPackageId = $_SESSION['CurrentpackageId'];
$userDao = new UserDao ();
$user = $userDao->GetById ( $userId );

$op = "";
$dataPackageId = "";
$days = "0";
$arrayEntry = array (
		"WordId" => "",
		"Chinese" => "",
		"Pinyin" => "",
		"Mongolian" => "",
		"MongolianLatin" => "",
		"MongolianCyrillic" => "",
		"English" => "",
		"Japanese" => "",
		"ChineseExampleSentence" => "",
		"MongolianExampleSentence" => "",
		"EnglishExampleSentence" => "",
		"JapaneseExampleSentence" => "",
		"ExamineGroup" => "",
		"OriginalCategory" => "",
		"WordCategory" => "",
		"SourceDictionary" => "",
		"Description" => "",
		"Status" => "0",
		"DataPackageId" => "" 
);
if (isset ( $_GET ["op"] )) {
	$op = $_GET ["op"];
}

if (isset ( $_POST ["WordId"] )) {
	$arrayEntry ["WordId"] = $_POST ["WordId"];
}
if (isset ( $_POST ["Chinese"] )) {
	$arrayEntry ["Chinese"] = Encode ( $_POST ["Chinese"] );
}
if (isset ( $_POST ["Pinyin"] )) {
	$arrayEntry ["Pinyin"] = Encode ( $_POST ["Pinyin"] );
}
if (isset ( $_POST ["Mongolian"] )) {
	$arrayEntry ["Mongolian"] = Encode ( $_POST ["Mongolian"] );
}
if (isset ( $_POST ["MongolianLatin"] )) {
	$arrayEntry ["MongolianLatin"] = Encode ( $_POST ["MongolianLatin"] );
}
if (isset ( $_POST ["MongolianCyrillic"] )) {
	$arrayEntry ["MongolianCyrillic"] = Encode ( $_POST ["MongolianCyrillic"] );
}
if (isset ( $_POST ["English"] )) {
	$arrayEntry ["English"] = Encode ( $_POST ["English"] );
}
if (isset ( $_POST ["Japanese"] )) {
	$arrayEntry ["Japanese"] = Encode ( $_POST ["Japanese"] );
}
if (isset ( $_POST ["ChineseExampleSentence"] )) {
	$arrayEntry ["ChineseExampleSentence"] = Encode ( $_POST ["ChineseExampleSentence"] );
}
if (isset ( $_POST ["MongolianExampleSentence"] )) {
	$arrayEntry ["MongolianExampleSentence"] = Encode ( $_POST ["MongolianExampleSentence"] );
}
if (isset ( $_POST ["EnglishExampleSentence"] )) {
	$arrayEntry ["EnglishExampleSentence"] = Encode ( $_POST ["EnglishExampleSentence"] );
}
if (isset ( $_POST ["JapaneseExampleSentence"] )) {
	$arrayEntry ["JapaneseExampleSentence"] = Encode ( $_POST ["JapaneseExampleSentence"] );
}
if (isset ( $_POST ["ExamineGroup"] )) {
	$arrayEntry ["ExamineGroup"] = Encode ( $_POST ["ExamineGroup"] );
}
if (isset ( $_POST ["OriginalCategory"] )) {
	$arrayEntry ["OriginalCategory"] = Encode ( $_POST ["OriginalCategory"] );
}
if (isset ( $_POST ["WordCategory"] )) {
	$arrayEntry ["WordCategory"] = Encode ( $_POST ["WordCategory"] );
}
if (isset ( $_POST ["SourceDictionary"] )) {
	$arrayEntry ["SourceDictionary"] = Encode ( $_POST ["SourceDictionary"] );
}
if (isset ( $_POST ["Description"] )) {
	$arrayEntry ["Description"] = Encode ( $_POST ["Description"] );
}
if (isset ( $_POST ["Status"] )) {
	$arrayEntry ["Status"] = Encode ( $_POST ["Status"] );
}
if (isset ( $_POST ["DataPackageId"] )) {
	$arrayEntry ["DataPackageId"] = $_POST ["DataPackageId"];
	$dataPackageId = $_POST ["DataPackageId"];
}

if (isset ( $_POST ["Days"] )) {	
	$days = $_POST ["Days"];
}

//设置当前数据包
if(!empty($dataPackageId)){
	if($currentPackageId != $dataPackageId){
		$_SESSION['CurrentpackageId'] = $dataPackageId;
	}
}

if ($op == "get") {
	if (! empty ( $arrayEntry ["WordId"] )) {
		$wordDao = new EditWordDao ();
		$entry = $wordDao->GetEntryById ( $arrayEntry ["WordId"] );
		echo json_encode ( $entry );
	} else {
		echo json_encode ( "" );
	}
}
if ($op == "get1") {
	if (isset ( $_GET ["id"] )) {
		$wordDao = new EditWordDao ();
		$rowId = $_GET ["id"];
		$row = $wordDao->GetById ( $rowId );
		echo json_encode ( $row );
	} else {
		echo json_encode ( "" );
	}
}

if ($op == "add") {
	try {
		$arrayEntry ["Status"] = "2"; // 状态录入中
		$wordDao = new EditWordDao ();
		$entryId = $wordDao->Create ( $arrayEntry );
		$arrayEntry ["WordId"] = $entryId;
		$entry = $wordDao->GetEntryById ( $arrayEntry ["WordId"] );
		$arrayEntry ["Chinese"] = $entry->Chinese;
		$arrayEntry ["English"] = $entry->English;
		$arrayEntry ["Mongolian"] = $entry->Mongolian;
		$arrayEntry ["MongolianLatin"] = $entry->MongolianLatin;
		$arrayEntry ["MongolianCyrillic"] = $entry->MongolianCyrillic;
		$arrayEntry ["Japanese"] = $entry->Japanese;		
		$where = " AND editpackage.packageid = '" . $dataPackageId . "'";
		$whereEdit = " AND editpackage.packageid = '" . $dataPackageId . "' and edititems.status = 2"; // 状态已处理
		$rowCount = $wordDao->GetCount ( $where );
		$editCount = $wordDao->GetEditEntryCount ( $whereEdit );
		$arrayEntry ["Count"] = $rowCount;
		$arrayEntry ["EditCount"] = $editCount;
		$arrayEntry ["success"] = true;
		echo json_encode ( $arrayEntry );
	} catch ( Exception $e ) {
		echo json_encode ( array (
				"success" => false,
				"msg" => $e->getMessage () 
		) );
	}
}

if ($op == "update") {
	try {
		if (! empty ( $arrayEntry ["WordId"] )) {
			$arrayEntry ["Status"] = "2"; // 状态录入中
			$wordDao = new EditWordDao ();
			$wordDao->Update ( $arrayEntry );
			$entry = $wordDao->GetEntryById ( $arrayEntry ["WordId"] );
			$arrayEntry ["Chinese"] = $entry->Chinese;
			$arrayEntry ["English"] = $entry->English;
			$arrayEntry ["Mongolian"] = $entry->Mongolian;
			$arrayEntry ["MongolianLatin"] = $entry->MongolianLatin;
			$arrayEntry ["MongolianCyrillic"] = $entry->MongolianCyrillic;
			$arrayEntry ["Japanese"] = $entry->Japanese;			
			$where = " AND editpackage.packageid = '" . $dataPackageId . "'";
			$whereEdit = " AND editpackage.packageid = '" . $dataPackageId . "' and edititems.status = 2"; // 状态已处理
			$rowCount = $wordDao->GetCount ( $where );
			$editCount = $wordDao->GetEditEntryCount ( $whereEdit );
			$arrayEntry ["Count"] = $rowCount;
			$arrayEntry ["EditCount"] = $editCount;
			$arrayEntry ["success"] = true;
			echo json_encode ( $arrayEntry );
		} else {
			echo json_encode ( array (
					"success" => false,
					"msg" => "无法获取词条" 
			) );
		}
	} catch ( Exception $e ) {
		echo json_encode ( array (
				"success" => false,
				"msg" => $e->getMessage () 
		) );
	}
}

if ($op == "delete") {
	if (! empty ( $arrayEntry ["WordId"] )) {
		$wordDao = new EditWordDao ();
		$entry = $wordDao->DeleteEntryById ( $arrayEntry ["WordId"] );
		echo json_encode ( array (
				"success" => true,
				"msg" => "操作成功" 
		) );
	} else {
		echo json_encode ( array (
				"success" => false,
				"msg" => "无法获取词条" 
		) );
	}
}

if ($op == "top") {
	$wordDao = new EditWordDao ();
	$entrys = $wordDao->GetEntryTop10 ( $userId );
	echo json_encode ( $entrys );
}

//取超期数据包数
if($op=="expire"){
   $where = " AND userid = ".$userId." AND Status = 0 AND (SysDate - CreateDate >  $days )";
   $editTaskDao = new EditTaskDao();
   $count = $editTaskDao->GetCount($where);
   echo json_encode ( array("count"=>$count) );
}

?>