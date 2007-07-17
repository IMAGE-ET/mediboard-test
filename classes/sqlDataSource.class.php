<?php
 
/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


class CSQLDataSource { 

	static $dataSources     = array();
    
    var $dsn       = null;
    var $link      = null;
    var $chrono    = null;
    var $dbhost    = null;
    var $dbname    = null;
    var $dbuser    = null;
    var $dbpass    = null;
    var $dbport    = null; 
    var $dbpersist = null;
  
  function CDataSource(){
  }
  
  
  function init($dsn) {

    global $AppUI;
    $AppUI->getSystemClass("chrono");
    
    $this->dsn       = $dsn;
    $this->dbhost    = $AppUI->cfg["db"][$dsn]["dbhost"];
    $this->dbname    = $AppUI->cfg["db"][$dsn]["dbname"];
    $this->dbuser    = $AppUI->cfg["db"][$dsn]["dbuser"];
    $this->dbpass    = $AppUI->cfg["db"][$dsn]["dbpass"];
    $this->dbport    = $AppUI->cfg["db"][$dsn]["dbport"];
    $this->dbpersist = $AppUI->cfg["dbpersist"];
    
    $this->link = $this->connect($dsn, $this->dbhost, $this->dbname, $this->dbuser, $this->dbpass,$this->dbport, $this->dbpersist);
  }
  
  
  // Recherche le dataSource s'il existe ou en crée un
  function get($dsn) {  	
  	if(!array_key_exists($dsn, self::$dataSources)){
  	  $dataSource = new CMySQLDataSource();
  	  $dataSource->init($dsn);
  	  self::$dataSources[$dsn] = $dataSource;
  	}
  	return self::$dataSources[$dsn];
  }
  
  
  
  function connect($dsn, $dbhost, $dbname, $dbuser, $dbpass, $dbport, $dbpersist){
    return null;	
  }
	  

  /**
   * Returns a link handler for given sourcename
   * @param string $dsn The DB sourcename
   */
  function db_link($dsn = "std") {
    if (!array_key_exists($dsn, $this->dataSources)) {
      trigger_error( "FATAL ERROR: link to $dsn not found.", E_USER_ERROR );
    }
    return $this->dataSources[$dsn];  
  }

  /**
  * This global function loads the first field of the first row returned by the query.
  *
  * @param string The SQL query
  * @param strong The db identifier
  * @return The value returned in the query or null if the query failed.
  */
  function db_loadResult($sql, $dsn = "std") {
    $cur = $this->db_exec($sql, $dsn);
    $cur or exit($this->db_error());
    $ret = null;
    if ($row = $this->db_fetch_row($cur)) {
      $ret = $row[0];
    }
    $this->db_free_result($cur);
    return $ret;
  }

  
  /**
  * This global function loads the first row of a query into an object 
  *
  * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
  * If <var>object</var> has a value of null, then all of the returned query fields returned in the object. 
  * @param string The SQL query
  * @param object The address of variable
  */
  function db_loadObject($sql, &$object) {
    //global $mbCacheObjectCount;
    $class = get_class($object);
    if ($object != null) {
      $hash = array();
      if(!$this->db_loadHash($sql, $hash)) {
        return false;
      }
      $this->bindHashToObject($hash, $object, false);
      return true;
    } else {
      $cur = $this->db_exec($sql);
      $cur or exit($this->db_error());
      if ($object = $this->db_fetch_object($cur)) {
        $this->db_free_result($cur);
        return true;
      } else {
        $object = null;
        return false;
      }
    }
  }

  
  function db_loadObjectWithOpt($sql, &$object) {
    global $mbCacheObjectCount;
    $class = get_class($object);
    if ($object != null) {
      if($object->_id && isset($object->_objectsTable[$object->_id])) {
        $this->bindObjectToObject($object->_objectsTable[$object->_id], $object);
        $mbCacheObjectCount++;
        return true;
      } else {
        $hash = array();
        if(!$this->db_loadHash($sql, $hash)) {
          return false;
        }
        $this->bindHashToObject($hash, $object, false);
        return true;
      }
    } else {
      $cur = $this->db_exec($sql);
      $cur or exit($this->db_error());
      if ($object = $this->db_fetch_object($cur)) {
        $this->db_free_result($cur);
        return true;
      } else {
        $object = null;
        return false;
      }
    }
  }

  /**
  * This global function return a result row as an associative array 
  *
  * @param string The SQL query
  * @param array An array for the result to be return in
  * @return <b>True</b> is the query was successful, <b>False</b> otherwise
  */
  function db_loadHash($sql, &$hash) {
    $cur = $this->db_exec($sql);
    $cur or exit($this->db_error());
    $hash = $this->db_fetch_assoc($cur);
    $this->db_free_result($cur);
    if ($hash == false) {
      return false;
    } else {
      return true;
    }
  }

