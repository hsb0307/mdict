<?php
require_once '../../lib/dao/UserDao.php';

session_start();
$userId = "";
if(isset($_SESSION["UserId"])){
	$userId = $_SESSION["UserId"];
}

$userDao = new UserDao();
$user = $userDao->GetById($userId);

echo json_encode(array("userName"=>$user->RealName,"userRole"=>$user->RoleId));

?>