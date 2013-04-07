<?php
/*
 *校对数据包管理
*/
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';

class ApprovePackageDao{
	// ApprovePackage  ApproveItems

	const GetPaged_Sentence = 'SELECT  PackageId "PackageId", PackageName "PackageName", UserId "UserId", Status "Status", to_char(CreateDate,\'yyyy/mm/dd hh24:mi:ss\') "CreateDate", Username "Username", RealName "RealName", Total "Total", Unhandled "Unhandled", Handled "Handled" FROM ( SELECT t.*, rownum r FROM (
SELECT  e.PackageId, e.PackageName, e.UserId, e.Status, e.CreateDate, u.Username, u.RealName,
(SELECT  COUNT(*) FROM ApproveItems WHERE ApproveItems.PackageId = e.PackageId  AND Status > -1 AND Status < 10) as Total
,(SELECT  COUNT(*) FROM ApproveItems WHERE ApproveItems.PackageId = e.PackageId  AND Status = 0) as Unhandled
,(SELECT  COUNT(*) FROM ApproveItems WHERE ApproveItems.PackageId = e.PackageId  AND Status = 2) as Handled
From ApprovePackage e, Users u Where e.UserId = u.UserId AND e.Status < 999 {@where} Order BY e.{@orderBy}) t
WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'SELECT  COUNT(*) FROM ApprovePackage e WHERE Status < 999 {@Where}';
	
	const Insert_Sentence = 'INSERT INTO ApprovePackage (PackageName, UserId) VALUES (\'{@PackageName}\', {@UserId})';
	const SelectCurrentPackageId = 'select ApprovePackage_PackageId.currval from dual';
	//  nlssort(Chinese,\'NLS_SORT = SChinese_Pinyin_M\')
	const InsertItems_Sentence = 'INSERT INTO ApproveItems (PackageId, WordId)
		SELECT {@CurrentValue}, t.WordId FROM (SELECT  WordId FROM DictionaryA WHERE Status = {@OldStatus} AND WordCategory = {@WordCategory} ORDER BY  Chinese) t WHERE  rownum <= {@ItemCount} ';
	const UpdateWord_Sentence = 'UPDATE DictionaryA SET Status = {@NewStatus} WHERE WordId IN (SELECT WordId FROM ApproveItems WHERE PackageId = {@CurrentValue})';
	const InsertItem_Sentence = 'INSERT INTO ApproveItems (PackageId, WordId, Status)  VALUES ({@PackageId}, {@CurrentValue}, {@Status}) ';
	
	//const GetItemCount_Sentence = 'SELECT  COUNT(*) FROM DictionaryA d, ApproveItems i WHERE i.WordId = d.WordId AND p.Status <> 4 {@where} ';
	const GetItemCount_Sentence = 'SELECT  COUNT(*) FROM DictionaryA d, ApproveItems i WHERE i.WordId = d.WordId AND i.PackageId = {@PackageId}  AND i.Status < 10 AND d.Status < 999 {@where} ';
	const GetItemPaged_Sentence = 'SELECT ItemId "ItemId", WordId "WordId", Chinese "Chinese", Status "Status" FROM ( SELECT t.*, rownum r FROM (
