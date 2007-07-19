<?php
 
/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


abstract class CSQLDataSource { 

	static $dataSources = array();
	static $trace = false;
    
  public $dsn       = null;
  public $link      = null;
  public $chrono    = null;

  function CSQLDataSource(){
  }
  
  /**
   * Get the data source with given name.
   * Create it if necessary
   * @return CSQLDataSource
   */
  static function get($dsn) {  	
  	if(!array_key_exists($dsn, self::$dataSources)){
  	  $dataSource = new CMySQLDataSource();
  	  $dataSource->init($dsn);
  	  self::$dataSources[$dsn] = $dataSource;
  	}
  	return self::$dataSources[$dsn];
  }
  
  /**
   * Initialize a data source by creating the link to the data base
   */
  function init($dsn) {
    $this->dsn = $dsn;
    
    global $dPconfig;
    $dsConfig = $dPconfig["db"][$dsn];
    
    $this->link = $this->connect($dsn, 
	    $dsConfig["dbhost"],
	    $dsConfig["dbname"],
	    $dsConfig["dbuser"],
	    $dsConfig["dbpass"],
	    $dsConfig["dbport"],
	    $dPconfig["dbpersist"]
    );
    
    if (!$this->link) {
      trigger_error( "FATAL ERROR: link to $this->dsn not found.", E_USER_ERROR );
    }
    
    $this->chrono = new Chronometer;
  }
  
  /**
   * Create connection to database
   * @param string $dsn
   * @param string $host
   * @param string $name
   * @param string $user
   * @param string $pass
   * @param string $port
   * @param bool $persist 
   */
  abstract function connect($dsn, $host, $name, $user, $pass, $port, $persist);
	
  /**
   * Query an SQL dump
   * Will fail to and exit to the first error
   * @param string $dumpPath the dump path
   * @return int number of queried lines, false if failed
   */
  function queryDump($dumpPath, $utfDecode = false) {
		$sqlLines = file($dumpPath);
		$query = "";
		foreach ($sqlLines as $lineNumber => $sqlLine) {
		  $sqlLine = trim($sqlLine);
		  if ($utfDecode) {
  		  $sqlLine = utf8_decode($sqlLine);
		  }

		  // Remove empty lignes
		  if (!$sqlLine) {
		    continue;
		  }
		  
		  // Remove comment lines
      if (substr($sqlLine, 0, 2) == "--" || substr($sqlLine, 0, 1) == "#") {
        continue;
      }

      $query .= $sqlLine;
      
      // Query at line end
	    if (preg_match("/;\s*$/", $sqlLine)) {
	      $this->exec($query);
	      if ($msg = $this->error()) {
	        trigger_error("Error reading dump on line $lineNumber : $msg");
	        return false;
	      }
	      $query = "";
	    }
		}  

		return $lineNumber;
  }

  /**
  * This global function loads the first field of the first row returned by the query.
  *
  * @param string The SQL query
  * @param strong The db identifier
  * @return The value returned in the query or null if the query failed.
  */
  function loadResult($sql) {
    $cur = $this->exec($sql);
    $cur or exit($this->error());
    $ret = null;
    if ($row = $this->fetchRow($cur)) {
      $ret = $row[0];
    }
    $this->freeResult($cur);
    return $ret;
  }

  
  /**
  * This global function loads the first row of a query into an object 
  *
  * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
  * If <var>object</var> has a value of null, then all of the returned query fields returned in the object. 
  * @param string The SQL query
  * @param object The address of variable
  **/
  function loadObject($sql, &$object) {
    //global $mbCacheObjectCount;
    $class = get_class($object);
    if ($object != null) {
      if (null == $hash = $this->loadHash($sql)) {
        return false;
      }
      
      $this->bindHashToObject($hash, $object, false);
      return true;
    } else {
      $cur = $this->exec($sql);
      $cur or exit($this->error());
      if ($object = $this->fetchObject($cur)) {
        $this->freeResult($cur);
        return true;
      } else {
        $object = null;
        return false;
      }
    }
  }

  /**
   * Execute query and returns first result row as array 
   * @param string $query
   * @return array The hash, false if failed
   **/
  function loadHash($query) {
    $cur = $this->exec($query);
    $cur or exit($this->error());
    $hash = $this->fetchAssoc($cur);
    $this->freeResult($cur);
    return $hash;
  }

