<?php
/*
 *校对数据包管理 
 */
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/dao/WordDao.php';
//require_once '../../lib/dao/LogDao.php';


class RevisePackageDao{
	const GetPackageCount = "SELECT COUNT(*) FROM RevisePackage WHERE UserId = {@UserId} {@Where} ";	
	const Insert_Sentence = 'INSERT INTO RevisePackage (PackageName, UserId) VALUES (\'{@PackageName}\', {@UserId})';
	const SelectCurrentPackageId = 'select RevisePackage_PackageId.currval from dual';
	// nlssort(Chinese,\'NLS_SORT = SChinese_Pinyin_M\')
	const InsertItems_Sentence = 'INSERT INTO ReviseItems (PackageId, WordId)
		SELECT {@CurrentValue}, t.WordId FROM (SELECT  WordId FROM DictionaryA WHERE Status = {@OldStatus} AND WordCategory = {@WordCategory} ORDER BY  Chinese) t WHERE  rownum <= {@ItemCount} ';
	const UpdateWord_Sentence = 'UPDATE DictionaryA SET Status = {@NewStatus} WHERE WordId IN (SELECT WordId FROM ReviseItems WHERE PackageId = {@CurrentValue})';
	const InsertItem_Sentence = 'INSERT INTO ReviseItems (PackageId, WordId, Status)  VALUES ({@PackageId}, {@CurrentValue}, {@Status}) ';
	// {@CurrentValue}
	const ExistWord_Sentence ='SELECT  COUNT(*) FROM DictionaryA WHERE WordCategory = {@WordCategory} AND Status = {@Status}';
	const InsertPackage ='DECLARE
	last_PackageId NUMBER(10,0);
BEGIN
	INSERT INTO RevisePackage (PackageName, UserId) VALUES (\'{@PackageName}\', {@UserId}) RETURNING PackageId INTO last_PackageId;
	INSERT INTO ReviseItems (PackageId, WordId)
		SELECT last_PackageId, t.WordId FROM (SELECT  WordId FROM DictionaryA WHERE Status = {@OldStatus} AND SourceDictionary = {@SourceDictionary} ORDER BY  nlssort(Chinese,\'NLS_SORT = SChinese_Pinyin_M\')) t WHERE  rownum <= 500;
	UPDATE DictionaryA SET Status = {@NewStatus} WHERE WordId IN (SELECT WordId FROM ReviseItems WHERE PackageId = last_PackageId)
END;';
	/*
	const InsertEditItems = "
	DECLARE
	last_rowid number;
	BEGIN
	insert into RevisePackage (packagename, userid) values ('admin的第1个数据包', 101) RETURNING packageid INTO last_rowid;
	INSERT INTO edititems (packageid, wordid, status)
	SELECT last_rowid,a,0 FROM DICTIONARYA WHERE ROWNUM <= 500 AND Status = 0 ORDER BY CREATEDDATE;
	End;";
	*/
	const GetPaged_Sentence = 'SELECT  PackageId "PackageId", PackageName "PackageName", UserId "UserId", Status "Status", to_char(CreateDate,\'yyyy/mm/dd hh24:mi:ss\') "CreateDate", Username "Username", RealName "RealName", Total "Total", Unhandled "Unhandled", Handled "Handled" FROM ( SELECT t.*, rownum r FROM (
SELECT  e.PackageId, e.PackageName, e.UserId, e.Status, e.CreateDate, u.Username, u.RealName,
(SELECT  COUNT(*) FROM ReviseItems WHERE ReviseItems.PackageId = e.PackageId  AND Status > -1 AND Status < 10) as Total
,(SELECT  COUNT(*) FROM ReviseItems WHERE ReviseItems.PackageId = e.PackageId  AND Status = 0) as Unhandled
,(SELECT  COUNT(*) FROM ReviseItems WHERE ReviseItems.PackageId = e.PackageId  AND Status = 2) as Handled
From RevisePackage e, Users u Where e.UserId = u.UserId AND e.Status < 999 {@where} Order BY e.{@orderBy}) t
WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'SELECT  COUNT(*) FROM RevisePackage e WHERE Status < 999 {@where}';
	
	// 包明细分页
	const GetItemPaged_Sentence = 'SELECT ItemId "ItemId", WordId "WordId", Chinese "Chinese", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", Status "Status" FROM ( SELECT t.*, rownum r FROM (
SELECT i.ItemId, i.WordId, i.Status, d.Chinese,  d.Pinyin, d.Mongolian, d.MongolianLatin, d.MongolianCyrillic, d.English, d.Japanese
FROM DictionaryA d, RevisePackage p, ReviseItems i 
WHERE  p.PackageId = i.PackageId AND i.WordId = d.WordId AND p.Status <> 4 {@where} 
ORDER BY i.CreateDate DESC, d.Chinese
) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetItemsCount_Sentence = 'SELECT  COUNT(*) FROM DictionaryA d, RevisePackage p, ReviseItems i WHERE  p.PackageId = i.PackageId AND i.WordId = d.WordId AND p.Status <> 4 {@where} ';
	
	const GetPagedByPackageId_Sentence = 'SELECT ItemId "ItemId", WordId "WordId", Chinese "Chinese", Status "Status" FROM ( SELECT t.*, rownum r FROM (
SELECT i.ItemId, d.WordId, d.Chinese, i.Status FROM DictionaryA d, ReviseItems i WHERE i.WordId = d.WordId AND i.PackageId = {@PackageId}  AND i.Status < 999 AND d.Status < 999 {@where} ORDER BY i.CreateDate, d.Chinese
) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	
	const Update_Sentence = 'UPDATE RevisePackage SET Status = {@NewStatus} WHERE  PackageId = {@PackageId} AND Status = {@OldStatus}';
	
