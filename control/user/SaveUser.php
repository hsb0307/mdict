<?php
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/common/_common.php';

$op = "add";
if(isset( $_GET ['op']))
{
	$op =   $_GET['op'];
}

$user = array();
if(isset( $_POST ['UserId']))
{
	$user["UserId"] =   $_POST['UserId'];
}

$user["UserName"] =  Encode( $_POST['UserName']);
$user["Password"] =  hash("sha512", Encode( $_POST['Password']), FALSE);
$user["PasswordQuestion"] =  Encode( $_POST['PasswordQuestion']);
$user["PasswordAnswer"] =  Encode( $_POST['PasswordAnswer']);
$user["RealName"] =  Encode( $_POST['RealName']);
$user["Gender"] =  Encode( $_POST['Gender']);
$user["Birthday"] =  Encode( $_POST['Birthday']);
$user["PINCodes"] =  Encode( $_POST['PINCodes']);
$user["Mobile"] =  Encode( $_POST['Mobile']);
$user["Telephone"] = Encode( $_POST["Telephone"]);
$user["Company"] = Encode( $_POST["Company"]);
$user["Email"] = Encode( $_POST["Email"]);
$user["QQ"] =  "";//Encode( $_POST['qqNumber']);
//$user["IsApproved"] =  1;
$user["RoleId"] =   $_POST['RoleId'];
$user["WordCategory"] =   $_POST['WordCategory'];
$user["Description"] = Encode( $_POST["Description"]);

$result = array('RoleId'=>'', 'HasPackage'=>0);
$userDao = new UserDao();
if($op == "add")
{
	$user["IsApproved"] =  0;
	$userId = $userDao->Create( $user );
	$result['RoleId'] = 100;
}
else
{
	$user["IsApproved"] =  $_POST["IsApproved"];
	$userDao->UpdateUser( $user );
	
	session_start();
	$_SESSION["UserId"] = $userId;
	$_SESSION['RealName'] = $user["RealName"];
	$_SESSION['RoleId'] = $user["RoleId"];
	$_SESSION['WordCategory'] = $user["WordCategory"];

	$result['RoleId'] =  $user["RoleId"];
}

echo json_encode( $result);
//echo $user["RoleId"];
?>