  /**
  * Document::db_loadHashList()
  *
  * { Description }
  *
  * @param string $index
  */
  function db_loadHashList($sql, $index = "") {
    $cur = $this->db_exec($sql);
    $cur or exit($this->db_error());
    $hashlist = array();
    while ($hash = $this->db_fetch_array($cur)) {
      $hashlist[$hash[$index ? $index : 0]] = $index ? $hash : $hash[1];
    }
    $this->db_free_result($cur);
    return $hashlist;
  }

  /**
  * Document::db_loadList() 
  *
  * return an array of the db lines
  * can connect to any database via $dsn param 
  *
  * @param int $maxrows
  * @param string the db identifier
  * @return array the query result
  */
  function db_loadList($sql, $maxrows = null, $dsn = "std") {
    //global $AppUI;
    if (!($cur = $this->db_exec($sql, $dsn))) {;
      $AppUI->setMsg($this->db_error(), UI_MSG_ERROR);
      return false;
    }
    $list = array();
    $cnt = 0;
    while ($hash = $this->db_fetch_assoc($cur)) {
      $list[] = $hash;
      if($maxrows && $maxrows == $cnt++) {
        break;
      }
    }
    $this->db_free_result($cur);
    return $list;
  }

  /**
  * Document::db_loadColumn()
  *
  * Loads the first column for a given query
  *
  * @param int $maxrows limit to a maximum nember of rows
  */
  function db_loadColumn($sql, $maxrows = null, $dsn = "std") {
    global $AppUI;
    if (!($cur = $this->db_exec($sql, $dsn))) {
      $AppUI->setMsg($this->db_error($dsn), UI_MSG_ERROR);
      return false;
    }
    $list = array();
    $cnt = 0;
    while ($row = $this->db_fetch_row($cur)) {
      $list[] = $row[0];
      if($maxrows && $maxrows == $cnt++) {
        break;
      }
    }
    $this->db_free_result($cur);
    return $list;
  }

  /* return an array of objects from a SQL SELECT query
   * class must implement the Load() factory, see examples in Webo classes
   * @note to optimize request, only select object oids in $sql
   */
  function db_loadObjectList($sql, $object, $maxrows = null) {
    global $mbCacheObjectCount;
    $cur = $this->db_exec($sql);
    $list = array();
    $cnt = 0;
    $class = get_class($object);
    $table_key = $object->_tbl_key;
    while ($row = $this->db_fetch_array($cur)) {
      $key = $row[$table_key];
      $newObject = new $class();
      $newObject->bind($row, false);
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $list[$newObject->_id] = $newObject;
      if($maxrows && $maxrows == $cnt++) {
        break;
      }
    }
    $this->db_free_result($cur);
    return $list;
  }

  function db_loadObjectListWithOpt($sql, $object, $maxrows = null) {
    global $mbCacheObjectCount;
    $cur = $this->db_exec($sql);
    $list = array();
    $cnt = 0;
    $class = get_class($object);
    $table_key = $object->_tbl_key;
    while ($row = $this->db_fetch_array($cur)) {
      $key = $row[$table_key];
      if(isset($object->_objectsTable[$key])) {
        $list[$key] = $object->_objectsTable[$key];
        $mbCacheObjectCount++;
      } else {
        $newObject = new $class();
        $newObject->bind($row, false);
        $newObject->checkConfidential();
        $newObject->updateFormFields();
        $object->_objectsTable[$newObject->_id] = $newObject;
        $list[$newObject->_id] = $newObject;
      }
      if($maxrows && $maxrows == $cnt++) {
        break;
      }
    }
    $this->db_free_result($cur);
    return $list;
  }


  /**
  * Document::db_insertArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function db_insertArray($table, &$hash, $verbose = false) {
    return null;
  }

  
  /**
  * Document::db_updateArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function db_updateArray($table, &$hash, $keyName, $verbose = false) {
    return null;
  }

  
  /**
  * Document::db_delete()  
  *
  * { Description } 
  *
  */
  function db_delete($table, $keyName, $keyValue) {
    return null;
  }


  /**
  * Document::db_insertObject() 
  *
  * { Description } 
  *
  * @param [type] $keyName
  * @param [type] $verbose
  */
  function db_insertObject($table, &$object, $keyName = null, $verbose = false) {
    return null;
  }

  
  
  /**
  * Document::db_updateObject() 
  *
  * { Description }
  *
  * @param [type] $updateNulls
  */
  function db_updateObject($table, &$object, $keyName) {
    return null;
  }

