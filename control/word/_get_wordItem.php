<?php

$op = $_GET["op"];
$id = "";
$name="";

if($op == "get"){
$id = $_POST["id"];
$name = $name==""?"学士":$name;
$arr = array ('id'=>$id,'name'=>$name);
}

if($op == "update"){
	$id = $_POST["id"];
	$name = $_POST["name"];
	$arr = array ('id'=>$id,'name'=>$name);
}



echo json_encode($arr);
?>