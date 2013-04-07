<?php
//$dbConfig = require("../../lib/config.inc.php");
require_once '../../libraries/config.inc.php';
//echo $dbConfig["DB_HOST"];
class Database  
{
    private $Connection = NULL;
      
    public static function Connect()
    {
        $obj = new Database();
        
        global $db_host, $db_user, $db_pass, $db_name;
        //$ret = $obj->Connection = mysql_connect( $dbConfig["DB_HOST"], $dbConfig["DB_USER"], $dbConfig["DB_PWD"]);
        $ret = $obj->Connection = mysql_connect( $db_host, $db_user, $db_pass );
        if( !$ret ) throw new Exception( "Error, failed to connect to database. " . mysql_error()); 
              
        $ret = mysql_select_db( $db_name, $obj->Connection);
        if( !$ret ) throw new Exception ("Error, failed to select db " . mysql_error());
          
        $ret = mysql_query( 'set names utf8', $obj->Connection);
        if( !$ret ) throw new Exception ("Error, failed to set the character_set" . mysql_error());
        
        return $obj;
    }
      
    function __destruct()
    { 
        $this->Close();
    }
    public function Close()
    {
        if( $this->Connection != NULL )
        {
            mysql_close($this->Connection);
            $this->Connection = NULL;
        }
    }
      
    public function Execute($sql)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute command '".$sql."',  " . mysql_error());
    }
    public function GetSingleVal($sql)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
        $row = mysql_fetch_row($rs);
        if( !$row ) return NULL;
        return $row[0];
    }
      
    public function GetSingleValOrDefault($sql, $defaultVal)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
        $row = mysql_fetch_row($rs);
        if( !$row ) return $defaultVal;
        return $row[0];
    }
      
    public function GetCount($sql)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
          
        return mysql_num_rows($rs);
    }
      
    public function GetSingleObject($sql)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
        return mysql_fetch_object($rs);
    }
    
    public function GetSingleRow($sql)
    {
        $rs = mysql_unbuffered_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
        return mysql_fetch_row($rs);
    } 
      
    public function GetResultSet($sql)
    {
        $rs = mysql_query( $sql, $this->Connection);
        return $rs;
    }
      
    public function Insert( $tableName, &$newRow)
    {
        $sql = "describe " . $tableName;
        $rs = mysql_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to describe table '".$tableName."',  " . mysql_error());
          
        $fieldsName = "";
        $fieldsValue = "";
          
        $reflectedObj = new ReflectionObject($newRow);
        $properties = $reflectedObj->getProperties();
        $count = count($properties);
        $fields = array();
        for( $i = 0; $i < $count; $i++)
        {
            $fields = array_merge( $fields, array($properties[$i]->getName() => $properties[$i]) );
        }
          
        $row = mysql_fetch_object($rs);
        while($row)
        {
            if( $reflectedObj->hasProperty($row->Field) )
            {
                $fieldVal = $fields[$row->Field]->getValue($newRow);
                $fieldsName = $fieldsName . $row->Field . ",";
                  
                if( strpos( $row->Type, "varchar") == 0 ||  
                    strpos( $row->Type, "char") == 0 ||  
                    strpos( $row->Type, "datetime") == 0 ||  
                    strpos( $row->Type, "time") == 0 )
                {
                    $fieldsValue = $fieldsValue."'".$this->Encode( $fieldVal )."',";
                }
                else  
                {
                    $fieldsValue = $fieldsValue . $fieldVal . ",";
                }
            }
          
            $row = mysql_fetch_object($rs);
        }
          
        $sql = "INSERT INTO ".$tableName."(".rtrim($fieldsName,",").") VALUES(".rtrim($fieldsValue,",").")";
        $rs = mysql_unbuffered_query($sql, $this->Connection);
          
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
    }
      
    public function Encode($str)
    {
        $str = str_replace("//", "////", $str);
        $str = str_replace("'", "''", $str); 
        //addslashes($str); 
        return $str;
    }
      
    public function UpdateByPk( $tableName, &$existingRow)
    {
        $sql = "describe " . $tableName;
        $rs = mysql_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to describe table '".$tableName."',  " . mysql_error());
          
        $updateCause = "";
        $whereCause = "";
          
        $reflectedObj = new ReflectionObject($existingRow);
        $properties = $reflectedObj->getProperties();
        $count = count($properties);
        $fields = array();
        for( $i = 0; $i < $count; $i++)
        {
            $fields = array_merge( $fields, array($properties[$i]->getName() => $properties[$i]) );
        }
          
        $row = mysql_fetch_object($rs);
        while($row)
        {
            if( $reflectedObj->hasProperty($row->Field) )
            {
                $fieldVal = $fields[$row->Field]->getValue($existingRow);
                  
                if( strpos( $row->Type, "varchar") == 0 ||  
                    strpos( $row->Type, "char") == 0 ||  
                    strpos( $row->Type, "datetime") == 0 ||  
                    strpos( $row->Type, "time") == 0 )
                {
                    if( $row->Key == "PRI" )
                        $whereCause = $whereCause . "AND " . $row->Field . "='" . $this->Encode( $fieldVal )."' ";
                    else  
                        $updateCause = $updateCause. $row->Field . "='".$this->Encode( $fieldVal )."',";
                }
                else  
                {
                    if( $row->Key == "PRI" )
                        $whereCause = $whereCause . "AND " . $row->Field . "=" . $fieldVal." ";
                    else  
                        $updateCause = $updateCause. $row->Field . "=".$fieldVal.",";
                }
            }
          
            $row = mysql_fetch_object($rs);
        }
          
        $sql = "UPDATE ".$tableName." SET ".rtrim($updateCause,",")." WHERE 1=1 ".$whereCause."";
        $rs = mysql_unbuffered_query($sql, $this->Connection);
          
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
    }
      
    public function Delete($tableName, $deleteRow)
    {
        $sql = "describe " . $tableName;
        $rs = mysql_query( $sql, $this->Connection);
        if( !$rs ) throw new Exception( "Error, failed to describe table '".$tableName."',  " . mysql_error());
          
        $whereCause = "";
          
        $reflectedObj = new ReflectionObject($deleteRow);
        $properties = $reflectedObj->getProperties();
        $count = count($properties);
        $fields = array();
        for( $i = 0; $i < $count; $i++)
        {
            $fields = array_merge( $fields, array($properties[$i]->getName() => $properties[$i]) );
        }
          
        $row = mysql_fetch_object($rs);
        while($row)
        {
            if( $reflectedObj->hasProperty($row->Field) )
            {
                $fieldVal = $fields[$row->Field]->getValue($deleteRow);
                  
                if( strpos( $row->Type, "varchar") == 0 ||  
                    strpos( $row->Type, "char") == 0 ||  
                    strpos( $row->Type, "datetime") == 0 ||  
                    strpos( $row->Type, "time") == 0 )
                {
                    $whereCause = $whereCause . "AND " . $row->Field . "='" . $this->Encode( $fieldVal )."' ";
                }
                else  
                {
                    $whereCause = $whereCause . "AND " . $row->Field . "=" . $fieldVal." ";
                }
            }
          
            $row = mysql_fetch_object($rs);
        }
          
        if( strlen($whereCause) == 0 )
            throw new Exception("Error, where cause is required!");
          
        $sql = "DELETE FROM ".$tableName." WHERE 1=1 ".$whereCause."";
        $rs = mysql_unbuffered_query($sql, $this->Connection);
          
        if( !$rs ) throw new Exception( "Error, failed to execute query '".$sql."',  " . mysql_error());
    }
}
?>