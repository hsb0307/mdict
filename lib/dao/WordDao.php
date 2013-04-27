<?php
//$ROOT = "/Dict";

require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/FileHelper.php';
class WordDao
{
    const GetByID_Sentence = 'SELECT  WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", ChineseExampleSentence "ChineseExampleSentence", MongolianExampleSentence "MongolianExampleSentence", EnglishExampleSentence "EnglishExampleSentence", JapaneseExampleSentence "JapaneseExampleSentence", ExamineGroup "ExamineGroup", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status", Description "Description", to_char(CreatedDate,\'yyyy-mm-dd hh24:mi:ss\') "CreatedDate" FROM DictionaryA WHERE WordId = ';
    //const GetAll_Sentence = 'SELECT  WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", ChineseExampleSentence "ChineseExampleSentence", MongolianExampleSentence "MongolianExampleSentence", EnglishExampleSentence "EnglishExampleSentence", JapaneseExampleSentence "JapaneseExampleSentence", ExamineGroup "ExamineGroup", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status", Description "Description", to_char(CreatedDate,\'yyyy-mm-dd hh24:mi:ss\') "CreatedDate" FROM DictionaryA ORDER BY nlssort(SourceDictionary,\'NLS_SORT = SChinese_Pinyin_M\'), nlssort(Chinese,\'NLS_SORT = SChinese_Pinyin_M\')';
    //const GetAll_Sentence = 'SELECT  Chinese "Chinese", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status" FROM DictionaryA ORDER BY nlssort(SourceDictionary,\'NLS_SORT = SChinese_Pinyin_M\'), nlssort(Chinese,\'NLS_SORT = SChinese_Pinyin_M\')';
    const GetAll_Sentence = 'SELECT  Chinese "Chinese", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status" FROM DictionaryA ORDER BY Chinese';
    
