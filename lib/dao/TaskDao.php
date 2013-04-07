<?php
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';


class TaskDao{
	//const GetEntryId_Sentence = "SELECT WordId,'{@DATAPACKID}' as DATAPACKID,null FROM DICTIONARYA WHERE ROWNUM <= 50 AND Status = 0 ORDER BY CREATEDDATE";
	const InsertDataPackage_Sentence = "INSERT INTO DATAPACKAGE (DATAPACKAGENAME,ISSUBMIT,DATAPACKAGEID,PACKAGETYPE,USERID) VALUES ('{@DATAPACKAGENAME}','{@ISSUBMIT}','{@DATAPACKAGEID}','{@PACKAGETYPE}','{@USERID}')";
	const InsertEntry_Sentence = "INSERT INTO ENTRY (ENTRYID,DATAPACKAGEID,EDITDATE) SELECT WordId,'{@DATAPACKAGEID}' as DATAPACKAGEID,null FROM DICTIONARYA WHERE ROWNUM <= 500 AND Status = 0 ORDER BY CREATEDDATE";
	const GetUser_Sentence = "SELECT UserId,UserName,RealName FROM Users WHERE USERID = '{@UserId}'";
	const GetDataPackageCount = "SELECT COUNT(*) FROM DATAPACKAGE WHERE USERID = '{@UserId}'";
	const UpdateDictionaryaStatus = "Update dictionarya set Status = 1 where WordId in (select entryid from entry where DATAPACKAGEID = '{@DATAPACKAGEID}')";
	const GetDataPackageByUserId = "select DATAPACKAGEID,DATAPACKAGENAME from DATAPACKAGE where USERID = '{@USERID}' order by CREATEDATE";
	
	
	
	
	
	function CreateDataPackByUserId($userId) {
		try {
			$dataPackageId = GUID();
			//操作员
			$sqlUser = TaskDao::GetUser_Sentence;
			$sqlUser = str_replace('{@UserId}', $userId, $sqlUser);
			$db = Database::Connect();
			$user = $db->GetSingleObject($sqlUser);
			//用户数据包总数
			$sqlDataPackageCount = TaskDao::GetDataPackageCount;
			$sqlDataPackageCount = str_replace('{@UserId}', $user->USERID, $sqlDataPackageCount);
			$db = Database::Connect();
			$count = $db->GetSingleVal($sqlDataPackageCount);
			$count = $count==null?0:$count;
			//数据包
			$sql = TaskDao::InsertDataPackage_Sentence;
			$sql = str_replace('{@DATAPACKAGEID}', $dataPackageId, $sql);
			$sql = str_replace('{@DATAPACKAGENAME}', $user->REALNAME."的第".++$count."数据包", $sql);
			$sql = str_replace('{@USERID}', $user->USERID, $sql);
			$sql = str_replace('{@PACKAGETYPE}', 0, $sql);
			$sql = str_replace('{@ISSUBMIT}', 0, $sql);
			//词条
			$sqlEntry = TaskDao::InsertEntry_Sentence;
			$sqlEntry = str_replace('{@DATAPACKAGEID}',$dataPackageId, $sqlEntry);
			//更新字典状态
			$sqlDict = TaskDao::UpdateDictionaryaStatus;
			$sqlDict = str_replace('{@DATAPACKAGEID}',$dataPackageId, $sqlDict);
			$sql = "begin ".$sql.";".$sqlEntry.";".$sqlDict.";"." end;";
			
			$db = Database::Connect();
			$db->Execute($sql);
			$db->Close();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}				
	}

	function GetDataPackageByUserId($userId){
		$sqlDataPackage = TaskDao::GetDataPackageByUserId;
		$sqlDataPackage = str_replace('{@USERID}', $userId, $sqlDataPackage);
		$db = Database::Connect();
		$dataPackages = $db->GetResultSet($sqlDataPackage);		
		return $dataPackages;
	}
}	
?>	