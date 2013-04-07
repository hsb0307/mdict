<?php
/*
 *录入任务 
 */
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';

class EditTaskDao{	
	const Select = ' Select e.PACKAGEID "PackageId",e.PACKAGENAME "PackageName",e.USERID "UserId",e.Status "Status",e.CREATEDATE "CreateDate",u.USERNAME "Username" From EDITPACKAGE  e Inner Join USERS u On e.USERID = u.USERID;';
	const GetDataPackageByUserId = "select packageid,packagename from editpackage where USERID = '{@USERID}' and status = 0 order by CREATEDATE";
		
	//录入数据包数
	const GetCount = 'SELECT  COUNT(*) FROM EditPackage u WHERE Status < 999 {@where}';
	//增加数据包
	const InsertEditPackage = "insert into editpackage (packagename, userid) values ('{@packagename}', {@userid})";
	//批量插入明细表
	const InsertEditItems = "INSERT INTO edititems (packageid, wordid) SELECT {@CurrentValue},t.WordId FROM
							(SELECT WordId  FROM DictionaryA WHERE Status = 0 and SourceDictionary = {@SourceDictionary} ORDER BY  nlssort(Chinese,'NLS_SORT = SChinese_Pinyin_M')) t
							WHERE  rownum <= {@rownum}";
	
	
	const ExistWord_Sentence ='SELECT  COUNT(WordId) FROM DictionaryA WHERE Status = {@Status} AND SourceDictionary = {@SourceDictionary}';
	
