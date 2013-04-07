<?php
ini_set ('memory_limit', '512M');
ini_set ('max_execution_time', '0');
require_once '../../lib/dao/WordDao.php';
require_once '../../lib/common/FileHelper.php';
require_once '../../lib/common/EnumerableData.php';

$wordDao = new WordDao();
$words = $wordDao->GetAll();
//$word = $wordDao->GetById(10000);

//20121112peij修改文件路径$_SERVER["DOCUMENT_ROOT"]
$log = new FileHelper();
$log->file = dirname(dirname(dirname(__FILE__)))."\\doc\\dictionary.csv";
$template = '"{@Chinese}", "{@Mongolian}", "{@MongolianLatin}", "{@MongolianCyrillic}", "{@English}", "{@OriginalCategory}", "{@WordCategory}", "{@SourceDictionary}"';
$line = "";
for($i = 0, $size = sizeof($words); $i < $size; $i++){
	//$words[$i]['Chinese'] = rand(000000, 999999);
	//$category = GetId($words[$i]['SourceDictionary'], 'WordCategory');
	$line = str_replace('{@Chinese}', str_replace('"', '“', $words[$i]['Chinese']) , $template );
	$line = str_replace('{@Mongolian}', str_replace('"', '“', $words[$i]['Mongolian']) , $line );
	$line = str_replace('{@MongolianLatin}', str_replace('"', '“', $words[$i]['MongolianLatin']) , $line );
	$line = str_replace('{@MongolianCyrillic}', str_replace('"', '“', $words[$i]['MongolianCyrillic']) , $line );
	$line = str_replace('{@English}', str_replace('"', '“', $words[$i]['English']) , $line );
	$line = str_replace('{@OriginalCategory}', $words[$i]['OriginalCategory'], $line );
	$line = str_replace('{@WordCategory}', $words[$i]['WordCategory'], $line );
	$line = str_replace('{@SourceDictionary}', $words[$i]['SourceDictionary'], $line );

	$log->writeline($line.PHP_EOL);
	$line = "";
	//if($i==50)	break;
}
echo "Success!";

//echo json_encode($word);
/*
 * require_once '../../lib/dao/UserDao.php';

$userDao = new UserDao();

$columns = $userDao->GetDesc();
foreach ($columns as $row) {
	$ColumnName = ucfirst(strtolower($row["COLUMNNAME"]));
	
	echo '$user["'.$ColumnName.'"] =  Encode( $_POST["'.$ColumnName.'"]);<br />'; //.$row["DATATYPE"].
	
	
}
$sha1_second_value = hash("sha512", "secret", FALSE);
echo $sha1_second_value;

$columns = $userDao->GetAll();

foreach ($columns as $row) {
	echo $row["REALNAME"];
}
 * 
 */

?>