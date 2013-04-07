<?php
/*
 *日志管理数据访问类
*/
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/dao/WordDao.php';

class LogDao{
	const GetById_Sentence = ' FROM DictionaryA WHERE WordId = ';
	const GetAll_Sentence = 'SELECT LogId "LogId", UserId "UserId", CategoryId "CategoryId", ContentText "ContentText", IPAddress "IPAddress", CreateDate "CreateDate", Status "Status", Description "Description" FROM Logs ';
	const GetPaged_Sentence = 'SELECT LogId "LogId", UserId "UserId", CategoryId "CategoryId", ModuleId "ModuleId", OperationName "OperationName", ContentText "ContentText", IPAddress "IPAddress", to_char(CreateDate,\'yyyy/mm/dd hh24:mi:ss\') "CreateDate", Status "Status", ObjectId "ObjectId", Description "Description", RealName "RealName" FROM ( 
SELECT t.*, rownum r FROM (
SELECT LogId, l.UserId, CategoryId, ModuleId, OperationName, ContentText, IPAddress, l.CreateDate, l.Status, l.ObjectId, l.Description, u.RealName
FROM Logs l, Users u WHERE l.UserId = u.UserId {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'select 	count(*)  from Logs l, Users u WHERE l.UserId = u.UserId {@where}';
	
	const DeleteUser_Sentence =  "UPDATE Logs SET Status = 999 WHERE LogId = {@WordId} ";
	const Update_Sentence = '';//, Status = {@Status}
	const Insert_Sentence = 'INSERT INTO Logs ( UserId, CategoryId, ModuleId, OperationId, OperationName, ContentText, IPAddress, ObjectId, Description )  VALUES ({@UserId}, {@CategoryId}, {@ModuleId}, {@OperationId}, \'{@OperationName}\', \'{@ContentText}\', \'{@IPAddress}\', {@ObjectId}, \'{@Description}\' ) ';
	
	public function GetLogSQL($categoryId, $moudleId, $operationId, $operationName, $message, $desc, $objectId=0){
		//session_start();
		if(!isset($_SESSION)){session_start();}
		$sql = str_replace ('{@UserId}', $_SESSION["UserId"], LogDao::Insert_Sentence );// $_SESSION["UserId"]
		$sql = str_replace ('{@CategoryId}', $categoryId, $sql );
		$sql = str_replace ('{@ModuleId}', $moudleId, $sql );
		$sql = str_replace ('{@OperationId}', $operationId, $sql );
		
		$sql = str_replace ('{@OperationName}', $operationName, $sql );
		$sql = str_replace ('{@ContentText}', $message, $sql );
		$sql = str_replace ('{@IPAddress}', GetIP(), $sql );
		$sql = str_replace ('{@ObjectId}', $objectId, $sql );
		$sql = str_replace ('{@Description}', $desc, $sql );
	
		return $sql;
	}
	
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// 准备好SQL语句
		$sql = str_replace ( "{@where}", $where, LogDao::GetPaged_Sentence );
		$sql = str_replace ( "{@orderBy}", $orderBy, $sql );
		$sql = str_replace ( "{@endRowIndex}", $startRowIndex + $pageSize, $sql );
		$sql = str_replace ( "{@startRowIndex}", $startRowIndex, $sql );
		//setcookie("currentSQL", $sql, time()+30, '/');
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );
	
		return $resultSet;
	}
	public function GetCount($where) {
		$sql = str_replace ( "{@where}", $where, LogDao::GetCount_Sentence );
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}
	
	public function PrepareSQL($array, $sql) {
		
		$sql = str_replace ('{@UserId}', $array ['UserId'], $sql );
		$sql = str_replace ('{@CategoryId}', $array ['CategoryId'], $sql );
		$sql = str_replace ('{@ModuleId}', $array ['ModuleId'], $sql );
		$sql = str_replace ('{@OperationId}', $array ['OperationId'], $sql );
		$sql = str_replace ('{@OperationName}', $array ['OperationName'], $sql );
		$sql = str_replace ('{@ContentText}', $array ['ContentText'], $sql );
		$sql = str_replace ('{@IPAddress}', $array ['IPAddress'], $sql );
		$sql = str_replace ('{@ObjectId}', $array ['ObjectId'], $sql );
		//$sql = str_replace ('{@CreateDate}', $array ['CreateDate'], $sql );
		//$sql = str_replace ('{@Status}', $array ['Status'], $sql );
		$sql = str_replace ('{@Description}', $array ['Description'], $sql );
		if (isset ( $array ["LogId"] )) {
			$sql = str_replace ( "{@LogId}", $array ["LogId"], $sql );
		}
		if (isset ( $array ["Status"] )) {
			$sql = str_replace ( "{@Status}", $array ["Status"], $sql );
		}
		return $sql;
	}
	public function Create($array) {
		// 准备好SQL语句
		$sql = $this->PrepareSQL ( $array, LogDao::Insert_Sentence );
		// $sql = str_replace("{@Id}",$array["Id"], $sql);
		$db = Database::Connect ();
		$userId = $db->Execute ( $sql );
		$db->Close ();
	
		return $userId;
	}
}
?>	