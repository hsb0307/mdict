<?php

//namespace dic\lib\common;

/**
 *
 * @author Administrator
 *        
 */
//$ROOT = "/Dict";
$dbConfig = require("../../lib/config.inc.php");
// OracleDb
class Database {
	// TODO - Insert your code here
	
	/**
	 */
	function __construct() {
		
		// TODO - Insert your code here
	}
	
	/**
	 */
	function __destruct() {
		
		// TODO - Insert your code here
		$this->Close();
	}
	
	const Insert_Sentence = 'INSERT INTO Logs ( UserId, CategoryId, OperationName, ContentText, IPAddress, Description )  VALUES ({@UserId}, 901, \'{@OperationName}\', \'{@ContentText}\', \'{@IPAddress}\', \'\' ) ';
	
	private $Connection = NULL;
	
	public static function Connect()
	{
		$obj = new Database();
	
		global $dbConfig;// $db_user, $db_pass, $db;
		//$ret = $obj->Connection = mysql_connect( $dbConfig["DB_HOST"], $dbConfig["DB_USER"], $dbConfig["DB_PWD"]);
		$cn = $obj->Connection = oci_connect($dbConfig["DB_USER"], $dbConfig["DB_PWD"], $dbConfig["DB_EasyConnect"]);
		if (!$cn) {
			$e = oci_error();
			//trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			throw new Exception( "未能连接到数据库, " . $e['message']);
		}
		//$ret = $obj->Connection = mysql_connect( $db_host, $db_user, $db_pass );
		//if( !$cn ) throw new Exception( "Error, failed to connect to database. " . mysql_error());
	
		//$cn = mysql_select_db( $db_name, $obj->Connection);
		//if( !$cn ) throw new Exception ("Error, failed to select db " . mysql_error());
	
		//$cn = mysql_query( 'set names utf8', $obj->Connection);
		//if( !$cn ) throw new Exception ("Error, failed to set the character_set" . mysql_error());
	
		return $obj;
	}
	
