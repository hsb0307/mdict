<?php

function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
		if($cip == '::1'){
			$cip = '127.0.0.1';
		}
	}
	else{
		$cip = "无法获取！";
	}
	return $cip;
}
//ȫ�ֱ��뺯��
function Encode($str){
    $str = str_replace("//", "////", $str);
    $str = str_replace("'", "''", $str); 
    //addslashes($str); 
    return $str;
}
function array2str($array, $pre = '', $pad = '', $sep = '&')
{
	$str = '';
	if(is_array($array)) {
		if(count($array)) {
			foreach($array as $v) {
				$str .= $pre.$v.$pad.$sep;
			}
			$str = substr($str, 0, -strlen($sep));
		}
	} else {
		$str .= $pre.$array.$pad;
	}

	return $str;
}
/*在Array和String类型之间转换，转换为字符串的数组可以直接在URL上传递*/
// convert a multidimensional array to url save and encoded string
// usage: string Array2String( array Array )
function Array2String($Array)
{
	$Return='';
	$NullValue="^^^";
	foreach ($Array as $Key => $Value) {
		if(is_array($Value))
			$ReturnValue='^^array^'.Array2String($Value);
		else
			$ReturnValue=(strlen($Value)>0)?$Value:$NullValue;
		$Return.=urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)).'||';
	}
	return urlencode(substr($Return,0,-2));
}

// convert a string generated with Array2String() back to the original (multidimensional) array
// usage: array String2Array ( string String)
function String2Array($String)
{
	$Return=array();
	$String=urldecode($String);
	$TempArray=explode('||',$String);
	$NullValue=urlencode(base64_encode("^^^"));
	foreach ($TempArray as $TempValue) {
		list($Key,$Value)=explode('|',$TempValue);
		$DecodedKey=base64_decode(urldecode($Key));
		if($Value!=$NullValue) {
			$ReturnValue=base64_decode(urldecode($Value));
			if(substr($ReturnValue,0,8)=='^^array^')
				$ReturnValue=String2Array(substr($ReturnValue,8));
			$Return[$DecodedKey]=$ReturnValue;
		}
		else
			$Return[$DecodedKey]=NULL;
	}
	return $Return;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		 * Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

function arrayToObject($d) {
	if (is_array($d)) {
		/*
		 * Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		// Return object
		return $d;
	}
}

function GUID(){
	if (function_exists('com_create_guid') === true)
	{
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}



?>