  /**
  * Document::db_dateConvert() 
  *
  * { Description } 
  *
  */
  function db_dateConvert($src, &$dest, $srcFmt) {
    $result = strtotime($src);
    $dest = $result;
    return ($result != 0);
  }

  /**
  * Document::db_datetime() 
  *
  * { Description } 
  *
  * @param [type] $timestamp
  */
  function db_datetime($timestamp = null) {
    if (!$timestamp) {
     return null;
    }
    if (is_object($timestamp)) {
      return $timestamp->toString("%Y-%m-%d %H:%M:%S");
    } else {
      return strftime("%Y-%m-%d %H:%M:%S", $timestamp);
    }
  }

  /**
  * Document::db_dateTime2locale()  
  *
  * { Description }
  *  
  */
  function db_dateTime2locale($dateTime, $format) {
    if (intval($dateTime)) {
      $date = new CDate($dateTime);
      return $date->format($format);
    } else {
      return null;
    }
  }

  function stripslashes_deep($value) {
    return is_array($value) ?
      array_map("stripslashes_deep", $value) :
      stripslashes($value);
  }

  /**
  * copy the hash array content into the object as properties
  * only existing properties of object are filled. when undefined in hash, properties wont be deleted
  * @param array the input array
  * @param obj byref the object to fill of any class
  * @param string
  * @param boolean
  * @param boolean
  */
  function bindHashToObject($hash, &$obj, $doStripSlashes = true) {
    is_array($hash) or die("bindHashToObject : hash expected");
    is_object($obj) or die("bindHashToObject : object expected");

    foreach (get_object_vars($obj) as $k => $v) {
      if (isset($hash[$k])) {
        if($doStripSlashes){
          $obj->$k = stripslashes_deep($hash[$k]);
        }else{
          $obj->$k = $hash[$k];
        }
      } 
    }
  }

  function bindObjectToObject($obj1, &$obj) {
    is_object($obj1) or die("bindObjectToObject : object expected");
    is_object($obj) or die("bindObjectToObject : object expected");
    foreach ($obj1->getProps() as $k => $v) {
      $obj->$k = $v;
    }
  }

  function db_loadTable($table, $dsn = "std") {
    return null;
  }

  function db_loadField($table, $field, $dsn = "std") {
    return null;
  } 

	
  //-------------------------------------

  
  /**
   * Escapes up to nine values for SQL queries
   * => db_prepare("INSERT INTO table_name VALUES (%)", $value);
   * => db_prepare("INSERT INTO table_name VALUES (%1, %2)", $value1, $value2);
   */
  function db_prepare($sql) {
    $values = func_get_args();
    array_shift($values);
    $trans = array();
    for ($i = 0; $i < count($values); $i++) {
      $escaped = $this->db_escape($values[$i]);
      $quoted = "'$escaped'";
      if ($i == 0) {
        $trans["%"] = $quoted;
      }
      $key = $i+1;
      $trans["%$key"] = $quoted;
    }
    return strtr($sql, $trans);
  }

  
  /**
   * Prepares an IN where clause with a given array of values
   * Prepares a standard = where clause when alternate value is supplied
   */
  function db_prepare_in($values, $alternate = null) {
    return null;
  }
  
  

  function db_error($dsn = "std") {
    return null;
  }

  function db_errno($dsn = "std") {
    return null;
  }

  function db_insert_id($dsn = "std") {
    return null;
  }

  function db_exec($sql, $dsn = "std") {
    return null;
  }

  
  function db_free_result( $cur ) {
	return null;
  }

  function db_num_rows( $qid ) {
	return null;
  }

  function db_affected_rows($dsn = "std" ) {
    return null;
  }

  function db_fetch_row( $cur ) {
	return null;
  }

  function db_fetch_assoc( $cur ) {
    return null;
  }

  function db_fetch_array( $cur  ) {
	return null;
  }

  function db_fetch_object( $cur  ) {
	return null;
  }

  function db_escape( $str ) {
	return null;
  }

  function db_version($dsn = "std") {
    return null;
  }


  function db_unix2dateTime( $time ) {
  	// converts a unix time stamp to the default date format
    return $time > 0 ? date("Y-m-d H:i:s", $time) : null;
  }

  function db_dateTime2unix( $time ) {
	if ($time == "0000-00-00 00:00:00") {
		return -1;
	}
	if( ! preg_match( "/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})(.?)$/", $time, $a ) ) {
		return -1;
	} else {
		return mktime( $a[4], $a[5], $a[6], $a[2], $a[3], $a[1] );
	}
  }
  
}
?>