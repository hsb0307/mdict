<?php
// $ROOT = "/Dict";

require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/FileHelper.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/LogDao.php';
class UserDao {
	const GetUserByID_Sentence = 'SELECT 
    		UserId as "UserId", 
    		UserName as "UserName", 
    		Password as "Password", 
    		PasswordQuestion as "PasswordQuestion", 
    		PasswordAnswer as "PasswordAnswer", 
    		RealName as "RealName", 
    		Gender as "Gender", 
    		to_char(Birthday,\'yyyy-mm-dd\') as "Birthday", 
    		PINCodes as "PINCodes", 
    		Mobile as "Mobile", 
    		Telephone as "Telephone", 
    		Company as "Company", 
    		Email as "Email", 
    		QQ, 
			WordCategory "WordCategory",
    		CreateDate as "CreateDate", 
    		IsApproved as "IsApproved", 
    		RoleId as "RoleId", 
    		Description as "Description"  
    		FROM Users WHERE UserId = \'{@UserId}\'';
	const GetAll_Sentence = 'SELECT UserId, UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, WordCategory "WordCategory", CreateDate, IsApproved, RoleId, Description FROM Users ';
	const GetPaged_Sentence = 'SELECT UserId as "UserId", 
    		UserName as "UserName", 
    		Password as "Password", 
    		PasswordQuestion as "PasswordQuestion", 
    		PasswordAnswer as "PasswordAnswer", 
    		RealName as "RealName", 
    		Gender as "Gender", 
    		to_char(Birthday,\'yyyy-mm-dd\') as "Birthday", 
    		PINCodes as "PINCodes", 
    		Mobile as "Mobile", 
    		Telephone as "Telephone", 
    		Company as "Company", 
    		Email as "Email", 
    		QQ, 
			WordCategory as "WordCategory",
    		CreateDate as "CreateDate", 
    		IsApproved as "IsApproved", 
    		RoleId as "RoleId", 
    		Description as "Description" FROM ( SELECT t.*, rownum r FROM (SELECT UserId, UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, CreateDate, WordCategory, IsApproved, RoleId, Description FROM Users {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	// {where} order by {orderBy} limit {startRowIndex}, {pageSize} ";
	const GetCount_Sentence = "select 	count(*)  from Users {@where} ";
	const DeleteUser_Sentence = "DELETE FROM Users where UserId = {@UserId} ";
	const Update_Sentence = "UPDATE Users SET UserName = '{@UserName}', Password = '{@Password}', PasswordQuestion = '{@PasswordQuestion}', PasswordAnswer = '{@PasswordAnswer}', RealName = '{@RealName}', Gender = '{@Gender}', Birthday = to_date('{@Birthday}','yyyy-mm-dd'), PINCodes = '{@PINCodes}', Mobile = '{@Mobile}', Telephone = '{@Telephone}', Company = '{@Company}', Email = '{@Email}', QQ = '{@QQ}', WordCategory = {@WordCategory}, IsApproved = {@IsApproved}, Description = '{@Description}', RoleId = {@RoleId} WHERE UserId = {@UserId} ";
	// const Insert_Sentence = "INSERT INTO Users (UserId, UserName, Password,
	// PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes,
	// Mobile, Telephone, Company, Email, QQ, CreateDate, IsApproved, RoleId,
	// Description) VALUES ({@UserId}, {@UserName}, {@Password},
	// {@PasswordQuestion}, {@PasswordAnswer}, {@RealName}, {@Gender},
	// {@Birthday}, {@PINCodes}, {@Mobile}, {@Telephone}, {@Company}, {@Email},
	// {@QQ}, {@CreateDate}, {@IsApproved}, {@RoleId}, {@Description}) ";
	const Insert_Sentence = "INSERT INTO Users (UserName, Password, PasswordQuestion, PasswordAnswer, RealName, Gender, Birthday, PINCodes, Mobile, Telephone, Company, Email, QQ, WordCategory, IsApproved, RoleId, Description)  VALUES ('{@UserName}', '{@Password}', '{@PasswordQuestion}', '{@PasswordAnswer}', '{@RealName}', '{@Gender}', to_date('{@Birthday}','yyyy-mm-dd'), '{@PINCodes}', '{@Mobile}', '{@Telephone}', '{@Company}', '{@Email}', '{@QQ}', {@WordCategory}, {@IsApproved}, {@RoleId}, '{@Description}') ";
	const GetMaxUserId_Sentence = "select 	max(UserId)  from Users {where} ";
	const IsUserExist_Sentence = "select count(UserId)  from Users where UserName = '{@UserName}' ";
	const ValidateUser_Sentence = 'select RoleId "RoleId", UserId "UserId", RealName "RealName", IsApproved "IsApproved", WordCategory "WordCategory"  from Users where UserName = \'{@UserName}\' and Password = \'{@Password}\' ';
	const HasEditPackageExist_Sentence = "select count(UserId)  from EditPackage where Status = 0  AND UserId = '{@UserId}' ";
	const HasRevisePackageExist_Sentence = "select count(UserId)  from RevisePackage where  Status = 0  AND  UserId = '{@UserId}' ";
	const HasApprovePackageExist_Sentence = "select count(UserId)  from ApprovePackage where Status = 0  AND  UserId = '{@UserId}' ";
	
	const UpdateIsApproval_Sentence = 'UPDATE Users SET IsApproved = {@IsApproved} WHERE UserId = {@UserId} ';
	
	//当前用户编辑数据包
	const CurrentEditPackage = "SELECT PackageId FROM ( SELECT t.*, rownum r FROM (SELECT p.PackageId  FROM editPackage p, editItems i, Users u 
								WHERE u.Userid = p.Userid AND  p.PackageId = i.PackageId AND u.UserId = '{@UserId}' AND  p.Status < 2
								ORDER BY i.ModifiedDate DESC) t WHERE rownum <= 1) B  WHERE r > 0";
	const CurrentRevisePackage = "SELECT PackageId FROM ( SELECT t.*, rownum r FROM (SELECT p.PackageId  FROM RevisePackage p, ReviseItems i, Users u
								WHERE u.Userid = p.Userid AND  p.PackageId = i.PackageId AND u.UserId = '{@UserId}' AND  p.Status < 2
								ORDER BY i.ModifiedDate DESC) t WHERE rownum <= 1) B  WHERE r > 0";	
	const CurrentApprovePackage = "SELECT PackageId FROM ( SELECT t.*, rownum r FROM (SELECT p.PackageId  FROM ApprovePackage p, ApproveItems i, Users u
								WHERE u.Userid = p.Userid AND  p.PackageId = i.PackageId AND u.UserId = '{@UserId}' AND  p.Status < 2
								ORDER BY i.ModifiedDate DESC) t WHERE rownum <= 1) B  WHERE r > 0";
	
	public function HasPackageExist($id, $roleId) {
		if($roleId > 3) {
			return 0;
		}
		$sql = null;
		switch ($roleId) {
			case 1 :
				$sql = str_replace ( "{@UserId}", $id, UserDao::HasEditPackageExist_Sentence );
				break;
			case 2 :
				$sql = str_replace ( "{@UserId}", $id, UserDao::HasRevisePackageExist_Sentence );
				break;
			case 3 :
				$sql = str_replace ( "{@UserId}", $id, UserDao::HasApprovePackageExist_Sentence );
				break;
		}
		
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}
	
	//当前数据包
	public function CurrentEditPackage($userId,$roleId){
		if($roleId < 1 || $roleId>3){
			return 0;
		}		
		switch ($roleId) {
			case 1 :
				$sql = str_replace ( "{@UserId}", $userId, UserDao::CurrentEditPackage );
				break;
			case 2 :
				$sql = str_replace ( "{@UserId}", $userId, UserDao::CurrentRevisePackage );
				break;
			case 3 :
				$sql = str_replace ( "{@UserId}", $userId, UserDao::CurrentApprovePackage );
				break;
		}		
		$db = Database::Connect();
		$packageId = $db->GetSingleVal($sql);
		return $packageId;
	}
	
	public function GetLogSQL($operationId, $operationName, $message, $userId){
		//session_start();
		$sql = str_replace ('{@UserId}', $userId, LogDao::Insert_Sentence );// $_SESSION["UserId"]
		$sql = str_replace ('{@CategoryId}', 4, $sql );
		$sql = str_replace ('{@ModuleId}', 1, $sql );
		$sql = str_replace ('{@OperationId}', $operationId, $sql );
		$sql = str_replace ('{@OperationName}', $operationName, $sql );
		$sql = str_replace ('{@ContentText}', $message, $sql );
		$sql = str_replace ('{@IPAddress}', GetIP(), $sql );
		$sql = str_replace ('{@ObjectId}', $userId, $sql );//$_SESSION["UserId"]
		$sql = str_replace ('{@Description}', '', $sql );
	
		return $sql;
	}
	
	public function ValidateUser($username, $password) {
		$sql = str_replace ( "{@UserName}", $username, UserDao::ValidateUser_Sentence );
		$sql = str_replace ( "{@Password}", $password, $sql );
		
		//$logSQL = GetLogSQL('登录系统', '');
		$db = Database::Connect ();		
		$user = $db->GetSingleObject ( $sql );
		if(!$user) {
			return null;
		}

		$array = array("UserId"=>$user->UserId, 
				'CategoryId'=>1,
				"ModuleId"=>1, 
				"OperationId"=>103,
				"OperationName"=>$user->RealName.'登录系统',
				"ContentText"=>$user->RealName.'登录系统', 
				"IPAddress"=>GetIP(), 
				'ObjectId'=>$user->UserId,
				"Description"=> isset($_SERVER) ? Encode( $_SERVER['HTTP_USER_AGENT']):'');
		$logDao = new LogDao();
		$logDao->Create($array);
		
		return $user;
	}
	public function IsUserExist($username) {
		$sql = str_replace ( "{@UserName}", $username, UserDao::IsUserExist_Sentence );
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}
	public function GetMaxUserId($where) {
		$sql = str_replace ( "{where}", $where, UserDao::GetMaxUserId_Sentence );
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}
	public function GetById($id) {
		$db = Database::Connect ();
		$sql = UserDao::GetUserByID_Sentence;
		$sql = str_replace ( "{@UserId}", $id, $sql );
		$user = $db->GetSingleObject ( $sql );
		$db->Close ();
		return $user;
	}
	public function Update($existingRow) {
		$db = Database::Connect ();
		
		$user = $db->UpdateByPk ( 'Users', $existingRow );
		$db->Close ();
		return $user;
	}
	public function PrepareSQL($array, $sql) {
		$sql = str_replace ( "{@UserName}", $array ["UserName"], $sql );
		$sql = str_replace ( "{@Password}", $array ["Password"], $sql );
		$sql = str_replace ( "{@PasswordQuestion}", $array ["PasswordQuestion"], $sql );
		$sql = str_replace ( "{@PasswordAnswer}", $array ["PasswordAnswer"], $sql );
		$sql = str_replace ( "{@RealName}", $array ["RealName"], $sql );
		$sql = str_replace ( "{@Gender}", $array ["Gender"], $sql );
		$sql = str_replace ( "{@Birthday}", $array ["Birthday"], $sql );
		$sql = str_replace ( "{@PINCodes}", $array ["PINCodes"], $sql );
		$sql = str_replace ( "{@Mobile}", $array ["Mobile"], $sql );
		$sql = str_replace ( "{@Telephone}", $array ["Telephone"], $sql );
		$sql = str_replace ( "{@Company}", $array ["Company"], $sql );
		$sql = str_replace ( "{@Email}", $array ["Email"], $sql );
		$sql = str_replace ( "{@QQ}", $array ["QQ"], $sql );
		$sql = str_replace ( "{@WordCategory}", $array ["WordCategory"], $sql );
		$sql = str_replace ( "{@IsApproved}", $array ["IsApproved"], $sql );
		$sql = str_replace ( "{@RoleId}", $array ["RoleId"], $sql );
		$sql = str_replace ( "{@Description}", $array ["Description"], $sql );
		// 20121112peij判断userid是否存在
		if (isset ( $array ["UserId"] )) {
			$sql = str_replace ( "{@UserId}", $array ["UserId"], $sql );
		}
		return $sql;
	}
	public function Create($array) {
		// 准备好SQL语句
		$sql = $this->PrepareSQL ( $array, UserDao::Insert_Sentence );
		// $sql = str_replace("{@Id}",$array["Id"], $sql);
		
		//$log = new FileHelper ();
		// 20121112peij修改文件路径$_SERVER["DOCUMENT_ROOT"]
		//$log->file = dirname ( dirname ( dirname ( __FILE__ ) ) ) . "\\doc\\log.txt";
		//$log->writeline ( PHP_EOL . '========' . date ( "Y-m-d H:i:s" ) . PHP_EOL . $sql );
		
		$db = Database::Connect ();
		$userId = $db->InsertReturnSequence ( $sql, 'Users_UserId' );
		$db->Close ();
		
		return $userId;
	}
	public function UpdateUser($array) {
		$sql = $this->PrepareSQL ( $array, UserDao::Update_Sentence );
		$sql = str_replace ( "{@UserId}", $array ["UserId"], $sql );
		
		$sql2 = $this->GetLogSQL(104, "修改用户信息", $this->Diff($this->GetById($array ["UserId"]), $array), $array ["UserId"]);
		
		$db = Database::Connect ();
		$db->Execute2 ( $sql, $sql2 );
		//$db->Execute ( $sql2 );
		$db->Close ();
	}
	public function GetAll() {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B
		// WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = UserDao::GetAll_Sentence;
		
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
		
		return $resultSet;
	}
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B
		// WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace ( "{@where}", $where, UserDao::GetPaged_Sentence );
		$sql = str_replace ( "{@orderBy}", $orderBy, $sql );
		$sql = str_replace ( "{@endRowIndex}", $startRowIndex + $pageSize, $sql );
		$sql = str_replace ( "{@startRowIndex}", $startRowIndex, $sql );
		
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
		
		return $resultSet;
	}
	public function GetCount($where) {
		$sql = str_replace ( "{@where}", $where, UserDao::GetCount_Sentence );
		$db = Database::Connect ();
		// echo "ss";
		return $db->GetSingleVal ( $sql );
		// $row = mysql_fetch_object($result)
	}
	public function Delete($id) {
		$sql = str_replace ( "{@UserId}", $id, UserDao::DeleteUser_Sentence );
		
		$db = Database::Connect ();
		$db->Execute ( $sql );
		$db->Close ();
	}
	
	public function Approval($id) {
		$sql = str_replace ( "{@UserId}", $id, UserDao::UpdateIsApproval_Sentence );
		$sql = str_replace ( "{@IsApproved}", 1, $sql );
		$db = Database::Connect ();
		$db->Execute ( $sql );
		$db->Close ();
	}
	
	public function GetDesc() {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B
		// WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = "select column_name ColumnName,data_type DataType  from user_tab_columns where Table_Name='USERS'"; // "desc
		                                                                                                           // Users";
		                                                                                                           
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
		
		return $resultSet;
	}
	
	public function Diff($oldObj, $newObj) {
		$diff = '';
		if($oldObj->UserName != $newObj['UserName']) $diff .= ('UserName:'.$oldObj->UserName.'=>'.$newObj['UserName'].',');
		if($oldObj->Password != $newObj['Password']) $diff .= ('Password:'.$oldObj->Password.'=>'.$newObj['Password'].',');
		if($oldObj->PasswordQuestion != $newObj['PasswordQuestion']) $diff .= ('PasswordQuestion:'.$oldObj->PasswordQuestion.'=>'.$newObj['PasswordQuestion'].',');
		if($oldObj->PasswordAnswer != $newObj['PasswordAnswer']) $diff .= ('PasswordAnswer:'.$oldObj->PasswordAnswer.'=>'.$newObj['PasswordAnswer'].',');
		if($oldObj->RealName != $newObj['RealName']) $diff .= ('RealName:'.$oldObj->RealName.'=>'.$newObj['RealName'].',');
		if($oldObj->Gender != $newObj['Gender']) $diff .= ('Gender:'.$oldObj->Gender.'=>'.$newObj['Gender'].',');
		if($oldObj->Birthday != $newObj['Birthday']) $diff .= ('Birthday:'.$oldObj->Birthday.'=>'.$newObj['Birthday'].',');
		if($oldObj->PINCodes != $newObj['PINCodes']) $diff .= ('PINCodes:'.$oldObj->PINCodes.'=>'.$newObj['PINCodes'].',');
		if($oldObj->Mobile != $newObj['Mobile']) $diff .= ('Mobile:'.$oldObj->Mobile.'=>'.$newObj['Mobile'].',');
		if($oldObj->Telephone != $newObj['Telephone']) $diff .= ('Telephone:'.$oldObj->Telephone.'=>'.$newObj['Telephone'].',');
		if($oldObj->Company != $newObj['Company']) $diff .= ('Company:'.$oldObj->Company.'=>'.$newObj['Company'].',');
		if($oldObj->Email != $newObj['Email']) $diff .= ('Email:'.$oldObj->Email.'=>'.$newObj['Email'].',');
		if($oldObj->QQ != $newObj['QQ']) $diff .= ('QQ:'.$oldObj->QQ.'=>'.$newObj['QQ'].',');
		if($oldObj->WordCategory != $newObj['WordCategory']) $diff .= ('WordCategory:'.$oldObj->WordCategory.'=>'.$newObj['WordCategory'].',');
		if($oldObj->IsApproved != $newObj['IsApproved']) $diff .= ('IsApproved:'.$oldObj->IsApproved.'=>'.$newObj['IsApproved'].',');
		if($oldObj->RoleId != $newObj['RoleId']) $diff .= ('RoleId:'.$oldObj->RoleId.'=>'.$newObj['RoleId'].',');
		if($oldObj->Description != $newObj['Description']) $diff .= ('Description:'.$oldObj->Description.'=>'.$newObj['Description'].',');
		
		//$diff = str_replace("//", "////", $diff);
		//$diff = str_replace("'", "''", $diff);
		
		return $diff;
	}
	
}
?>