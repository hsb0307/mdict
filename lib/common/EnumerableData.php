<?php

$enumerableData = require("../../lib/EnumerableData.php");
function GetName($id, $category){
	global $enumerableData;
	$name = '';
	for($i = 0, $size = sizeof($enumerableData[$category]); $i < $size; $i++){
		if($enumerableData[$category][$i]['id'] == $id){
			$name = $enumerableData[$category][$i]['name'];
			break;
		}
	}
	return $name;
}
function GetId($name, $category){
	global $enumerableData;
	$id = 0;
	for($i = 0, $size = sizeof($enumerableData[$category]); $i < $size; $i++){
		if($enumerableData[$category][$i]['name'] == $name){
			$id = $enumerableData[$category][$i]['id'];
			break;
		}
	}
	return $id;
}