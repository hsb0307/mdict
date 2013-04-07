<?php
//require_once '../lib/dao/WordDao.php';
$category = require("../lib/EnumerableData.php");
if(isset($_GET["wordcategory"])){
	$wordCategory = $category['WordCategory'];
	echo json_encode($wordCategory);
	//die ();
	//exit ();
}
