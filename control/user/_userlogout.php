<?php
require_once '../../lib/dao/UserDao.php';
$op = 'exist';
if(isset($_GET["op"])) { $op = $_GET["op"]; }
if($op == "exist"){
	$dao = new UserDao();
	$username = Encode($_POST["Username"]) ;
	$count = $dao->IsUserExist($username);
	echo json_encode($count);
	die();
	exit();
}

?>