<?php
session_start();
require_once '../../lib/dao/userdao.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/EditTaskDao.php';

$username = "";
if(isset( $_POST ['Username']))
{
	$username =   Encode($_POST['Username']);
}
$password = "";
if(isset($_POST["Password"]))
{
	$password = Encode($_POST["Password"]);
}
//$username = 'admin';
//$password='admin';
$password = hash("sha512", $password, FALSE);

$userDao = new UserDao();
$user = $userDao->ValidateUser($username, $password);
$result = array('RoleId'=>'', 'HasPackage'=>0,'CurrentpackageId'=>'', 'IsApproved'=>false);

if($user != null){
	$result['RoleId'] = $user->RoleId;
	$result['IsApproved'] = $user->IsApproved;
	//setcookie("mongolian_dictionary_" . $username,"mongolian_dictionary_" . $username, 0, "/", $_SERVER["SERVER_NAME"], false, true);
	//setcookie("mongolian_dictionary", $username, time()+3600, '/');// 0
	//setcookie("mongolian_dictionary_" . $username,"mongolian_dictionary_" . $username, 0, "/", $_SERVER["SERVER_NAME"], false, true);
	//setcookie("mongolian_dictionary_" . $id,"mongolian_dictionary_" . $id, time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
	//SetCookie("MyCookie", "Value of MyCookie");
	//带失效时间的：
	//SetCookie("WithExpire", "Expire in 1 hour", time()+3600);//3600秒=1小时
	//什么都有的：
	//SetCookie("FullCookie", "Full cookie value", time()+3600, "/forum", ".phpuser.com", 1);
	//$userId = $userDao->GetMaxUserId(" where USERNAME = '".$username."'");
	$_SESSION["UserId"] = $user->UserId;//$userId;
	$_SESSION['RealName'] = $user->RealName;
	$_SESSION['RoleId'] = $user->RoleId;
	$_SESSION['WordCategory'] = $user->WordCategory;
	$_SESSION['CurrentpackageId'] = 0;
	if($user->RoleId > 0 && $user->RoleId < 10) {
		$result['HasPackage'] = $userDao->HasPackageExist($user->UserId, $user->RoleId);
		$_SESSION['CurrentpackageId'] = $userDao->CurrentEditPackage($user->UserId, $user->RoleId);
	}
	
	setcookie("currentPackageId", $_SESSION['CurrentpackageId'], time()+3600*24*30, '/');
}
echo json_encode($result);
?>