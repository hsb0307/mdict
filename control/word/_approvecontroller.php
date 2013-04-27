<?php

require_once '../../lib/dao/ApproveWordDao.php';
require_once '../../lib/dao/ApprovePackageDao.php';

session_start();

 if(!isset($_SESSION["UserId"]))
 {
header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
die();
exit();
}

if(!isset($_SESSION["UserId"])){
	$result["success"] = false;
	$result["msg"] = "当前连接已过期，请重新登录。";
	echo json_encode($result);
	die();
	exit();
}

$action = "";
if(isset($_GET["action"])) { $action = $_GET["action"]; }
$word = array();
if($action == 'approve' || $action == "edit" || $action == "create" || $action == "add" || $action == "delete" || $action == "existword" || $action == "update"){
	if(isset($_POST['OriginalId'])) $word['OriginalId'] = $_POST['OriginalId']; 
	if(isset($_POST['WordId'])) $word['WordId'] = $_POST['WordId']; 
	if(isset($_POST['WordIds'])) $word['WordIds'] = $_POST['WordIds'];
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
	if(isset($_POST['Russian'])) { $word['Russian'] = Encode($_POST['Russian']);} else {$word['Russian'] = ''; }
	if(isset($_POST['ExamineGroup'])) {$word['ExamineGroup'] = $_POST['ExamineGroup'];} else {$word['ExamineGroup'] = 0;}
	if(isset($_POST['Description'])) {$word['Description'] = $_POST['Description'];} else {$word['Description'] = '';}
	if(isset($_POST['CreatedBy'])) {$word['CreatedBy'] = $_POST['CreatedBy'];} else {$word['CreatedBy'] = $_SESSION["UserId"];}
	
}

/*
$word['OriginalId'] = 6265;
$word['WordIds']='6265,6266';
$word['QueryCode'] = '';
$word['Pinyin'] = '';
$word['Chinese']='一致性测试';
$word['Mongolian'] = '';
$word['MongolianCyrillic'] = '';
$word['Japanese'] = '';
$word['MongolianLatin']='NIHEDUMEL TeSt';
$word['English'] ='conformancetesting';
$word['SourceDictionary']=4;
$word['WordCategory']=4;
$word['PackageStatus']=0;
$word['PackageId']=2;
*/
if($action == "approve"){
	if(!empty($word["OriginalId"])){
		$dao = new ApprovePackageDao();
		try {
			$dao->UpdateWord($word);
			echo json_encode(array("success"=>true,"msg"=>"更新词条成功"));
		} catch (Exception $e) {
			echo json_encode(array("success"=>false,"msg"=>"更新词条失败"));//
		}
	}else{
		echo json_encode(array("success"=>false,"msg"=>"无法获取词条"));//
	}
	die();
	exit();
}
if($action == "create"){
	if(!empty($word["OriginalId"])){
		$dao = new ApprovePackageDao();
		try {
			$word['Status'] = 6;
			$dao->UpdateWord($word);
			echo json_encode(array("success"=>true,"msg"=>"更新词条成功"));
		} catch (Exception $e) {
			echo json_encode(array("success"=>false,"msg"=>"更新词条失败"));//
		}
	}else{
		echo json_encode(array("success"=>false,"msg"=>"无法获取词条"));//
	}
	die();
	exit();
}

?>