	// 修改字典词条的状态为：录入中
	const UpdateDictionaryaStatus = "Update dictionarya set Status = {@Status} where WordId in (select wordid from edititems where packageid = '{@CurrentValue}')";
	const UpdateDictionaryaStatus_Sentence = "UPDATE dictionarya SET Status = {@NewStatus} WHERE Status = {@OldStatus} AND WordId IN (SELECT Wordid FROM EditItems WHERE PackageId = '{@PackageId}' AND Status = {@ItemStatus} )";
	//数据包自增id
	const SelectCurrentPackageId = 'select EditPackage_PackageId.currval from dual';
	//数据包数
	const GetPackageCount = "SELECT COUNT(*) FROM editpackage WHERE userid = {@userid}";
	const GetPackageCountByWhere = "SELECT COUNT(*) FROM editpackage WHERE userid = {@userid} and '{@where}' ";
	//数据包分页
	const GetPaged_Sentence = 'SELECT  PACKAGEID "PackageId", PACKAGENAME "PackageName", USERID "UserId", Status "Status", to_char(CREATEDATE,\'yyyy-mm-dd hh24:mi:ss\') "CreateDate", USERNAME "Username", RealName "RealName",total,utotal FROM ( SELECT t.*, rownum r FROM (
							SELECT  e.PACKAGEID, e.PACKAGENAME, e.USERID, e.Status, e.CREATEDATE, u.USERNAME, u.RealName,
							(select count(*) from edititems where edititems.packageid = e.PACKAGEID) as total,
							(select count(*) from edititems where edititems.packageid = e.PACKAGEID and edititems.status = 2) as utotal
			 				From EDITPACKAGE  e, USERS  u Where e.USERID = u.USERID AND e.Status < 999 {@where} Order BY {@orderBy}) t
							WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	//插入单条明细
	const InsertEditItem = "INSERT INTO edititems (wordid,packageid,status) Values ('{@wordid}','{@packageid}',{@status})";	
	//提交录入数据包 ，更新状态为：已提交
	const UpdateEditPackage = "update editpackage set status = {@newstatus} where packageid = {@packageid} and status = {@oldstatus}";
	const UpdateEditItems = "update edititems set status = {@newstatus},modifieddate = sysdate where packageid = {@packageid} and status = {@oldstatus}" ;
	// 更新录入包明细的状态为：已提交
	const UpdateEditItem = "update edititems set status = {@status} where wordid = '{@wordid}' and packageid = '{@packageid}'";
	const Insert_Sentence = 'INSERT INTO Logs ( UserId, CategoryId, ModuleId, OperationId, OperationName, ContentText, IPAddress, ObjectId, Description )  VALUES ({@UserId}, {@CategoryId}, {@ModuleId}, {@OperationId}, \'{@OperationName}\', \'{@ContentText}\', \'{@IPAddress}\', {@CurrentValue}, \'{@Description}\' ) ';
	public $db = null;
		
	//创建录入数据包
	function Create($userId,$category,$packName,$applyCount) {

		$sqlExist = str_replace('{@SourceDictionary}', $category, EditTaskDao::ExistWord_Sentence);
		$sqlExist = str_replace('{@Status}', 0, $sqlExist);//4
		$dbExist = Database::Connect();
		$exist = $dbExist->GetSingleVal($sqlExist);
		if($exist > 0){			
			$sqlPackage = EditTaskDao::InsertEditPackage;
			if(!empty($packName)){
				$sqlPackage = str_replace('{@packagename}', $packName, $sqlPackage);
			}else{
				//录入员
				$userDao = new UserDao();
				$user = $userDao->GetById($userId);
				//数据包数量
				$count = EditTaskDao::GetCountByUser($userId);
				//数据包
				$sqlPackage = str_replace('{@packagename}', $user->RealName.'的第'.($count + 1). '个数据包', $sqlPackage);
			}			
			$sqlPackage = str_replace('{@userid}', $userId, $sqlPackage);		
			//包明细		
			$sqlDict = EditTaskDao::UpdateDictionaryaStatus;
			$sqlDict = str_replace('{@Status}', 2, $sqlDict);// 修改字典词条的状态为：录入中
			//日志			
			$sqlLog =  EditTaskDao::Insert_Sentence;// $logDao->GetLogSQL(4, 2, 201, "录入人员提交数据包".$packName, "录入人员提交数据包".$packName, '');
			$sqlLog = str_replace ('{@UserId}', $userId, $sqlLog );// $_SESSION["UserId"]
			$sqlLog = str_replace ('{@CategoryId}', 4, $sqlLog );
			$sqlLog = str_replace ('{@ModuleId}', 2, $sqlLog );
			$sqlLog = str_replace ('{@OperationId}', 201, $sqlLog );			
			$sqlLog = str_replace ('{@OperationName}', "录入人员分配数据包".$packName, $sqlLog );
			$sqlLog = str_replace ('{@ContentText}', "录入人员分配数据包".$packName, $sqlLog );
			$sqlLog = str_replace ('{@IPAddress}', GetIP(), $sqlLog );			
			$sqlLog = str_replace ('{@Description}', '', $sqlLog );
						
			$sql = 'begin '.EditTaskDao::InsertEditItems.';'.$sqlDict.';'.$sqlLog.';  end;';
			$sql = str_replace('{@rownum}', $applyCount, $sql);			
			$sql = str_replace('{@SourceDictionary}', $category, $sql);			
			$db = Database::Connect();
			$db->ExecuteWithTrans($sqlPackage, EditTaskDao::SelectCurrentPackageId, $sql);		
			$db->Close();
			return 1;
		}else{
			return 0;
		}
	}
	//录入员数据包数量
	function GetCountByUser($userId){
		$sql = EditTaskDao::GetPackageCount;
		$sql = str_replace('{@userid}', $userId, $sql);
		$db = Database::Connect();
		$count = $db->GetSingleVal($sql);
		return $count==null?0:$count;
	}
	//数据包分页
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {		
		// 准备好SQL语句
		$sql = str_replace("{@where}",$where, EditTaskDao::GetPaged_Sentence );
		$sql = str_replace("{@orderBy}",$orderBy, $sql );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
		 
		// 连接数据库，并读取结果集
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
		 
		return $resultSet;
	}
	
	//根据条件取数据包数
	public function GetCount( $where) {
		$sql = str_replace("{@where}",$where, EditTaskDao::GetCount );
		$db = Database::Connect();		
		return $db->GetSingleVal($sql);		
	}
	
	//可以分配的词条数
	function GetWordCount($category){
		$sqlExist = str_replace('{@SourceDictionary}', $category, EditTaskDao::ExistWord_Sentence);
		$sqlExist = str_replace('{@Status}', 0, $sqlExist);//4
		$dbExist = Database::Connect();
		return $dbExist->GetSingleVal($sqlExist);
	}
	
	// 提交录入数据包，
	// 1、更新录入数据包的状态为已提交
	// 2、更新数据包明细的状态为已提交
	public function Update($packageId,$packageName){
		try {
			//1.更新词典字中词条状态
			//数据包明细中已经处理的
			$updateDictionaryaStatus = EditTaskDao::UpdateDictionaryaStatus_Sentence;
			$updateDictionaryaStatus = str_replace('{@OldStatus}', 2, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@NewStatus}', 4, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@ItemStatus}', 2, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@PackageId}', $packageId, $updateDictionaryaStatus);
			//数据包明细中没有处理的
			$updateDictionaryaStatus1 = EditTaskDao::UpdateDictionaryaStatus_Sentence;
			$updateDictionaryaStatus1 = str_replace('{@OldStatus}', 2, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@NewStatus}', 0, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@ItemStatus}', 0, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@PackageId}', $packageId, $updateDictionaryaStatus1);
			//2.更新数据包明细
			//数据包明细中已经处理的
			$updateEditItems = EditTaskDao::UpdateEditItems;
			$updateEditItems = str_replace('{@newstatus}', 4, $updateEditItems);
			$updateEditItems = str_replace('{@oldstatus}', 2, $updateEditItems);
			$updateEditItems = str_replace('{@packageid}', $packageId, $updateEditItems);
			//数据包明细中没有处理的
			$updateEditItems1 = EditTaskDao::UpdateEditItems;
			$updateEditItems1 = str_replace('{@newstatus}', 6, $updateEditItems1);
			$updateEditItems1 = str_replace('{@oldstatus}', 0, $updateEditItems1);
			$updateEditItems1 = str_replace('{@packageid}', $packageId, $updateEditItems1);
			//3.更新数据包状态
			$updateEditPackage = EditTaskDao::UpdateEditPackage;
			$updateEditPackage = str_replace('{@newstatus}', 2, $updateEditPackage);
			$updateEditPackage = str_replace('{@oldstatus}', 0, $updateEditPackage);
			$updateEditPackage = str_replace('{@packageid}', $packageId, $updateEditPackage);
			//写日志
			$logDao = new LogDao();
			$sqlLog = $logDao->GetLogSQL(4, 2, 202, "录入人员提交数据包".$packageName, "录入人员提交数据包".$packageName, '', $packageId);
			
			$sql = "begin ".$updateDictionaryaStatus.";".$updateDictionaryaStatus1.";".$updateEditItems.";".$updateEditItems1.";".$updateEditPackage.";".$sqlLog.";  end;";
			$db = Database::Connect();
			$db->Execute( $sql);
			$db->Close();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}	
	}
	
	public function Cancel($packageId,$packageName){
		try {
			//1.更新词典字中词条状态
			//数据包明细中已经处理的
			$updateDictionaryaStatus = EditTaskDao::UpdateDictionaryaStatus_Sentence;
			$updateDictionaryaStatus = str_replace('{@OldStatus}', 2, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@NewStatus}', 4, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@ItemStatus}', 2, $updateDictionaryaStatus);
			$updateDictionaryaStatus = str_replace('{@PackageId}', $packageId, $updateDictionaryaStatus);
			//数据包明细中没有处理的
			$updateDictionaryaStatus1 = EditTaskDao::UpdateDictionaryaStatus_Sentence;
			$updateDictionaryaStatus1 = str_replace('{@OldStatus}', 2, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@NewStatus}', 0, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@ItemStatus}', 0, $updateDictionaryaStatus1);
			$updateDictionaryaStatus1 = str_replace('{@PackageId}', $packageId, $updateDictionaryaStatus1);
			//2.更新数据包明细
			//数据包明细中已经处理的
			$updateEditItems = EditTaskDao::UpdateEditItems;
			$updateEditItems = str_replace('{@newstatus}', 4, $updateEditItems);
			$updateEditItems = str_replace('{@oldstatus}', 2, $updateEditItems);
			$updateEditItems = str_replace('{@packageid}', $packageId, $updateEditItems);
			//数据包明细中没有处理的
			$updateEditItems1 = EditTaskDao::UpdateEditItems;
			$updateEditItems1 = str_replace('{@newstatus}', 6, $updateEditItems1);
			$updateEditItems1 = str_replace('{@oldstatus}', 0, $updateEditItems1);
			$updateEditItems1 = str_replace('{@packageid}', $packageId, $updateEditItems1);
			//3.更新数据包状态
			$updateEditPackage = EditTaskDao::UpdateEditPackage;
			$updateEditPackage = str_replace('{@newstatus}', 4, $updateEditPackage);
			$updateEditPackage = str_replace('{@oldstatus}', 0, $updateEditPackage);
			$updateEditPackage = str_replace('{@packageid}', $packageId, $updateEditPackage);
			//写日志
			$logDao = new LogDao();
			$sqlLog = $logDao->GetLogSQL(4, 2, 203, "录入人员撤销数据包".$packageName, "录入人员撤销数据包".$packageName, '', $packageId);
				
			$sql = "begin ".$updateDictionaryaStatus.";".$updateDictionaryaStatus1.";".$updateEditItems.";".$updateEditItems1.";".$updateEditPackage.";".$sqlLog.";  end;";
			$db = Database::Connect();
			$db->Execute( $sql);
			$db->Close();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	public function GetDataPackageByUserId($userId){
		$sqlDataPackage = EditTaskDao::GetDataPackageByUserId;
		$sqlDataPackage = str_replace('{@USERID}', $userId, $sqlDataPackage);
		$db = Database::Connect();
		$dataPackages = $db->GetResultSet($sqlDataPackage);
		return $dataPackages;
	}
	
	//插入数据包明细
	public function InsertEditItem($wordid,$packageid,$status){
		$sql = EditTaskDao::InsertEditItem;
		$sql = str_replace('{@wordid}', $wordid, $sql);
		$sql = str_replace('{@packageid}', $packageid, $sql);
		$sql = str_replace('{@status}', $status, $sql);
		$this->db->ExecuteWithMode($sql,false);		
	}
	
	//更新数据包明细
	public function UpdateEditItem($wordid,$packageid,$status){
		$sql = EditTaskDao::UpdateEditItem;
		$sql = str_replace('{@wordid}', $wordid, $sql);
		$sql = str_replace('{@packageid}', $packageid, $sql);
		$sql = str_replace('{@status}', $status, $sql);
		$this->db->ExecuteWithMode($sql,false);
	}	
}


?>