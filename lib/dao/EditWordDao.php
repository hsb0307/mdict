<?php
//$ROOT = "/Dict";

require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/FileHelper.php';
require_once 'EditTaskDao.php';
class EditWordDao
{
    const GetUserByID_Sentence = 'SELECT  WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", ChineseExampleSentence "ChineseExampleSentence", MongolianExampleSentence "MongolianExampleSentence", EnglishExampleSentence "EnglishExampleSentence", JapaneseExampleSentence "JapaneseExampleSentence", ExamineGroup "ExamineGroup", OriginalCategory "OriginalCategory", WordCategory "WordCategory", SourceDictionary "SourceDictionary", Status "Status", Description "Description", to_char(CreatedDate,\'yyyy-mm-dd hh24:mi:ss\') "CreatedDate" FROM DictionaryA WHERE WordId = ';
    const GetAll_Sentence = "SELECT UserId, UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, CreateDate, IsApproved, RoleId, Description FROM Users ";    
    const DeleteUser_Sentence =  "DELETE FROM Users where UserId = {@UserId} ";
    const Update_Sentence = "UPDATE Users SET UserName = '{@UserName}', Password = '{@Password}', PasswordQuestion = '{@PasswordQuestion}', PasswordAnswer = '{@PasswordAnswer}', RealName = '{@RealName}', Gender = '{@Gender}', Birthday = '{@Birthday}', PINCodes = '{@PINCodes}', Mobile = '{@Mobile}', Telephone = '{@Telephone}', Company = '{@Company}', Email = '{@Email}', QQ = '{@QQ}', IsApproved = {@IsApproved}, Description = '{@Description}', RoleId = {@RoleId} WHERE UserId = {@UserId} ";
    const Insert_Sentence = "INSERT INTO Users (UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, IsApproved, RoleId, Description)  VALUES ('{@UserName}', '{@Password}', '{@PasswordQuestion}', '{@PasswordAnswer}', '{@RealName}', '{@Gender}', to_date('{@Birthday}','yyyy-mm-dd'), '{@PINCodes}', '{@Mobile}', '{@Telephone}', '{@Company}', '{@Email}', '{@QQ}', {@IsApproved}, {@RoleId}, '{@Description}') ";
    const GetMaxUserId_Sentence =  "select 	max(UserId)  from Users {where} ";
    const IsUserExist_Sentence =  "select count(UserId)  from Users where UserId = '{@UserId}' ";
    const ValidateUser_Sentence =  "select RoleId  from Users where UserName = '{@UserName}' and Password = '{@Password}' ";
    const GetEntryById = 'select WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese",WordCategory "WordCategory",SourceDictionary "SourceDictionary",Status "Status",Description "Description" from dictionarya where WordId = \'{@WordId}\'';
    const InsertEntry = "INSERT INTO edititems (wordid,PACKAGEID,status) select ORIGINALDATA_WordId.currval,'{@DATAPACKAGEID}',1 from dual";
    const DeleteDictionaryA = "delete from DICTIONARYA where WordId = '{@WordId}'";
    const DeleteEntry = "delete from edititems where wordid = '{@ENTRYID}'";
        
