<?php
require_once '../../lib/dao/WordDao.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';

/*
 * 0未分配、1录入中、2录入完成、3编辑中、4编辑完成、5审核中、6审核完成、7公布、8已删除
 * */


$result = array();
session_start();
if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "SESSION已过期";
	echo json_encode($result);
	exit();
}
/*用户信息*/
$userId = $_SESSION["UserId"];
$userDao = new UserDao();
$user = $userDao->GetById($userId);

$op = "";
$arrayEntry = array("WordId"=>"","Chinese"=>"","Pinyin"=>"","Mongolian"=>"","MongolianLatin"=>"","MongolianCyrillic"=>"",
		"English"=>"","Japanese"=>"","ChineseExampleSentence"=>"","MongolianExampleSentence"=>"","EnglishExampleSentence"=>"",
		"JapaneseExampleSentence"=>"","ExamineGroup"=>"","OriginalCategory"=>"","WordCategory"=>"","SourceDictionary"=>"","Description"=>"","Status"=>"0","DataPackageId"=>"");
if(isset($_GET["op"])){
	$op = $_GET["op"];
}

if(isset($_POST["WordId"])){
	$arrayEntry["WordId"] = $_POST["WordId"];
}
if(isset($_POST["Chinese"])){
	$arrayEntry["Chinese"] = $_POST["Chinese"];
}
if(isset($_POST["Pinyin"])){
	$arrayEntry["Pinyin"] = $_POST["Pinyin"];
}
if(isset($_POST["Mongolian"])){
	$arrayEntry["Mongolian"] = $_POST["Mongolian"];
}
if(isset($_POST["MongolianLatin"])){
	$arrayEntry["MongolianLatin"] = $_POST["MongolianLatin"];
}
if(isset($_POST["MongolianCyrillic"])){
	$arrayEntry["MongolianCyrillic"] = $_POST["MongolianCyrillic"];
}
if(isset($_POST["English"])){
	$arrayEntry["English"] = $_POST["English"];
}
if(isset($_POST["Japanese"])){
	$arrayEntry["Japanese"] = $_POST["Japanese"];
}
if(isset($_POST["ChineseExampleSentence"])){
	$arrayEntry["ChineseExampleSentence"] = $_POST["ChineseExampleSentence"];
}
if(isset($_POST["MongolianExampleSentence"])){
	$arrayEntry["MongolianExampleSentence"] = $_POST["MongolianExampleSentence"];
}
if(isset($_POST["EnglishExampleSentence"])){
	$arrayEntry["EnglishExampleSentence"] = $_POST["EnglishExampleSentence"];
}
if(isset($_POST["JapaneseExampleSentence"])){
	$arrayEntry["JapaneseExampleSentence"] = $_POST["JapaneseExampleSentence"];
}
if(isset($_POST["ExamineGroup"])){
	$arrayEntry["ExamineGroup"] = $_POST["ExamineGroup"];
}
if(isset($_POST["OriginalCategory"])){
	$arrayEntry["OriginalCategory"] = $_POST["OriginalCategory"];
}
if(isset($_POST["WordCategory"])){
	$arrayEntry["WordCategory"] = $_POST["WordCategory"];
}
if(isset($_POST["SourceDictionary"])){
	$arrayEntry["SourceDictionary"] = $_POST["SourceDictionary"];
}
if(isset($_POST["Description"])){
	$arrayEntry["Description"] = $_POST["Description"];
}
if(isset($_POST["Status"])){
	$arrayEntry["Status"] = $_POST["Status"];
}
if(isset($_POST["DataPackageId"])){
	$arrayEntry["DataPackageId"] = $_POST["DataPackageId"];
}

if($op=="get"){	
	if(!empty($arrayEntry["WordId"])){
		$wordDao = new WordDao();		
		$entry = $wordDao->GetEntryById($arrayEntry["WordId"]);
		echo json_encode($entry);
	}else{
		echo json_encode("");
	}	
}
if($op=="get1"){
	if(isset($_GET["id"])){
		$wordDao = new WordDao();
		$rowId = $_GET["id"];
		$row = $wordDao->GetById($rowId);
		echo json_encode($row);
	}else{
		echo json_encode("");
	}
}

if($op == "add"){
	/*根据用户权限设置新增词条状态*/
	if($user->RoleId == 1){
		$arrayEntry["Status"] = "1";//录入中
	}elseif ($user->RoleId == 2){
		$arrayEntry["Status"] = "3";//编辑中
	}elseif ($user->RoleId == 3){
		$arrayEntry["Status"] = "5";//审核中
	}
	
	$wordDao = new WordDao();
	$entryId = $wordDao->AddEntry($arrayEntry);
	$arrayEntry["WordId"] = $entryId;
	echo json_encode($arrayEntry);	
}

if($op == "update"){		
	if(!empty($arrayEntry["WordId"])){
		$wordDao = new WordDao();
		$entry = $wordDao->UpdateEntryById($arrayEntry);
		echo json_encode($arrayEntry);
	}else{		
		echo json_encode(array("success"=>false,"msg"=>"无法获取词条"));
	}	
}

if($op=="delete"){
	if(!empty($arrayEntry["WordId"])){
		$wordDao = new WordDao();
		$entry = $wordDao->DeleteEntryById($arrayEntry["WordId"]);
		echo json_encode(array("success"=>true,"msg"=>"操作成功"));
	}else{
		echo json_encode(array("success"=>false,"msg"=>"无法获取词条"));
	}
}

if($op=="top"){	
	$wordDao = new WordDao();
	$entrys = $wordDao->GetEntryTop10($userId);
	echo json_encode($entrys);	
}




?>