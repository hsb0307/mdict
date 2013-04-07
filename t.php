<?php
echo $_SERVER['HTTP_USER_AGENT'];

/*
$value = 'something from somewhere';

setcookie("TestCookie", $value);

echo $_COOKIE["TestCookie"];
echo $HTTP_COOKIE_VARS["TestCookie"];
// set the cookies
setcookie("cookie[three]", "cookiethree");
setcookie("cookie[two]", "cookietwo");
setcookie("cookie[one]", "cookieone");

// after the page reloads, print them out
if (isset($_COOKIE['cookie'])) {
    foreach ($_COOKIE['cookie'] as $name => $value) {
        $name = htmlspecialchars($name);
        $value = htmlspecialchars($value);
        echo "$name : $value <br />\n";
    }
}

setcookie("TestCookie", "", time()-3600);
setcookie("cookie[three]", "", time()-3600);
setcookie("cookie[two]", "", time()-3600);
setcookie("cookie[one]", "", time()-3600);

//phpinfo();
$i = '';
if(isset($i)){
	echo '$i isset = true';
} else {
	echo '$i isset = false';
}
if(empty($i)){
	echo '$i empty = true';
} else {
	echo '$i empty = false';
}

function f($a1, $a2=true, $a3) {
	echo '<br />==============<br />';
	echo $a1;
	//echo '==============<br />';
	//echo empty($a2);
	echo '==============<br />';
	if(!$a2) {
		echo 'a2';//empty($a2);
	}
	echo '==============<br />';
	echo !isset($a3);

}
f('b');
*/

/*
 * 
$dbConfig = require("lib/config.inc.php");
require_once 'lib/base.inc.php';
require_once 'lib/common/_common.php';
//// $dbConfig["DB_HOST"], $dbConfig["DB_USER"], $dbConfig["DB_PWD"]
test_global();
function test_global() {

	 echo $dbConfig["DB_HOST"]. $dbConfig["DB_USER"]. $dbConfig["DB_PWD"];// 这句无输出
	global $dbConfig;
	echo $dbConfig["DB_HOST"]. $dbConfig["DB_USER"]. $dbConfig["DB_PWD"];// 这句有输出

	echo "<br />";
	echo DBHOST;echo "<br />";
	echo DBNAME;echo "<br />";
	echo DBUSER;echo "<br />";
	echo DBPWD;echo "<br />";

	$uuid = GUID();
	echo $uuid;

}
	*/

?>