SELECT i.ItemId, d.WordId, d.Chinese, i.Status FROM DictionaryA d, ApproveItems i WHERE i.WordId = d.WordId AND i.PackageId = {@PackageId}  AND i.Status < 999 AND d.Status < 999 {@where} ORDER BY i.CreateDate, d.Chinese
) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	
	const GetCountApproved_Sentence = 'SELECT COUNT(*) FROM ApproveItems WHERE PackageId = {@PackageId}  AND  Status > 0 AND  Status < 5 ';
	
	// 获取已经审批过的词条数
	public function GetCountApproved($packageId) {
		$sql = str_replace("{@PackageId}",$packageId, ApprovePackageDao::GetCountApproved_Sentence );
		//$sql = str_replace("{@where}",$where, $sql );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
	}
	
	public function GetItemPaged($packageId, $startRowIndex, $pageSize, $where, $orderBy) {
		$sql = str_replace("{@PackageId}",$packageId, ApprovePackageDao::GetItemPaged_Sentence );
		$sql = str_replace("{@where}",$where, $sql );
		$sql = str_replace("{@orderBy}",$orderBy, $sql );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
		return $resultSet;
	}
	
	public function GetItemCount($packageId, $where) {
		$sql = str_replace("{@PackageId}",$packageId, ApprovePackageDao::GetItemCount_Sentence );
		$sql = str_replace("{@where}",$where, $sql );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
	}
	
	function Create($userId, $categoryId, $packageName, $itemCount) {
	
		//录入员
		$userDao = new UserDao();
		$user = $userDao->GetById($userId);
		//数据包数量
		$count = ApprovePackageDao::GetCountByUser($userId, ' AND Status = 0 '); //0:已分配，但未提交
	
		if($count >= 3 ) {
			return 1;// 表示此人的数据包数量已经达到上限
		}
	
		//首先判断是否有可以分配的词条
		$sqlExist = str_replace('{@WordCategory}', $categoryId, WordDao::GetCountByWordCategory_Sentence);
		$sqlExist = str_replace('{@Status}', 8, $sqlExist);//8:编辑完成
		$dbExist = Database::Connect();
		$exist = $dbExist->GetSingleVal($sqlExist);
		if($exist > 0){
			// 插入一条数据包记录
			$sql1 = '';
			if(isset($packageName) && !empty($packageName)) {
				$sql1 = str_replace('{@PackageName}', $packageName, ApprovePackageDao::Insert_Sentence);
			} else {
				//$count = RevisePackageDao::GetCountByUser($userId, '');
				$sql1 = str_replace('{@PackageName}', $user->RealName.'的第'.($count + 1). '个审定数据包', RevisePackageDao::Insert_Sentence);
			}
			$sql1 = str_replace('{@UserId}', $userId, $sql1);
			// 插入包明细 ：选择已经编辑完成的
			$sql2 = str_replace('{@OldStatus}', 8, ApprovePackageDao::InsertItems_Sentence);//4
			$sql2 = str_replace('{@WordCategory}', $categoryId, $sql2);
			$sql2 = str_replace('{@ItemCount}', $itemCount, $sql2);
				
			// 更新字典中的词条状态 :审定中
			$sql3 = str_replace('{@NewStatus}', 10, ApprovePackageDao::UpdateWord_Sentence);
				
			$db = Database::Connect();
			$packageId = $db->ExecuteWithSequence($sql1, ApprovePackageDao::SelectCurrentPackageId, $sql2, $sql3);
			//$db->Execute($sql); //RevisePackageDao::InsertEditItems
			$db->Close();
				
			$array = array("UserId"=>$user->UserId,
					'CategoryId'=>4, // 数据操作
					"ModuleId"=>2,  // 任务管理
					"OperationId"=>207, // 207,建立审定数据包
					"OperationName"=>$user->RealName.'创建审定数据包：'.$packageName,
					"ContentText"=>$user->RealName.'创建审定数据包：'.$packageName,
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
		$sql = str_replace("{@Where}", ' AND UserId = '.$userId, ApprovePackageDao::GetCount_Sentence );
		$sql = $sql.$where;
		$db = Database::Connect();
		$count = $db->GetSingleVal($sql);
		return $count==null?0:$count;
	}

	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}
		// 准备好SQL语句
		$sql = str_replace("{@where}",$where, ApprovePackageDao::GetPaged_Sentence );
		$sql = str_replace("{@orderBy}",$orderBy, $sql );
		$sql = str_replace("{@endRowIndex}",$startRowIndex + $pageSize, $sql );
		$sql = str_replace("{@startRowIndex}",$startRowIndex, $sql );
		 
		// 连接数据库，并读取结果集
		$db = Database::Connect();
		$resultSet = $db->GetResultSet($sql);
		 
		return $resultSet;
	}
	
	public function GetCount( $where) {
		$sql = str_replace("{@Where}",$where, ApprovePackageDao::GetCount_Sentence );
		$db = Database::Connect();
		return $db->GetSingleVal($sql);
		//$row = mysql_fetch_object($result)
	}

}


?>