  /**
   * Returns a array as result of query
   * where column 0 is key and column 1 is value
   * @param string $query
   **/
  function loadHashList($query) {
    $cur = $this->exec($query);
    $cur or exit($this->error());
    $hashlist = array();
    while ($hash = $this->fetchArray($cur)) {
      $hashlist[$hash[0]] = $hash[1];
    }
    $this->freeResult($cur);
    return $hashlist;
  }

  /**
   * Return a list of associative array as the query result
   * @param string $query
   * @param int $maxrows
   * @return array the query result
   **/
  function loadList($query, $maxrows = null) {
    global $AppUI;
    if (null == $cur = $this->exec($query)) {
      $AppUI->setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }
    
    $list = array();
    while ($hash = $this->fetchAssoc($cur)) {
      $list[] = $hash;
      if ($maxrows && $maxrows == count($list)) {
        break;
      }
    }
    
    $this->freeResult($cur);
    return $list;
  }

  /**
   * Return a array of the first column of the query result
   * @param string $query
   * @param int $maxrows
   * @return array the query result
   **/
  function loadColumn($query, $maxrows = null) {
    global $AppUI;
    if (null == $cur = $this->exec($query)) {
      $AppUI->setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }
    
    $list = array();
    while ($row = $this->fetchRow($cur)) {
      $list[] = $row[0];
      if ($maxrows && $maxrows == count($list)) {
        break;
      }
    }
    
    $this->freeResult($cur);
    return $list;
  }

  /**
  * Document::insertArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function insertArray($table, &$hash, $verbose = false) {
    return null;
  }

  
  /**
  * Document::updateArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function updateArray($table, &$hash, $keyName, $verbose = false) {
    return null;
  }

  
  /**
  * Document::delete()  
  *
  * { Description } 
  *
  */
  function delete($table, $keyName, $keyValue) {
    return null;
  }


  /**
  * Document::insertObject() 
  *
  * { Description } 
  *
  * @param [type] $keyName
  * @param [type] $verbose
  */
  function insertObject($table, &$object, $keyName = null, $verbose = false) {
    return null;
  }

  
  
  /**
  * Document::updateObject() 
  *
  * { Description }
  *
  * @param [type] $updateNulls
  */
  function updateObject($table, &$object, $keyName) {
    return null;
  }

  /**
  * Document::dateConvert() 
  *
  * { Description } 
  *
  */
  function dateConvert($src, &$dest, $srcFmt) {
    $result = strtotime($src);
    $dest = $result;
    return ($result != 0);
  }

  /**
  * Document::datetime() 
  *
  * { Description } 
  *
  * @param [type] $timestamp
  */
  function datetime($timestamp = null) {
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
  * Document::dateTime2locale()  
  *
  * { Description }
  *  
  */
  function dateTime2locale($dateTime, $format) {
    if (intval($dateTime)) {
      $date = new CDate($dateTime);
      return $date->format($format);
    } else {
      return null;
    }
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

  function loadTable($table) {
    return null;
  }

  function loadField($table, $field) {
    return null;
  } 

  
  /**
   * Escapes up to nine values for SQL queries
   * => prepare("INSERT INTO table_name VALUES (%)", $value);
   * => prepare("INSERT INTO table_name VALUES (%1, %2)", $value1, $value2);
   */
  function prepare($sql) {
    $values = func_get_args();
    array_shift($values);
    $trans = array();
    for ($i = 0; $i < count($values); $i++) {
      $escaped = $this->escape($values[$i]);
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
  function prepareIn($values, $alternate = null) {
    return null;
  }
  
  /**
   * The last error message
   * @return string the message
   */
  abstract function error();

  function errno() {
    return null;
  }

  function insertId() {
    return null;
  }

  /**
   * Execute a query
   * @param string $query
   * @return resource The result resource on SELECT, true on others, false if failed 
   */
  function exec($query) {
    return null;
  }

  
  function freeResult( $cur ) {
	return null;
  }

  /**
   * Returns number of rows for given result
   * @param resource $result Query result
   * @return int the rows count
   */
  function numRows($result) {
	  return null;
  }

  /**
   * Number of rows affected by last query
   * @return int the actual number
   */
  function affectedRows() {
    return null;
  }

  function fetchRow( $cur ) {
	return null;
  }

  function fetchAssoc( $cur ) {
    return null;
  }

  function fetchArray( $cur  ) {
	return null;
  }

  function fetchObject( $cur  ) {
	return null;
  }

  function escape( $str ) {
	return null;
  }

  function version() {
    return null;
  }
  
}
?>