	public function GetIP(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];
		}
		else{
			$cip = "无法获取！";
		}
		return $cip;
	}
	
	public function GetLogSQL($message){
		session_start();
		$sql = str_replace ('{@UserId}', $_SESSION["UserId"], Database::Insert_Sentence );
		$sql = str_replace ('{@OperationName}', '执行SQL失败', $sql );
		$sql = str_replace ('{@ContentText}', $message, $sql );
		$sql = str_replace ('{@IPAddress}', $this->GetIP(), $sql );
		
		return $sql;
	}
	
	public function Close()
	{
		if( $this->Connection != NULL )
		{
			oci_close($this->Connection);
			$this->Connection = NULL;
		}
	}
	
	public function Execute($sql, $autoCommit=true)
	{		
		$cn = $this->Connection;
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = null;
		if($autoCommit) {
			$IsSuccess = oci_execute($stid);
		} else {
			$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		}
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			
			$logSQL = $this->GetLogSQL($e['message']. str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command.执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
		
		if (oci_statement_type($stid) == 'SELECT') {
			$resultSet = array();
			while ( $row = oci_fetch_assoc($stid) ) {
				$resultSet[] = $row;
			}
			return $resultSet;
		}
		
		return $IsSuccess;
	}
	
	public function ExecuteWithMode($sql, $autoCommit)
	{
		$cn = $this->Connection;
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = null;
		if($autoCommit) {
			$IsSuccess = oci_execute($stid);
		} else {
			$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		}
	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			
			$logSQL = $this->GetLogSQL($e['message']. str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
	
		return $IsSuccess;
	}
	
	public function Commit()
	{
		oci_commit($this->Connection);
		oci_close($this->Connection);
	}
	
	public function InsertReturnSequence($sql, $sequence)
	{
		$cn = $this->Connection;
		
		$stid = oci_parse($cn, $sql);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句：". $e['sqltext'].($e['offset']+1)."'");
		}
		
		$stid = oci_parse($cn, 'select '. $sequence. '.currval from dual');
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句：". $e['sqltext'].($e['offset']+1)."'");
		}
		
		$row = oci_fetch_row($stid);
		$returnValue = NULL;
		if( $row ) $returnValue = $row[0];
		
		oci_commit($cn);
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $returnValue;
	}

	public function ExecuteWithTrans($sql1, $sql2, $sql3)
	{
		$cn = $this->Connection;
		
		$stid = oci_parse($cn, $sql1);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			
			$logSQL = $this->GetLogSQL($e['message']. str_replace("'", "''", $sql1).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql1. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
		
		$stid = oci_parse($cn, $sql2);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			
			$logSQL = $this->GetLogSQL($e['message']. str_replace("'", "''", $sql2).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql2. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
		
		$row = oci_fetch_row($stid);
		$returnValue = NULL;
		if( $row ) $returnValue = $row[0];		
		$sql = str_replace('{@CurrentValue}', $returnValue, $sql3);
		
		$stid = oci_parse($cn, $sql);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			
			$logSQL = $this->GetLogSQL($e['message']. str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
		
		oci_commit($cn);
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		//return $returnValue;
	
		return $IsSuccess;
	}
	
	// 执行包含返回序列的多条sql语句，在这些语句中替换掉序列的值
	public function ExecuteWithSequence($sql1, $sql2, $sql3, $sql4)
	{
		$cn = $this->Connection;
	
		$stid = oci_parse($cn, $sql1);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql1. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
	
		$stid = oci_parse($cn, $sql2);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql2. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
	
		$row = oci_fetch_row($stid);
		$returnValue = NULL;
		if( $row ) { 
			$returnValue = $row[0];
		} else {
			oci_rollback($cn);
			throw new Exception( "当前表没有使用序列");
		}
		$sql = str_replace('{@CurrentValue}', $returnValue, $sql3);
	
		$stid = oci_parse($cn, $sql);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
		
		$sql = str_replace('{@CurrentValue}', $returnValue, $sql4);
		
		$stid = oci_parse($cn, $sql);
		$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  .$sql. "执行的SQL语句:". $e['sqltext'].($e['offset']+1)."'");
		}
	
		oci_commit($cn);
	
		oci_free_statement($stid);
		oci_close($this->Connection);
		//return $returnValue;
	
		return $returnValue;//$IsSuccess;
	}
	
	public function ExecuteSQLs($sqlArray)
	{
		$cn = $this->Connection;
		$array = array();
		for($i = 0, $size = sizeof($sqlArray); $i < $size; $i++){
			if(isset($sqlArray[$i]) && (!empty($sqlArray[$i]))){
				
				$stid = oci_parse($cn, $sqlArray[$i]);
				$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
				if( !$IsSuccess ) {
					$e = oci_error($stid);
					oci_rollback($cn);
					
					$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sqlArray[$i]).'行号:'.($e['offset']+1));
					$stid = oci_parse($cn, $logSQL);
					oci_execute($stid);
					throw new Exception( "Error, " . $e['message']. " failed to execute command '" . $e['sqltext'].($e['offset']+1)."'");
				}
				
				if (oci_statement_type($stid) == 'SELECT')
				{
					while ( $row = oci_fetch_assoc($stid) ) {
						$array[$i][] = $row;
					}
				}
			}
		}
		
		oci_commit($cn);
		oci_free_statement($stid);
		oci_close($this->Connection);
		
		
		return $array;
	}
	
	public function Execute2($sql1, $sql2)
	{
		$cn = $this->Connection;
		$array = array();
		if (isset ( $sql1 ) && (! empty ( $sql1 ))) {
			
			$stid = oci_parse ( $cn, $sql1 );
			$IsSuccess = oci_execute ( $stid, OCI_NO_AUTO_COMMIT );
			
			if (! $IsSuccess) {
				$e = oci_error ( $stid );
				oci_rollback ( $cn );
				
				$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sql1).'行号:'.($e['offset']+1));
				$stid = oci_parse($cn, $logSQL);
				oci_execute($stid);
				
				throw new Exception ( "Error, " . $e ['message'] . " failed to execute command. 程序生成的SQL语句：" . $e ['sqltext'] . ($e ['offset'] + 1) . "'" );
			}
			if (oci_statement_type($stid) == 'SELECT')
			{
				while ( $row = oci_fetch_assoc ( $stid ) ) {
					$array [0] [] = $row;
				}
			}
		}
		
		if (isset ( $sql2 ) && (! empty ( $sql2 ))) {
			$stid = oci_parse($cn, $sql2);
			$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
			if( !$IsSuccess ) {
				$e = oci_error($stid);
				oci_rollback($cn);
				
				$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sql2).'行号:'.($e['offset']+1));
				$stid = oci_parse($cn, $logSQL);
				oci_execute($stid);
				
				throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句："  . $e['sqltext'].($e['offset']+1)."'");
			}
			if (oci_statement_type ( $stid ) == 'SELECT') {
				while ( $row = oci_fetch_assoc ( $stid ) ) {
					$array [1] [] = $row;
				}
			}
		}

		oci_commit($cn);
	
		oci_free_statement($stid);
		oci_close($this->Connection);
		//return $returnValue;
	
		return $array;
	}
	
	public function GetSingleVal($sql)
	{
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			
			$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($this->Connection, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command '"  .$sql. $e['sqltext'].($e['offset']+1)."'");
		}
		$row = oci_fetch_row($stid);
		$returnValue = NULL;
		if( $row ) $returnValue = $row[0];
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $returnValue;
	}
	
	public function GetSingleValWithMode($sql,$autoCommit)
	{
		$cn = $this->Connection;
		$stid = oci_parse($this->Connection, $sql);
		if($autoCommit){
			$IsSuccess = oci_execute($stid);
		}else{
			$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		}	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);
			throw new Exception( "Error, " . $e['message']. " failed to execute command '"  .$sql. $e['sqltext'].($e['offset']+1)."'");
		}
		$row = oci_fetch_row($stid);
		$returnValue = NULL;
		if( $row ) $returnValue = $row[0];
	
		oci_free_statement($stid);
		//oci_close($this->Connection);
		return $returnValue;
	
	}
	
	
	public function GetSingleValOrDefault($sql, $defaultVal)
	{
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command '"  .$sql. $e['sqltext'].($e['offset']+1)."'");
		}
		$row = oci_fetch_row($stid);
		oci_free_statement($stid);
		oci_close($this->Connection);
		if( !$row ) return $defaultVal;
		return $row[0];
	}
	
	public function GetCount($sql)
	{
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command '"  .$sql. $e['sqltext'].($e['offset']+1)."'");
		}
		$count = oci_num_rows($stid);
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $count;
	}
	
	public function GetSingleObject($sql)
	{
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			
			$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($this->Connection, $logSQL);
			oci_execute($stid);
			
			throw new Exception( "Error, " . $e['message']. " failed to execute command '" . $e['sqltext'].($e['offset']+1)."'");
		}
		$obj = oci_fetch_object($stid);
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $obj;
	}
	
	public function GetSingleObjectWithMode($sql,$autoCommit)
	{
		$cn = $this->Connection;
		$stid = oci_parse($this->Connection, $sql);
		if($autoCommit){
			$IsSuccess = oci_execute($stid);
		}else{
			$IsSuccess = oci_execute($stid, OCI_NO_AUTO_COMMIT);
		}		
	
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			oci_rollback($cn);			
			throw new Exception( "Error, " . $e['message']. " failed to execute command '" . $e['sqltext'].($e['offset']+1)."'");
		}
		$obj = oci_fetch_object($stid);
		oci_free_statement($stid);
		//oci_close($this->Connection);
		return $obj;
	}
	
	
	
	public function GetObject($sql, $sql2)
	{
		$cn = $this->Connection;
		$stid = oci_parse($cn, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			$logSQL = $this->GetLogSQL($e['message'].str_replace("'", "''", $sql).'行号:'.($e['offset']+1));
			$stid = oci_parse($cn, $logSQL);
			oci_execute($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command '" . $e['sqltext'].($e['offset']+1)."'");
		}
		$obj = oci_fetch_object($stid);
		
		$stid = oci_parse($cn, $sql2);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command. 程序生成的SQL语句：". $e['sqltext'].($e['offset']+1)."'");
		}
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $obj;
	}
	
	public function GetResultSet($sql)
	{
		$stid = oci_parse($this->Connection, $sql);
		$IsSuccess = oci_execute($stid);
		
		if( !$IsSuccess ) {
			$e = oci_error($stid);
			throw new Exception( "Error, " . $e['message']. " failed to execute command '"  .$sql. $e['sqltext'].($e['offset']+1)."'");
		}
		$resultSet = array();
		//$obj = oci_fetch_object($stid);
		while ($row = oci_fetch_assoc($stid)) {
			$resultSet[] = $row;
		}
		
		oci_free_statement($stid);
		oci_close($this->Connection);
		return $resultSet;
	}
	
	public function Encode($str)
	{
		$str = str_replace("//", "////", $str);
		$str = str_replace("'", "''", $str);
		//addslashes($str);
		return $str;
	}
	
}

?>