    //const GetPaged_Sentence = "SELECT * FROM ( SELECT t.*, rownum r FROM (SELECT  WordId, Chinese, Pinyin, Mongolian, MongolianLatin, MongolianCyrillic, English, Japanese, ChineseExampleSentence, MongolianExampleSentence, EnglishExampleSentence, JapaneseExampleSentence, ExamineGroup, OriginalCategory, WordCategory, SourceDictionary, Status, Description, CreatedDate FROM DictionaryA {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}";
    //const GetPaged_Sentence = "SELECT * FROM ( SELECT t.*, rownum r FROM (SELECT  WordId, Chinese FROM DictionaryA {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}";
    ///const GetPaged_Sentence = "SELECT * FROM ( SELECT t.*, rownum r FROM (SELECT  WordId, Chinese FROM DictionaryA join entry on dictionarya.wordid = entry.entryid 
	//							join datapackage on entry.datapackageid = datapackage.datapackageid {@where} Order BY WordId ASC
	//							) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}";
    const GetPaged_Sentence = 'SELECT * FROM ( SELECT t.*, rownum r FROM (
    		SELECT  WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", ChineseExampleSentence "ChineseExampleSentence", MongolianExampleSentence "MongolianExampleSentence", EnglishExampleSentence "EnglishExampleSentence", JapaneseExampleSentence "JapaneseExampleSentence", ExamineGroup "ExamineGroup", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status", Description "Description", to_char(CreatedDate,\'yyyy-mm-dd hh24:mi:ss\') "CreatedDate"
    		FROM DictionaryA {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
    // {where} order by {orderBy} limit {startRowIndex}, {pageSize} ";
    //const GetCount_Sentence =  "select 	count(*)  from DictionaryA {@where} ";
    const GetCount_Sentence = "SELECT  count(*) FROM DictionaryA {@where}";
    //const DeleteUser_Sentence =  "DELETE FROM DictionaryA WHERE WordId = {@WordId} ";
    const Delete_Sentence =  'UPDATE DictionaryA SET Status = 999 WHERE WordId = {@WordId} ';
    // 修改词条时，暂时不修改词条的状态，仅仅修改数据包明细项的状态
    const Update_Sentence = 'UPDATE DictionaryA SET Chinese = \'{@Chinese}\', Pinyin = \'{@Pinyin}\', Mongolian = \'{@Mongolian}\', MongolianLatin = \'{@MongolianLatin}\', MongolianCyrillic = \'{@MongolianCyrillic}\', English = \'{@English}\', Japanese = \'{@Japanese}\', QueryCode = \'{@QueryCode}\', WordCategory = {@WordCategory}, LastModifiedBy = {@LastModifiedBy}, LastModifiedDate = sysdate  WHERE WordId = {@WordId} ';//, Status = {@Status}
    //const Insert_Sentence = "INSERT INTO Users (UserId, UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, CreateDate, IsApproved, RoleId, Description)  VALUES ({@UserId}, {@UserName}, {@Password}, {@PasswordQuestion}, {@PasswordAnswer}, {@RealName}, {@Gender}, {@Birthday}, {@PINCodes}, {@Mobile}, {@Telephone}, {@Company}, {@Email}, {@QQ}, {@CreateDate}, {@IsApproved}, {@RoleId}, {@Description}) ";
    const Insert_Sentence = 'INSERT INTO DictionaryA ( Chinese, Pinyin, Mongolian, MongolianLatin, MongolianCyrillic, English, Japanese, QueryCode, OriginalCategory, WordCategory, SourceDictionary, LastModifiedBy, Status )  VALUES (\'{@Chinese}\', \'{@Pinyin}\', \'{@Mongolian}\', \'{@MongolianLatin}\', \'{@MongolianCyrillic}\', \'{@English}\', \'{@Japanese}\', \'{@QueryCode}\', \'{@OriginalCategory}\', \'{@WordCategory}\', \'{@SourceDictionary}\', {@LastModifiedBy}, {@Status}) ';
    const SelectCurrentWordId_Sentence = 'select DictionaryA_WordId.currval from dual';
    const UpdateStatus_Sentence = 'UPDATE DictionaryA SET Status = {@Status}  WHERE WordId = {@WordId} ';
    
    const GetMaxUserId_Sentence =  "select 	max(UserId)  from Users {where} ";
    const IsUserExist_Sentence =  "select count(UserId)  from Users where UserId = '{@UserId}' ";
    const ValidateUser_Sentence =  "select RoleId  from Users where UserName = '{@UserName}' and Password = '{@Password}' ";
    const GetEntryById = "select WordId,Chinese,NVL(Pinyin,' ') as Pinyin,Nvl(English,' ') as English,NVL(Mongolian,' ') as Mongolian,NVL(MongolianLatin,' ') as MongolianLatin,NVL(MongolianCyrillic,' ') as MongolianCyrillic,NVL(Japanese,' ') as Japanese from dictionarya where WordId = '{@WordId}'";
    const UpdateEntryById = "update dictionarya set Chinese = '{@Chinese}',Pinyin = '{@Pinyin}',Mongolian = '{@Mongolian}',MongolianLatin = '{@MongolianLatin}',MongolianCyrillic = '{@MongolianCyrillic}',English = '{@English}',Japanese = '{@Japanese}' where WordId = '{@WordId}'";
    const InsertDictionaryA ="INSERT INTO DICTIONARYA (WordId,Chinese,Pinyin,Mongolian,MongolianLatin,MongolianCyrillic,English,Japanese,ChineseExampleSentence,MongolianExampleSentence,EnglishExampleSentence,JapaneseExampleSentence,ExamineGroup,OriginalCategory,WordCategory,SourceDictionary,Description,Status)
     VALUES ({@WordId},'{@Chinese}','{@Pinyin}','{@Mongolian}','{@MongolianLatin}','{@MongolianCyrillic}','{@English}','{@Japanese}','{@ChineseExampleSentence}','{@MongolianExampleSentence}','{@EnglishExampleSentence}','{@JapaneseExampleSentence}','{@ExamineGroup}','{@OriginalCategory}','{@WordCategory}','{@SourceDictionary}','{@Description}','{@Status}')";
    const InsertEntry = "INSERT INTO ENTRY (ENTRYID,DATAPACKAGEID,EDITDATE) select ORIGINALDATA_WordId.currval,'{@DATAPACKAGEID}',null from dual";
    const DeleteDictionaryA = "delete from DICTIONARYA where WordId = '{@WordId}'";
    const DeleteEntry = "delete from ENTRY where ENTRYID = '{@ENTRYID}'";
    const UpdateEntryDate = "update ENTRY set EDITDATE = to_date('{@EDITDATE}','yyyy-mm-dd hh24:mi:ss') where ENTRYID = '{@ENTRYID}'";
    const EditEntryCount = "select count(*) from entry join datapackage on entry.datapackageid = datapackage.datapackageid
    join dictionarya on dictionarya.wordid = entry.entryid  {@where}";//where DATAPACKAGEID = '{@DATAPACKAGEID}' and EDITDATE is not null";
    const GetEntryTop10 = "select WordId,Chinese,NVL(Pinyin,' ') as Pinyin,Nvl(English,' ') as English,NVL(Mongolian,' ') as Mongolian,NVL(MongolianLatin,' ') as MongolianLatin,NVL(MongolianCyrillic,' ') as MongolianCyrillic,NVL(Japanese,' ') as Japanese from (select entry.editdate,dictionarya.* from dictionarya join entry on dictionarya.wordid = entry.entryid
     join datapackage on entry.datapackageid = datapackage.datapackageid where entry.editdate is not null and datapackage.userid = '{@userid}' order by entry.editdate desc
     ) temp where rownum <=10";
    
    const GetByChinese_Sentence = 'SELECT  WordId "WordId", Chinese "Chinese", QueryCode "QueryCode", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", ChineseExampleSentence "ChineseExampleSentence", MongolianExampleSentence "MongolianExampleSentence", EnglishExampleSentence "EnglishExampleSentence", JapaneseExampleSentence "JapaneseExampleSentence", ExamineGroup "ExamineGroup", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status", Description "Description", to_char(CreatedDate,\'yyyy-mm-dd hh24:mi:ss\') "CreatedDate" FROM DictionaryA WHERE Status < 20 AND Chinese = \'{@Chinese}\' ';
    
    const GetCountByWordCategory_Sentence ='SELECT  count(*) FROM DictionaryA WHERE WordCategory = {@WordCategory} AND Status = {@Status}';
    
    const ExistWord_Sentence = 'SELECT  count(*) FROM DictionaryA WHERE SourceDictionary = {@SourceDictionary} AND Chinese = \'{@Chinese}\'';
    
    const GetRepetitiveWord_Sentence = 'select WordId "WordId", Chinese "Chinese" from DictionaryA where WordCategory = {@WordCategory} AND Chinese in (select  Chinese  from  DictionaryA WHERE WordCategory = {@WordCategory} AND Status < 900 group  by  Chinese  having  count(Chinese) > 1) order by Chinese';
    
    const GetRepetitiveWordPaged_Sentence = 'SELECT WordId "WordId", Chinese "Chinese" FROM ( SELECT t.*, rownum r FROM (
    		SELECT  WordId, Chinese
    		FROM DictionaryA WHERE WordCategory = {@WordCategory} AND Chinese in (select  Chinese  from  DictionaryA WHERE WordCategory = {@WordCategory} AND Status < 900 group  by  Chinese  having  count(Chinese) > 1)  Order BY Chinese) t 
        WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
    const GetRepetitiveWordCount_Sentence = 'select count(*) from DictionaryA where WordCategory = {@WordCategory} AND Chinese in (select  Chinese  from  DictionaryA WHERE WordCategory = {@WordCategory} AND Status < 900 group  by  Chinese  having  count(Chinese) > 1) ';
    
