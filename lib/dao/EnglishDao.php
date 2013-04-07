<?php
require_once '../../lib/common/OracleDb.php';

class EnglishDao{
	const GetPaged_Sentence = 'SELECT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", English "English", Japanese "Japanese", QueryCode "QueryCode", POS "POS", WordCategory "WordCategory" FROM (
SELECT t.*, rownum r FROM (
SELECT WordId, Chinese, Pinyin, English, Japanese, QueryCode, POS, WordCategory
FROM ChineseEnglishDictionary {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'select count(*)  from ChineseEnglishDictionary {@where}';
	
	const GetById_Sentence = 'SELECT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", English "English", Japanese "Japanese", QueryCode "QueryCode", POS "POS", WordCategory "WordCategory"  FROM ChineseEnglishDictionary WHERE WordId = ';
	
	const GetByChinese_Sentence = 'SELECT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", English "English", Japanese "Japanese", QueryCode "QueryCode", POS "POS", WordCategory "WordCategory"  FROM ChineseEnglishDictionary WHERE Chinese = \'';
	
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// 准备好SQL语句
		$sql = str_replace ( "{@where}", $where, EnglishDao::GetPaged_Sentence );
		$sql = str_replace ( "{@orderBy}", $orderBy, $sql );
		$sql = str_replace ( "{@endRowIndex}", $startRowIndex + $pageSize, $sql );
		$sql = str_replace ( "{@startRowIndex}", $startRowIndex, $sql );
	
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
	
		return $resultSet;
	}
	public function GetCount($where) {
		$sql = str_replace ( "{@where}", $where, EnglishDao::GetCount_Sentence );
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}
	
	public function GetByChinese($chinese) {
		if(!isset($chinese) || empty($chinese)) return '';
		$sql = EnglishDao::GetByChinese_Sentence;
		$sql =  $sql.$chinese.'\'';
		
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
	
		return $resultSet;
	}
	
}