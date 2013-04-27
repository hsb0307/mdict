<?php
require_once '../../lib/common/OracleDb.php';

class ApproveWordDao{
	const GetPaged_Sentence = 'SELECT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", English "English", Japanese "Japanese", QueryCode "QueryCode", POS "POS", WordCategory "WordCategory" FROM (
SELECT t.*, rownum r FROM (
SELECT WordId, Chinese, Pinyin, English, Japanese, QueryCode, POS, WordCategory
FROM ChineseEnglishDictionary {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'select count(*)  from ChineseEnglishDictionary {@where}';
	
	const GetById_Sentence = 'SELECT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", Russian "Russian", QueryCode "QueryCode", OriginalId "OriginalId", ExamineGroup "ExamineGroup", WordCategory "WordCategory", SourceDictionary "SourceDictionary", CreatedBy "CreatedBy", CreatedDate "CreatedDate", LastModifiedBy "LastModifiedBy", LastModifiedDate "LastModifiedDate", IsDeleted "IsDeleted", IsPublished "IsPublished", Status "Status", Description "Description" FROM DictionaryB WHERE WordId = ';
	
	//const GetByChinese_Sentence = 'SELECT DISTINCT WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", English "English", Japanese "Japanese", QueryCode "QueryCode", POS "POS", WordCategory "WordCategory"  FROM ChineseEnglishDictionary WHERE Chinese = \'';
	const GetByChinese_Sentence = 'SELECT DISTINCT  Chinese "Chinese", English "English"  FROM ChineseEnglishDictionary WHERE Chinese = \'';
	
	const ExistByChineseAndCategory_Sentence = 'SELECT WordId FROM DictionaryB WHERE Chinese = \'{@Chinese}\' AND WordCategory = {@WordCategory}';
	
	const Insert_Sentence = 'INSERT INTO DictionaryB ( Chinese, Pinyin, Mongolian, MongolianLatin, MongolianCyrillic, English, Japanese, Russian, QueryCode, OriginalId, ExamineGroup, WordCategory, SourceDictionary, CreatedBy, LastModifiedBy, Status, Description )  VALUES ( \'{@Chinese}\', \'{@Pinyin}\', \'{@Mongolian}\', \'{@MongolianLatin}\', \'{@MongolianCyrillic}\', \'{@English}\', \'{@Japanese}\', \'{@Russian}\', \'{@QueryCode}\', {@OriginalId}, {@ExamineGroup}, {@WordCategory}, {@SourceDictionary}, {@CreatedBy}, {@LastModifiedBy}, {@Status}, \'{@Description}\') ';
	const Update_Sentence = 'UPDATE DictionaryB SET QueryCode = \'{@QueryCode}\', Pinyin = \'{@Pinyin}\', Mongolian = \'{@Mongolian}\', MongolianLatin = \'{@MongolianLatin}\', MongolianCyrillic = \'{@MongolianCyrillic}\', English = \'{@English}\', Japanese = \'{@Japanese}\', Russian = \'{@Russian}\', OriginalId = \'{@OriginalId}\', LastModifiedBy = \'{@LastModifiedBy}\', LastModifiedDate = SYSDATE WHERE  WordId = ';
	
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
	
	public function PrepareUpdateSQL($array, $sqlUpdate)
	{
		$sql = str_replace('{@Chinese}', $array['Chinese'], $sqlUpdate);
		$sql = str_replace('{@Pinyin}', $array['Pinyin'], $sql);
		$sql = str_replace('{@Mongolian}', $array['Mongolian'], $sql);
		$sql = str_replace('{@MongolianLatin}', $array['MongolianLatin'], $sql);
		$sql = str_replace('{@MongolianCyrillic}', $array['MongolianCyrillic'], $sql);
		$sql = str_replace('{@English}', $array['English'], $sql);
		$sql = str_replace('{@Japanese}', $array['Japanese'], $sql);
		$sql = str_replace('{@Russian}', $array['Russian'], $sql);
		$sql = str_replace('{@OriginalId}', $array['OriginalId'], $sql);
		$sql = str_replace('{@ExamineGroup}', $array['ExamineGroup'], $sql);
		//$sql = str_replace('{@WordCategory}', $array['WordCategory'], $sql);
		
		$sql = str_replace('{@LastModifiedBy}', $array['LastModifiedBy'], $sql);
		$sql = str_replace('{@Status}', $array['Status'], $sql);
		$sql = str_replace('{@Description}', $array['Description'], $sql);
		$sql = str_replace('{@QueryCode}', $array['QueryCode'], $sql);
	
		if(isset($array["WordId"])){
			$sql = str_replace("{@WordId}",$array["WordId"], $sql);
		}
		return  $sql;
	}
	
	public function PrepareInsertSQL($array)
	{
		$sql = $this->PrepareUpdateSQL($array, ApproveWordDao::Insert_Sentence);
		//$sql = str_replace('{@QueryCode}', $array['QueryCode'], $sql);
		//$sql = str_replace('{@OriginalCategory}', $array['OriginalCategory'], $sql);
		$sql = str_replace('{@WordCategory}', $array['WordCategory'], $sql);
		$sql = str_replace('{@SourceDictionary}', $array['SourceDictionary'], $sql);
		$sql = str_replace('{@CreatedBy}', $array['CreatedBy'], $sql);
		 
		return  $sql;
	}
	
}

?>