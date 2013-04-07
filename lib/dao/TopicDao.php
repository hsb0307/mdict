<?php
/*
 *日志管理数据访问类
*/
require_once '../../lib/common/OracleDb.php';
require_once '../../lib/common/_common.php';
require_once '../../lib/dao/UserDao.php';

class TopicDao{
	const GetById_Sentence = 'SELECT TopicId "TopicId", UserId "UserId", Title "Title", FullText "FullText", LastPostUserId "LastPostUserId", LastPostText "LastPostText", to_char(LastPostDate,\'yyyy/mm/dd hh24:mi:ss\') "LastPostDate", to_char(DateCreated,\'yyyy/mm/dd hh24:mi:ss\') "DateCreated", Status "Status" FROM Topics WHERE TopicId = ';
	const GetAll_Sentence = 'SELECT LogId "LogId", UserId "UserId", CategoryId "CategoryId", ContentText "ContentText", IPAddress "IPAddress", CreateDate "CreateDate", Status "Status", Description "Description" FROM Logs ';
	const GetPaged_Sentence = 'SELECT TopicId "TopicId", UserId "UserId", Title "Title", FullText "FullText", LastPostUserId "LastPostUserId", LastPostText "LastPostText", LastPostDate "LastPostDate", to_char(DateCreated,\'yyyy/mm/dd hh24:mi:ss\') "DateCreated", Status "Status", RealName "UserName" FROM (
SELECT t.*, rownum r FROM (
SELECT TopicId, t.UserId, Title, FullText, LastPostUserId, LastPostText, LastPostDate, DateCreated, t.Status, u.RealName
FROM Topics t, Users u WHERE t.UserId = u.UserId {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetCount_Sentence = 'select count(*)  from Topics t, Users u WHERE t.UserId = u.UserId {@where}';

	const DeleteUser_Sentence =  "UPDATE Topics SET Status = 999 WHERE LogId = {@WordId} ";
	const Update_Sentence = '';//, Status = {@Status}
	const Insert_Sentence = 'INSERT INTO Topics ( UserId, Title, FullText )  VALUES ({@UserId}, \'{@Title}\', \'{@FullText}\' ) ';
	const InsertPost_Sentence = 'INSERT INTO Posts ( TopicId, UserId, Title, FullText )  VALUES ({@TopicId}, {@UserId}, \'{@Title}\', \'{@FullText}\' ) ';
	const UpdateLastPost_Sentence = 'UPDATE Topics SET LastPostUserId = \'{@LastPostUserId}\', LastPostText = \'{@LastPostText}\', LastPostDate = sysdate, Status = 1 WHERE TopicId = {@TopicId} ';

	const GetPostPaged_Sentence = 'SELECT PostId "PostId", TopicId "TopicId", UserId "UserId", Title "Title", FullText "FullText", to_char(DateCreated,\'yyyy/mm/dd hh24:mi:ss\') "DateCreated", Status "Status", RealName "UserName" FROM (
SELECT t.*, rownum r FROM (
SELECT PostId, TopicId, u.UserId, Title, FullText, DateCreated, p.Status, u.RealName
FROM Posts p, Users u WHERE p.UserId = u.UserId AND TopicId = {@TopicId} {@where} Order BY {@orderBy}) t WHERE rownum <= {@endRowIndex}) B  WHERE r > {@startRowIndex}';
	const GetPostCount_Sentence = 'select count(*)  from Posts p, Users u WHERE p.UserId = u.UserId AND TopicId = {@TopicId} {@where}';
	
	
	public function GetPaged($startRowIndex, $pageSize, $where, $orderBy) {
		// 准备好SQL语句
		$sql = str_replace ( "{@where}", $where, TopicDao::GetPaged_Sentence );
		$sql = str_replace ( "{@orderBy}", $orderBy, $sql );
		$sql = str_replace ( "{@endRowIndex}", $startRowIndex + $pageSize, $sql );
		$sql = str_replace ( "{@startRowIndex}", $startRowIndex, $sql );

		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$resultSet = $db->GetResultSet ( $sql );

		return $resultSet;
	}
	public function GetCount($where) {
		$sql = str_replace ( "{@where}", $where, TopicDao::GetCount_Sentence );
		$db = Database::Connect ();
		return $db->GetSingleVal ( $sql );
	}

	public function PrepareSQL($array, $sql) {
		$sql = str_replace ('{@UserId}', $array ['UserId'], $sql );
		$sql = str_replace ('{@Title}', $array ['Title'], $sql );
		if (isset ( $array ['FullText'] )) { $sql = str_replace ('{@FullText}', $array ['FullText'], $sql ); }
		if (isset ( $array ["TopicId"] )) {
			$sql = str_replace ( "{@TopicId}", $array ["TopicId"], $sql );
		}
		if (isset ( $array ["Status"] )) {
			$sql = str_replace ( "{@Status}", $array ["Status"], $sql );
		}
		return $sql;
	}
	public function Create($array) {
		// 准备好SQL语句
		$sql = $this->PrepareSQL ( $array, TopicDao::Insert_Sentence );
		// $sql = str_replace("{@Id}",$array["Id"], $sql);
		
		$db = Database::Connect ();
		$userId = $db->Execute ( $sql );
		$db->Close ();

		return $userId;
	}
	
	public function CreatePost($array) {
		$sql = str_replace ('{@TopicId}', $array ['TopicId'], TopicDao::InsertPost_Sentence );
		$sql = str_replace ('{@UserId}', $array ['UserId'], $sql );
		if (isset ( $array ['Title'] )) { $sql = str_replace ('{@Title}', $array ['Title'], $sql ); }
		if (isset ( $array ['FullText'] )) { $sql = str_replace ('{@FullText}', $array ['FullText'], $sql ); }
		
		$sql2 = str_replace ('{@LastPostUserId}', $array ['UserId'], TopicDao::UpdateLastPost_Sentence );
		$sql2 = str_replace ('{@LastPostText}', $array ['FullText'], $sql2 );
		$sql2 = str_replace ('{@TopicId}', $array ['TopicId'],  $sql2);
		// $sql = str_replace("{@Id}",$array["Id"], $sql);
		$db = Database::Connect ();
		$userId = $db->Execute2 ( $sql, $sql2 );
		$db->Close ();
	
		return $userId;
	}
	
	public function GetPostPaged($topicId, $startRowIndex, $pageSize, $where, $orderBy) {
		// 准备好SQL语句
		$sql1 = str_replace ( "{@TopicId}", $topicId, TopicDao::GetPostCount_Sentence );
		$sql1 = str_replace ( "{@where}", $where, $sql1 );
		
		$sql2 = str_replace ( "{@TopicId}", $topicId, TopicDao::GetPostPaged_Sentence );
		$sql2 = str_replace ( "{@where}", $where, $sql2 );
		$sql2 = str_replace ( "{@orderBy}", $orderBy, $sql2 );
		$sql2 = str_replace ( "{@endRowIndex}", $startRowIndex + $pageSize, $sql2 );
		$sql2 = str_replace ( "{@startRowIndex}", $startRowIndex, $sql2 );
		
		$sql3 = TopicDao::GetById_Sentence.$topicId;
		
		// 连接数据库，并读取结果集
		$db = Database::Connect ();
		$result = $db->ExecuteSQLs(array($sql1, $sql2, $sql3));
		$array = array('Count'=>$result[0][0]['COUNT(*)'], 'Rows'=>$result[1], 'OtherData'=>$result[2][0]);
		return $array;
	}
	
	
}
?>