	const GetCountByPackageId_Sentence = 'SELECT  COUNT(*) FROM DictionaryA d, ReviseItems i WHERE i.WordId = d.WordId AND i.PackageId = {@PackageId}  AND i.Status < 10 AND d.Status < 999 {@where} ';
	const GetTotalByPackageId_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status < 6 ';
	const GetCountUnhandled_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status < 6 ';
	const GetCountRevisedByPackageId_Sentence = 'SELECT COUNT(ItemId)  FROM ReviseItems WHERE PackageId = {@PackageId}  AND  Status > 0 AND  Status < 5 ';
	
	const GetItemCount_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status > {@Status1} AND Status < {@Status2} {@where} ';
	//const GetItemCountHandled_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status > {@Status1} AND Status < {@Status2} {@where} ';
	//const GetItemCountUnhandled_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status > {@Status1} AND Status < {@Status2} {@where} ';
	const GetItemCountByStatus_Sentence = 'SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status = {@Status} {@where} ';
	
	//const UpdateItemsStatusToRejected_Sentence = 'UPDATE ReviseItems SET Status = {@Status} WHERE  PackageId = {@PackageId} AND Status = {@Handled} ';
	
	const UpdateItemStatus_Sentence = 'UPDATE ReviseItems SET Status = {@Status}, ModifiedDate = sysdate WHERE WordId = {@WordId} AND PackageId = {@PackageId} ';
	