    //总记录数
    const GetCount = "SELECT  count(*) FROM DictionaryA join edititems on dictionarya.wordid = edititems.wordid join editpackage on edititems.packageid = editpackage.packageid {@where}";
    //编辑记录数
    const GetEditCount = "select count(*) from edititems join editpackage on edititems.packageid = editpackage.packageid
    join dictionarya on dictionarya.wordid = edititems.wordid  {@where}";
    //分页取记录
    const GetPaged = 'SELECT WordId "WordId", Chinese "Chinese" FROM ( SELECT t.*, rownum r FROM (SELECT  DictionaryA.WordId, DictionaryA.Chinese FROM DictionaryA join edititems on dictionarya.wordid = edititems.wordid
								join editpackage on edititems.packageid = editpackage.packageid {@where} Order BY WordId ASC
								) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
    //前10条历史记录
    const GetDictTop10 = 'select WordId "WordId",Chinese "Chinese",Pinyin "Pinyin",English "English",Mongolian "Mongolian",MongolianLatin "MongolianLatin",MongolianCyrillic "MongolianCyrillic",Japanese "Japanese" from (
						  select dictionarya.* from dictionarya join edititems on dictionarya.wordid = edititems.wordid join editpackage on edititems.packageid = editpackage.packageid 
     					  where edititems.modifieddate is not null and editpackage.userid = \'{@userid}\' order by edititems.modifieddate desc ) temp where rownum <=10';
    //插入字典
    const InsertDict ="INSERT INTO DICTIONARYA (Chinese,Pinyin,Mongolian,MongolianLatin,MongolianCyrillic,English,Japanese,ChineseExampleSentence,MongolianExampleSentence,EnglishExampleSentence,JapaneseExampleSentence,ExamineGroup,OriginalCategory,WordCategory,SourceDictionary,Description,Status)
     				   VALUES ('{@Chinese}','{@Pinyin}','{@Mongolian}','{@MongolianLatin}','{@MongolianCyrillic}','{@English}','{@Japanese}','{@ChineseExampleSentence}','{@MongolianExampleSentence}','{@EnglishExampleSentence}','{@JapaneseExampleSentence}','{@ExamineGroup}','{@OriginalCategory}','{@WordCategory}','{@SourceDictionary}','{@Description}','{@Status}')";
    //修改字典
    const UpdateDictById = "update dictionarya set Chinese = '{@Chinese}',Pinyin = '{@Pinyin}',Mongolian = '{@Mongolian}',MongolianLatin = '{@MongolianLatin}',MongolianCyrillic = '{@MongolianCyrillic}',English = '{@English}',Japanese = '{@Japanese}',Status = '{@Status}' where WordId = '{@WordId}'";
    
    public $db=null;
        
    public function ValidateUser( $username, $password) {
    	$sql = str_replace("{@UserName}",$username, EditWordDao::ValidateUser_Sentence );
    	$sql = str_replace("{@Password}",$password, $sql );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
    public function IsUserExist( $id) {
    	$sql = str_replace("{@UserId}",$id, EditWordDao::IsUserExist_Sentence );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
	public function GetMaxUserId( $where) {
    	$sql = str_replace("{where}",$where, EditWordDao::GetMaxUserId_Sentence );
    	$db = Database::Connect();
    	return $db->GetSingleVal($sql);
    }
    
    public function GetById($id) {
        $db = Database::Connect();
        $sql = EditWordDao::GetUserByID_Sentence.$id; //$db->Encode($id);
        $user = $db->GetSingleObject($sql);
        $db->Close();
        return $user;
    }
    
    /*
    public function Update($existingRow) {
        $db = Database::Connect();
        
        $user = $db->UpdateByPk('Users', $existingRow);
        $db->Close();
        return $user;
    }
*/
  

    public function PrepareSQL($array, $sql)
    {
    	$sql = str_replace("{@UserName}",$array["UserName"], $sql);
    	$sql = str_replace("{@Password}",$array["Password"], $sql);
    	$sql = str_replace("{@PasswordQuestion}",$array["PasswordQuestion"], $sql);
    	$sql = str_replace("{@PasswordAnswer}",$array["PasswordAnswer"], $sql);
    	$sql = str_replace("{@RealName}",$array["RealName"], $sql);
    	$sql = str_replace("{@Gender}",$array["Gender"], $sql);
    	$sql = str_replace("{@Birthday}",$array["Birthday"], $sql);
    	$sql = str_replace("{@PINCodes}",$array["PINCodes"], $sql);
    	$sql = str_replace("{@Mobile}",$array["Mobile"], $sql);
    	$sql = str_replace("{@Telephone}",$array["Telephone"], $sql);
    	$sql = str_replace("{@Company}",$array["Company"], $sql);
		$sql = str_replace("{@Email}",$array["Email"], $sql);
    	$sql = str_replace("{@QQ}",$array["QQ"], $sql);
    	$sql = str_replace("{@IsApproved}",$array["IsApproved"], $sql);
    	$sql = str_replace("{@RoleId}",$array["RoleId"], $sql);
    	$sql = str_replace("{@Description}",$array["Description"], $sql);
    	$sql = str_replace("{@UserId}",$array["UserId"], $sql);
    	return  $sql;
    }
    
    /*
    public function Create($array)
    {
    	// 准备好SQL语句
    	$sql = $this->PrepareSQL($array, EditWordDao::Insert_Sentence);
    	//$sql = str_replace("{@Id}",$array["Id"], $sql);
    	
    	$log = new FileHelper();
    	$log->file = "log.txt";
    	$log->writeline(PHP_EOL .'========' . date("Y-m-d H:i:s"). PHP_EOL.  $sql);
    	
    	$db = Database::Connect();
    	$db->Execute($sql);
    	$db->Close();
    }
    */
    public function UpdateUser($array)
    {
    	$sql = $this->PrepareSQL($array, EditWordDao::Update_Sentence);
    	$sql = str_replace("{@UserId}",$array["UserId"], $sql);
    	$db = Database::Connect();
    	$db->Execute($sql);
    	$db->Close();
    }
    
    public function GetAll() {
    	// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
    	// 准备好SQL语句
    	$sql = EditWordDao::GetAll_Sentence;
    	
    	// 连接数据库，并读取结果集
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);
    	 
    	return $resultSet;
    }

    public function Delete( $id) {
    	$sql = str_replace("{@UserId}",$id, EditWordDao::DeleteUser_Sentence );
    	 
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
    		$sql = EditWordDao::GetEntryById;
    		$sql = str_replace('{@WordId}', $id, $sql);
    		if($this->db == null){
    			$db = Database::Connect();
    			return $db->GetSingleObject($sql);
    		}else{
    			return $this->db->GetSingleObjectWithMode($sql,false);
    		}    		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }    
    
    public function DeleteEntryById($entryId){
    	try {
    		$sql = EditWordDao::DeleteDictionaryA;
    		$sql = str_replace('{@WordId}', $entryId, $sql);
    		$sqlEntry = EditWordDao::DeleteEntry;
    		$sqlEntry = str_replace('{@ENTRYID}', $entryId, $sqlEntry);
    		$sql = "begin ".$sql.";".$sqlEntry."; end;";
    		$db = Database::Connect();
    		$db->Execute($sql);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }
    
    
    //分页取记录
    public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {    	
    	// 准备好SQL语句
    	$sql = str_replace("{@where}",$where, EditWordDao::GetPaged );
    	$sql = str_replace("{@orderBy}",$orderBy, $sql );
    	$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
    	$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );    	
    	// 连接数据库，并读取结果集
    	$db = Database::Connect();
    	$resultSet = $db->GetResultSet($sql);    	
    	return $resultSet;
    }
    
    //取总记录数
    public function GetCount( $where) {
    	$sql = str_replace("{@where}",$where, EditWordDao::GetCount );
    	$db = Database::Connect();    	
    	return $db->GetSingleVal($sql);    	
    }
    //取编辑记录数
    public function GetEditEntryCount($where){
    	$sqlEditEntryCount = EditWordDao::GetEditCount;
    	$sqlEditEntryCount = str_replace('{@where}', $where, $sqlEditEntryCount);
    	$db = Database::Connect();
    	return $db->GetSingleVal($sqlEditEntryCount);
    }   
    
    //增加词条
    public function AddDict($arrayEntry){
    	try {
    		$sql = EditWordDao::InsertDict;    		
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
    		$this->db->ExecuteWithMode($sql,false);    		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }
    
    //修改词条
    public function ModifyDict($arrayEntry){
    	try {
    		$sql = EditWordDao::UpdateDictById;
    		$sql = str_replace('{@Chinese}', $arrayEntry["Chinese"], $sql);
    		$sql = str_replace('{@Pinyin}', $arrayEntry["Pinyin"], $sql);
    		$sql = str_replace('{@WordId}', $arrayEntry["WordId"], $sql);
    		$sql = str_replace('{@Mongolian}', $arrayEntry["Mongolian"], $sql);
    		$sql = str_replace('{@MongolianLatin}', $arrayEntry["MongolianLatin"], $sql);
    		$sql = str_replace('{@MongolianCyrillic}', $arrayEntry["MongolianCyrillic"], $sql);
    		$sql = str_replace('{@Japanese}', $arrayEntry["Japanese"], $sql);
    		$sql = str_replace('{@English}', $arrayEntry["English"], $sql);
    		$sql = str_replace('{@Status}', $arrayEntry["Status"], $sql);
    		$this->db->ExecuteWithMode($sql,false);    		
    	}catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }
    
    //创建词条
    public function Create($arrayEntry){ 
    	try {
    		$db = Database::Connect();
    		$this->db = $db;
    		$this->AddDict($arrayEntry);//字典表
    		$wordid = $db->GetSingleValWithMode("select DictionaryA_WordId.currval from dual", false);//取自增加id
    		$editTaskDao = new EditTaskDao();
    		$editTaskDao->db =$db;
    		$editTaskDao->InsertEditItem($wordid, $arrayEntry["DataPackageId"], 2);//数据包明细  状态为已处理
    		$db->Commit();
    		$db->Close();
    		return $wordid;
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }  
    //更新词条
    public function Update($arrayEntry){
    	try {
    		$db = Database::Connect();
    		$this->db = $db;
    		$this->WriteWordLog($arrayEntry);
    		$this->ModifyDict($arrayEntry);//字典表    		 		
    		$editTaskDao = new EditTaskDao();
    		$editTaskDao->db =$db;
    		$editTaskDao->UpdateEditItem($arrayEntry["WordId"],$arrayEntry["DataPackageId"],2);
    		$db->Commit();
    		$db->Close();
    		$this->db=null;    		   		   		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }     
    
    //历史
    public function GetEntryTop10($userId){
    	try {
	    	$sql = EditWordDao::GetDictTop10;
	    	$sql = str_replace('{@userid}', $userId, $sql);
	    	$db = Database::Connect();
	    	return $db->GetResultSet($sql);
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }

    private  function WriteWordLog($arrayEntry){
    	try {
    		$wordDao = new WordDao();
    		$word = $this->GetEntryById($arrayEntry["WordId"]);
    		if($word){
    			$message = $wordDao->Diff($word, $arrayEntry);
    			$logDao = new LogDao();
    			$sql = $logDao->GetLogSQL(4, 3, 301, '录入人员修改词条:'.$arrayEntry['Chinese'], $message, '', $arrayEntry["WordId"]);
    			$this->db->ExecuteWithMode($sql,false);
    		}    		
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}    	
    }    
}
?>