    public function GetRepetitiveWordPaged($wordCategory, $startRowIndex, $pageSize) {
    	$sql = str_replace("{@WordCategory}",$wordCategory, WordDao::GetRepetitiveWordPaged_Sentence );
    	$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
    	$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);
    	 
    	return $resultSet;
    }
    
    public function GetRepetitiveWordCount( $wordCategory) {
    	$sql = str_replace("{@WordCategory}", $wordCategory, WordDao::GetRepetitiveWordCount_Sentence );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
    public function ExistWord($userId, $chinese) {
    	$sql = str_replace ( "{@SourceDictionary}", $userId, WordDao::ExistWord_Sentence );
    	$sql = str_replace ( "{@Chinese}", $chinese, $sql );
    	 
    	$db = Database::Connect ();
    	return $db->GetSingleVal ( $sql );
    }
    
    public function GetCountByWordCategory($wordCategory, $status, $repetitive=false) {
    	$db = Database::Connect();
    	$sql = str_replace("{@WordCategory}",$wordCategory, WordDao::GetCountByWordCategory_Sentence );
    	$sql = str_replace("{@Status}",$status, $sql );
    	if($repetitive){
    		$sql = $sql . ' AND IsRepetitive = 1 ';
    	}
    	$count = $db->GetSingleVal($sql);
    	$db->Close();
    	return $count;
    }
    
    public function GetByChinese($searchText, $id) {
    	$db = Database::Connect();
    	$sql = str_replace("{@Chinese}",trim($searchText), WordDao::GetByChinese_Sentence );
    	if(isset($id) && $id < 1) {
    		$sql = $sql . ' AND WordId <> '.$id; 
    	}
    	$words = $db->GetResultSet($sql);
    	$db->Close();
    	return $words;
    }
    
    public function ValidateUser( $username, $password) {
    	$sql = str_replace("{@UserName}",$username, WordDao::ValidateUser_Sentence );
    	$sql = str_replace("{@Password}",$password, $sql );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
    public function IsUserExist( $id) {
    	$sql = str_replace("{@UserId}",$id, WordDao::IsUserExist_Sentence );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
	public function GetMaxUserId( $where) {
    	$sql = str_replace("{where}",$where, WordDao::GetMaxUserId_Sentence );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
    public function GetById($id) {
        $db = Database::Connect();
        $sql = WordDao::GetByID_Sentence.$id; //$db->Encode($id);
        $user = $db->GetSingleObject($sql);
        $db->Close();
        return $user;
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
		$sql = str_replace('{@QueryCode}', $array['QueryCode'], $sql);
		
		$sql = str_replace('{@WordCategory}', $array['WordCategory'], $sql);
		$sql = str_replace('{@LastModifiedBy}', $array['LastModifiedBy'], $sql);
		$sql = str_replace('{@Status}', $array['Status'], $sql);

		if(isset($array["WordId"])){
    		$sql = str_replace("{@WordId}",$array["WordId"], $sql);
		}
    	return  $sql;
    }
    
    public function PrepareInsertSQL($array)
    {
    	$sql = $this->PrepareUpdateSQL($array, WordDao::Insert_Sentence);
    	
    	$sql = str_replace('{@OriginalCategory}', $array['OriginalCategory'], $sql);
    	$sql = str_replace('{@WordCategory}', $array['WordCategory'], $sql);
    	$sql = str_replace('{@SourceDictionary}', $array['SourceDictionary'], $sql);
    	
    	
    	return  $sql;
    }
    
    public function Create($array)
    {
    	// 准备好SQL语句
    	$sql = $this->PrepareInsertSQL($array);
    	//$sql = str_replace("{@Id}",$array["Id"], $sql);
    	
    	$db = Database::Connect();
    	$db->Execute($sql);
    	$db->Close();
    }
    
    public function Update($array)
    {
    	$sql = $this->PrepareUpdateSQL($array, WordDao::Update_Sentence);
    
    	$db = Database::Connect();
    	$db->Execute($sql);
    	$db->Close();
    }

    public function GetAll() {
    	// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
    	// 准备好SQL语句
    	$sql = WordDao::GetAll_Sentence;
    	
    	// 连接数据库，并读取结果集
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);
    	 
    	return $resultSet;
    }
    
    
    public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
    	// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
    	// 准备好SQL语句
    	$sql = str_replace("{@where}",$where, WordDao::GetPaged_Sentence );
    	$sql = str_replace("{@orderBy}",$orderBy, $sql );
    	$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
    	$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
    	
    	// 连接数据库，并读取结果集
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);
    	
    	return $resultSet;
    }
    
    public function GetCount( $where) {
    	$sql = str_replace("{@where}",$where, WordDao::GetCount_Sentence );
    	$db = Database::Connect();
    	//echo "ss";
    	return $db->GetSingleVal($sql);
    	//$row = mysql_fetch_object($result)
    }
    
    public function GetEditEntryCount($where){
    	$sqlEditEntryCount = WordDao::EditEntryCount;
    	$sqlEditEntryCount = str_replace('{@where}', $where, $sqlEditEntryCount);
    	$db = Database::Connect();
    	return $db->GetSingleVal($sqlEditEntryCount);
    }
    
    public function Delete( $id) {
    	$sql = str_replace("{@WordId}",$id, WordDao::Delete_Sentence );
    	
    	$db = Database::Connect();
    	$db->Execute($sql);
    	$db->Close();
    }
    
    
    public function GetDesc() {
    	// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
    	// 准备好SQL语句
    	$sql = "select column_name ColumnName,data_type DataType  from user_tab_columns where Table_Name='USERS'";// "desc Users";
    	 
    	// 连接数据库，并读取结果集
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);
    
    	return $resultSet;
    }
    
    public function GetEntryById($id){
    	try {
    		$sql = WordDao::GetEntryById;
    		$sql = str_replace('{@WordId}', $id, $sql);
    		$db = Database::Connect();
    		return $db->GetSingleObject($sql);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }
    
    public function AddEntry($arrayEntry){
    	try {
    		$sql = WordDao::InsertDictionaryA;
    		$sql = str_replace('{@WordId}', "ORIGINALDATA_WordId"."."."nextval", $sql);
    		$sql = str_replace('{@Chinese}', $arrayEntry["Chinese"], $sql);
    		$sql = str_replace('{@Pinyin}', $arrayEntry["Pinyin"], $sql);
    		$sql = str_replace('{@Mongolian}', $arrayEntry["Mongolian"], $sql);
    		$sql = str_replace('{@MongolianLatin}', $arrayEntry["MongolianLatin"], $sql);
    		$sql = str_replace('{@MongolianCyrillic}', $arrayEntry["MongolianCyrillic"], $sql);
    		$sql = str_replace('{@English}', $arrayEntry["English"], $sql);
    		$sql = str_replace('{@Japanese}', $arrayEntry["Japanese"], $sql);
    		$sql = str_replace('{@ChineseExampleSentence}', $arrayEntry["ChineseExampleSentence"], $sql);
    		$sql = str_replace('{@MongolianExampleSentence}', $arrayEntry["MongolianExampleSentence"], $sql);
    		$sql = str_replace('{@EnglishExampleSentence}', $arrayEntry["EnglishExampleSentence"], $sql);
    		$sql = str_replace('{@JapaneseExampleSentence}', $arrayEntry["JapaneseExampleSentence"], $sql);
    		$sql = str_replace('{@ExamineGroup}', $arrayEntry["ExamineGroup"], $sql);
    		$sql = str_replace('{@OriginalCategory}', $arrayEntry["OriginalCategory"], $sql);
    		$sql = str_replace('{@WordCategory}', $arrayEntry["WordCategory"], $sql);
    		$sql = str_replace('{@SourceDictionary}', $arrayEntry["SourceDictionary"], $sql);
    		$sql = str_replace('{@Description}', $arrayEntry["Description"], $sql);
    		$sql = str_replace('{@Status}', $arrayEntry["Status"], $sql);

    		$sqlEntry = WordDao::InsertEntry;
    		$sqlEntry = str_replace('{@DATAPACKAGEID}', $arrayEntry["DataPackageId"], $sqlEntry);
    		
    		$sql = "begin ".$sql.";".$sqlEntry."; end;";
    		$db = Database::Connect();
    		
    		$db->Execute($sql);
    		
    		return $db->GetSingleVal("select ORIGINALDATA_WordId.currval from dual");//是否在一个事务中不能确定
    		
    		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }
    
    public function UpdateEntryById($arrayEntry){
    	try {
    		$sql = WordDao::UpdateEntryById;
    		$sql = str_replace('{@Chinese}', $arrayEntry["Chinese"], $sql);
    		$sql = str_replace('{@Pinyin}', $arrayEntry["Pinyin"], $sql);
    		$sql = str_replace('{@WordId}', $arrayEntry["WordId"], $sql);
    		$sql = str_replace('{@Mongolian}', $arrayEntry["Mongolian"], $sql);
    		$sql = str_replace('{@MongolianLatin}', $arrayEntry["MongolianLatin"], $sql);
    		$sql = str_replace('{@MongolianCyrillic}', $arrayEntry["MongolianCyrillic"], $sql);
    		$sql = str_replace('{@Japanese}', $arrayEntry["Japanese"], $sql);
    		$sql = str_replace('{@English}', $arrayEntry["English"], $sql);
    		
    		$date = date("Y-m-d H:i:s");
    		
    		$sqlEntryDate = WordDao::UpdateEntryDate;
    		$sqlEntryDate = str_replace('{@EDITDATE}', $date, $sqlEntryDate);
    		$sqlEntryDate = str_replace('{@ENTRYID}', $arrayEntry["WordId"], $sqlEntryDate);
    		
    		$sql = "begin ".$sql.";".$sqlEntryDate."; end;";
    		$db = Database::Connect();
    		$db->Execute($sql);    		   		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    	
    } 
    
    public function DeleteEntryById($entryId){
    	try {
	    	$sql = WordDao::DeleteDictionaryA;
	    	$sql = str_replace('{@WordId}', $entryId, $sql);
	    	$sqlEntry = WordDao::DeleteEntry;
	    	$sqlEntry = str_replace('{@ENTRYID}', $entryId, $sqlEntry);
	    	$sql = "begin ".$sql.";".$sqlEntry."; end;";
	    	$db = Database::Connect();    	
	    	$db->Execute($sql);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }
    
    public function GetEntryTop10($userId){
    	try {
	    	$sql = WordDao::GetEntryTop10;
	    	$sql = str_replace('{@userid}', $userId, $sql);
	    	$db = Database::Connect();
	    	return $db->GetResultSet($sql);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }
    
    public function Diff($oldObj, $newObj) {
    	$diff = '';
    	if($oldObj->Chinese != $newObj['Chinese']) $diff .= ('中文:'.$oldObj->Chinese.'=>'.$newObj['Chinese'].',');
		if($oldObj->Pinyin != $newObj['Pinyin']) $diff .= ('拼音:'.$oldObj->Pinyin.'=>'.$newObj['Pinyin'].',');
		if($oldObj->Mongolian != $newObj['Mongolian']) $diff .= ('传统蒙文:'.$oldObj->Mongolian.'=>'.$newObj['Mongolian'].',');
		if($oldObj->MongolianLatin != $newObj['MongolianLatin']) $diff .= ('拉丁转写:'.$oldObj->MongolianLatin.'=>'.$newObj['MongolianLatin'].',');
		if($oldObj->MongolianCyrillic != $newObj['MongolianCyrillic']) $diff .= ('西里尔蒙文:'.$oldObj->MongolianCyrillic.'=>'.$newObj['MongolianCyrillic'].',');
		if($oldObj->English != $newObj['English']) $diff .= ('英文:'.$oldObj->English.'=>'.$newObj['English'].',');
		if($oldObj->Japanese != $newObj['Japanese']) $diff .= ('日文:'.$oldObj->Japanese.'=>'.$newObj['Japanese'].',');
		if($oldObj->WordCategory != $newObj['WordCategory']) $diff .= ('词条类别:'.$oldObj->WordCategory.'=>'.$newObj['WordCategory'].',');
		//if($oldObj->Status != $newObj['Status']) $diff .= ('状态:'.$oldObj->Status.'=>'.$newObj['Status'].',');
		if($oldObj->Description != $newObj['Description']) $diff .= ('备注:'.$oldObj->Description.'=>'.$newObj['Description'].',');
    	
		$diff = str_replace("//", "////", $diff);
		$diff = str_replace("'", "''", $diff);
		
    	return $diff;
    }
    
    
}
?>