	const UpdateItemStatusByPackageId_Sentence = 'UPDATE ReviseItems SET Status = {@Status} WHERE  PackageId = {@PackageId} AND Status = {@Handled}';
	const UpdateItemStatusByOldStatus_Sentence = 'UPDATE ReviseItems SET Status = {@NewStatus}, ModifiedDate = sysdate WHERE PackageId = {@PackageId} AND Status = {@OldStatus}';
	const UpdateWordStatusByOldStatus_Sentence = 'UPDATE DictionaryA SET Status = {@NewStatus} WHERE Status = {@OldStatus} AND WordId IN (SELECT WordId FROM ReviseItems WHERE PackageId = {@PackageId} AND Status = {@ItemStatus} )';
	
	
	const GetLastItems_Sentence = 'SELECT ItemId "ItemId", WordId "WordId", Chinese "Chinese", Status "Status", Pinyin "Pinyin", Mongolian "Mongolian", MongolianLatin "MongolianLatin", MongolianCyrillic "MongolianCyrillic", English "English", Japanese "Japanese", SourceDictionary "SourceDictionary" FROM ( SELECT t.*, rownum r FROM (
SELECT i.ItemId, d.WordId, d.Chinese, d.Pinyin, d.Mongolian, d.MongolianLatin, d.MongolianCyrillic, d.English, d.Japanese, d.SourceDictionary, i.Status FROM DictionaryA d, ReviseItems i WHERE i.WordId = d.WordId AND i.PackageId = {@PackageId}  AND i.Status < 999 AND d.Status < 999  ORDER BY i.ModifiedDate DESC 
) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetItemCountByPackageId_Sentence ='SELECT 
(SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status > -1 AND Status < 10) "Total"
,(SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status = 0) "Unhandled"
,(SELECT  COUNT(ItemId) FROM ReviseItems WHERE PackageId = {@PackageId}  AND Status = 2) "Handled"
FROM Dual';
	
	private $Connection = NULL;
	
	public function GetExpireCount($userId, $days) {
		$sql = str_replace("{@UserId}",$userId, RevisePackageDao::GetPackageCount );
		$sql = str_replace("{@Where}",' AND Status = 0 AND TRUNC(SysDate - CreateDate) > ' . $days . ' ', $sql );
		$db = Database::Connect();
		$resultSet = $db->GetSingleVal($sql);
		
		
		//$log = new FileHelper();
		//$log->file = "log.txt";
		//$log->writeline(PHP_EOL .'========' . date("Y-m-d H:i:s"). PHP_EOL. $sql);
		
		
		
		return $resultSet;
	}
	
	public function  UpdatePackageStatus($packageId, $oldStatus, $newStatus, $operationId, $packageName='', $operationName='') {
		
		// 1、更新词典表中词条的状态，分为提交和撤销的
		$sql1 = str_replace("{@PackageId}",$packageId, RevisePackageDao::UpdateWordStatusByOldStatus_Sentence );
		$sql1 = str_replace('{@ItemStatus}', 0, $sql1); // 包明细中未处理的
		$sql1 = str_replace('{@OldStatus}', 6, $sql1);
		$sql1 = str_replace('{@NewStatus}', 4, $sql1);
		
		$sql2 = str_replace("{@PackageId}",$packageId, RevisePackageDao::UpdateWordStatusByOldStatus_Sentence );
		$sql2 = str_replace('{@ItemStatus}', 2, $sql2); // 包明细中已处理的
		$sql2 = str_replace('{@OldStatus}', 6, $sql2);
		$sql2 = str_replace('{@NewStatus}', 8, $sql2);
		
		// 2、更新包明细中词条的状态，分为已处理和未处理的
		$sql3 = str_replace("{@PackageId}",$packageId, RevisePackageDao::UpdateItemStatusByOldStatus_Sentence );
		$sql3 = str_replace('{@OldStatus}', 0, $sql3);
		$sql3 = str_replace('{@NewStatus}', 6, $sql3);
		
		$sql4 = str_replace("{@PackageId}",$packageId, RevisePackageDao::UpdateItemStatusByOldStatus_Sentence );
		$sql4 = str_replace('{@OldStatus}', 2, $sql4);
		$sql4 = str_replace('{@NewStatus}', 4, $sql4);
		
		// 3、更新包的状态
		$sql5 = str_replace("{@PackageId}",$packageId, RevisePackageDao::Update_Sentence );
		$sql5 = str_replace('{@OldStatus}', $oldStatus, $sql5); // 
		$sql5 = str_replace('{@NewStatus}', $newStatus, $sql5);
		
		$logDao = new LogDao();
		$sqlLog = $logDao->GetLogSQL(4, 2, $operationId, $operationName.$packageName, $operationName.$packageName, '', $packageId);
		
		$db = Database::Connect();
		$resultSet = $db->ExecuteSQLs(array($sql1, $sql2, $sql3, $sql4, $sql5, $sqlLog));
		return $resultSet;
	}
	
	public function  GetItemCountByPackageId($packageId) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetItemCountByPackageId_Sentence );
		
		$db = Database::Connect();
		$resultSet = $db->GetSingleObject($sql);
		return $resultSet;
	}
	
	public function  SubmitPackage($packageId, $packageName) {
		$count = $this->GetItemCountByPackageId($packageId);
		if(intval($count->Total) != ($count->Unhandled + $count->Handled)){
			return 0;// 系统数据出现了逻辑错误， 数据包的总是已经不等于 已处理和未处理之和。
		}
		//if($count['Unhandled'] > 0) {
		//	return 1;
		//}
		
		$result = $this->UpdatePackageStatus($packageId, 0, 2, 205, $packageName, '编辑人员提交数据包:');
		return $result;
	}
	
	public function  RejectPackage($packageId, $packageName) {
		$count = $this->GetItemCountByPackageId($packageId);
		if(intval($count->Total) != ($count->Unhandled + $count->Handled)){
			return 0;// 系统数据出现了逻辑错误， 数据包的总是已经不等于 已处理和未处理之和。
		}
		//if($count['Unhandled'] > 0) {
		//	return 1;
		//}
	
		$result = $this->UpdatePackageStatus($packageId, 0, 4, 206, $packageName, '编辑人员撤销数据包:');
		return $result;
	}
	
	
	public function GetItemCount($packageId, $where) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetItemCount_Sentence );
		$sql = str_replace("{@Status1}", -1, $sql ); // 0:已分配,表示还未处理
		$sql = str_replace("{@Status2}", 10, $sql ); // 4:已提交,6:已撤销
		$sql = str_replace("{@where}",$where, $sql );
	
		$db = Database::Connect();
		$resultSet = $db->GetSingleVal($sql);
		return $resultSet;
	}
	
	public function GetItemCountHandled($packageId, $where) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetItemCountByStatus_Sentence );
		$sql = str_replace("{@Status}", 2, $sql ); // 2:已处理
		$sql = str_replace("{@where}",$where, $sql );

		$db = Database::Connect();
		$resultSet = $db->GetSingleVal($sql);
		return $resultSet;
	}
	
	public function GetItemCountUnhandled($packageId, $where) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetItemCountByStatus_Sentence );
		$sql = str_replace("{@Status}", 0, $sql ); // 0:已分配,表示还未处理
		$sql = str_replace("{@where}",$where, $sql );
	
		$db = Database::Connect();
		$resultSet = $db->GetSingleVal($sql);
		return $resultSet;
	}

	public function UpdateItemStatusByPackageId($packageId, $newStatus, $oldStatus){
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::UpdateItemStatusByPackageId_Sentence );
		$sql = str_replace("{@Status}",$newStatus, $sql );
		$sql = str_replace("{@Handled}",$oldStatus, $sql );
		
		$this->Connection = Database::Connect();	
	}
	
	public function GetLastItem($packageId, $startRowIndex, $pageSize) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetLastItems_Sentence );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
			
		// 连接数据库，并读取结果集
		$db = Database::Connect();
		$resultSet = $db->GetSingleObject($sql);
			
		return $resultSet;
	}
	
	public function GetPagedByPackageId($packageId, $startRowIndex, $pageSize, $where, $orderBy) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetPagedByPackageId_Sentence );
		$sql = str_replace("{@where}",$where, $sql );
		$sql = str_replace("{@orderBy}",$orderBy, $sql );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
			
		// 连接数据库，并读取结果集
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
			
		return $resultSet;
	}
	
	public function GetCountByPackageId($packageId, $where) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetCountByPackageId_Sentence );
		$sql = str_replace("{@where}",$where, $sql );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}
	
	// 获取已经校对过的词条数
	public function GetCountRevised($packageId) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetCountRevisedByPackageId_Sentence );
		//$sql = str_replace("{@where}",$where, $sql );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}
	
	public function GetTotalByPackageId($packageId) {
		$sql = str_replace("{@PackageId}",$packageId, RevisePackageDao::GetTotalByPackageId_Sentence );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}
	
	//创建录入数据包
	function Create($userId, $categoryId, $packageName, $itemCount) {
		
		//录入员
		$userDao = new UserDao();
		$user = $userDao->GetById($userId);
		//数据包数量
		$count = RevisePackageDao::GetCountByUser($userId, ' AND Status = 0 '); //0:已分配，但未提交
		
		if($count >= 3 ) {
			return 2;// 表示此人的数据包数量已经达到上限
		}
		
		//首先判断是否有可以分配的词条
		$sqlExist = str_replace('{@WordCategory}', $categoryId, WordDao::GetCountByWordCategory_Sentence);
		$sqlExist = str_replace('{@Status}', 4, $sqlExist);//4:录入完成
		$dbExist = Database::Connect();
		$exist = $dbExist->GetSingleVal($sqlExist);
		if($exist > 0){
			// 插入一条数据包记录
			$sql1 = "";
			if(isset($packageName) && !empty($packageName)) {
				$sql1 = str_replace('{@PackageName}', $packageName, RevisePackageDao::Insert_Sentence);
			} else {
				//$count = RevisePackageDao::GetCountByUser($userId, '');
				$sql1 = str_replace('{@PackageName}', $user->RealName.'的第'.($count + 1). '个编辑数据包', RevisePackageDao::Insert_Sentence);
			}
			$sql1 = str_replace('{@UserId}', $userId, $sql1);
			// 插入包明细
			$sql2 = str_replace('{@OldStatus}', 4, RevisePackageDao::InsertItems_Sentence);//4
			$sql2 = str_replace('{@WordCategory}', $categoryId, $sql2);
			$sql2 = str_replace('{@ItemCount}', $itemCount, $sql2);
			
			// 更新字典中的词条状态
			$sql3 = str_replace('{@NewStatus}', 6, RevisePackageDao::UpdateWord_Sentence);
			
			$db = Database::Connect();
			$packageId = $db->ExecuteWithSequence($sql1, RevisePackageDao::SelectCurrentPackageId, $sql2, $sql3);
			  //$db->Execute($sql); //RevisePackageDao::InsertEditItems
			$db->Close();
			
			$array = array("UserId"=>$user->UserId,
					'CategoryId'=>4,
					"ModuleId"=>2,
					"OperationId"=>204,
					"OperationName"=>$user->RealName.'创建编辑数据包：'.$packageName,
					"ContentText"=>$user->RealName.'创建编辑数据包：'.$packageName,
					"IPAddress"=>GetIP(),
					'ObjectId'=>$packageId,
					"Description"=>'');
			$logDao = new LogDao();
			$logDao->Create($array);
			
			return $packageId;// 表示成功分配数据包
		} else {
			return 0;// 表示没有词条分配给此人
		}
	}
	//录入员数据包数量
	function GetCountByUser($userId, $where){
		$sql = RevisePackageDao::GetPackageCount;
		$sql = str_replace('{@UserId}', $userId, $sql);
		$sql = str_replace('{@Where}', $where, $sql);
		$db = Database::Connect();
		$count = $db->GetSingleVal($sql);
		return $count==null?0:$count;
	}
	
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace("{@where}",$where, RevisePackageDao::GetPaged_Sentence );
		$sql = str_replace("{@orderBy}",$orderBy, $sql );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
		 
		// 连接数据库，并读取结果集
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
		 
		return $resultSet;
	}
	
	public function GetCount( $where) {
		$sql = str_replace("{@where}",$where, RevisePackageDao::GetCount_Sentence );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}
	
	public function UpdateWord($array)
	{
		//$array['Status'] = 6;//编辑中 (即：校对中)
		$packageStatus = 0;
		if(isset($array['PackageStatus'])) $packageStatus = $array['PackageStatus'];
		$wordDao = new WordDao();
		$sql1 = $wordDao->PrepareUpdateSQL($array,  WordDao::Update_Sentence);
		// 这里 Status = 2表示当前词条正在校对中，但还没有提交。
		$sql2 = str_replace("{@Status}", 2, RevisePackageDao::UpdateItemStatus_Sentence );// $array['Status']
		$sql2 = str_replace("{@WordId}",$array['WordId'], $sql2);
		$sql2 = str_replace("{@PackageId}",$array['PackageId'], $sql2);
		
		$message = $wordDao->Diff($wordDao->GetById($array["WordId"]), $array);
		$logDao = new LogDao();
		$sql3 = $logDao->GetLogSQL(4, 3, 302, '编辑人员修改词条:'.$array['Chinese'], $message, '', $array["WordId"]);
		
		$db = Database::Connect();
		if($packageStatus == 0){
			$resultData = $db->ExecuteSQLs(array($sql1, $sql2, $sql3));//Execute2
		} else {
			$resultData = $db->ExecuteSQLs(array($sql1, $sql3));//Execute2
		}
		$db->Close();
	}
	
	public function CreateWord($array)
	{
		$array['Status'] = 6; //编辑中 (即：校对中)
		$array['OriginalCategory'] = $array['SourceDictionary'];
		$array['WordCategory'] = $array['SourceDictionary'];
		$array['SourceDictionary'] = 991;//'新建词条';
		
		$wordDao = new WordDao();
		$sql1 = $wordDao->PrepareInsertSQL($array);
		
		$sql2 = str_replace("{@PackageId}", $array['PackageId'], RevisePackageDao::InsertItem_Sentence );// $array['Status']
		//$sql2 = str_replace("{@WordId}",$array['WordId'], $sql2);
		$sql2 = str_replace("{@Status}", 2, $sql2);// 这里 Status = 2表示当前词条正在校对中，但还没有提交。
		
		// 词条表插入一条记录， 数据包明细插入一条记录，
		$db = Database::Connect();
		//$resultData = $db->Execute2($sql1, $sql2);
		$resultData = $db->ExecuteWithTrans($sql1, WordDao::SelectCurrentWordId_Sentence, $sql2);
		$db->Close();
	}
	public function DeleteWord($packageId,$wordId, $chinese)
	{
		// $word['PackageId'], $word['WordId'], $word['Chinese'], $word['UserId']
		$sql1 = str_replace("{@WordId}", $wordId, WordDao::Delete_Sentence );

		$sql2 = str_replace("{@PackageId}", $packageId, RevisePackageDao::UpdateItemStatus_Sentence );
		$sql2 = str_replace("{@WordId}",$wordId, $sql2);
		$sql2 = str_replace("{@Status}", 999, $sql2);
		
		$logDao = new LogDao(); // 4:数据操作, 3：词条管理, 
		$sql3 = $logDao->GetLogSQL(4, 3, 304, '编辑人员 删除词条:'.$chinese, '编辑人员 删除词条:'.$chinese, '', $wordId);
		
		$db = Database::Connect();
		$resultData = $db->ExecuteSQLs(array($sql1, $sql2, $sql3));
		$db->Close();
	}
	
	public function GetItemsPaged($startRowIndex, $pageSize, $where) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace("{@where}",$where, RevisePackageDao::GetItemPaged_Sentence );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
		
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
			
		return $resultSet;
	}
	
	public function GetItemsCount($where) {
		$sql = str_replace("{@where}",$where, RevisePackageDao::GetItemsCount_Sentence );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}
}


?>