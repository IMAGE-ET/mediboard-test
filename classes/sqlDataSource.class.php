<?php
 
/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


abstract class CSQLDataSource { 
  static $engines = array (
    "mysql" => "CMySQLDataSource",
    "ingres" => "CIngresDataSource",
  );
	static $dataSources = array();
	static $trace = false;
    
  public $dsn       = null;
  public $link      = null;
  public $chrono    = null;
  
  public $config = array();

  function __construct() {
    $this->chrono = new Chronometer;
  }
  
  /**
   * Get the data source with given name.
   * Create it if necessary
   * @return CSQLDataSource
   */
  static function get($dsn) {  	
  	if (!array_key_exists($dsn, self::$dataSources)) {
      global $dPconfig;
  	  if (null == $dbtype = @$dPconfig["db"][$dsn]["dbtype"]) {
        trigger_error( "FATAL ERROR: Undefined type DSN type '$dsn'.", E_USER_ERROR );
        die;
      }

  	  if (null == $dsClass = @self::$engines[$dbtype]) {
        trigger_error( "FATAL ERROR: DSN type '$dbtype' unhandled.", E_USER_ERROR );
        die;
  	  }

  	  $dataSource = new $dsClass;
  	  $dataSource->init($dsn);
  	  self::$dataSources[$dsn] = $dataSource;
  	}
  	return self::$dataSources[$dsn];
  }
  
  /**
   * Create connection to database
   * @param string $dsn
   * @param string $host
   * @param string $name
   * @param string $user
   * @param string $pass
   */
  abstract function connect($host, $name, $user, $pass);

  /**
   * Launch the actual query
   * @param string $query SQL query
   * @return resource result
   */
  abstract function query($query);
  
  /**
   * Get the first table like given name
   * @param string $table 
   */
  abstract function loadTable($table);

  /**
   * Get the first field like given name in table
   * @param string $table
   * @param string $field 
   **/
  abstract function loadField($table, $field);
  
  /**
   * Get the last error message
   * @return string the message
   */
  abstract function error();

  /**
   * Get the last error number
   * @return int The number
   **/
  abstract function errno();

  /**
   * Get the last autoincremented id
   * @return int The Id
   */
  abstract function insertId();

  /**
   * Free a query result
   * @param resource $result
   **/
  abstract function freeResult($result);

  /**
   * Returns number of rows for given result
   * @param resource $result Query result
   * @return int the rows count
   **/
  abstract function numRows($result);

  /**
   * Number of rows affected by last query
   * @return int the actual number
   */
  abstract function affectedRows();

  /**
   * Get a result row as an enumerative array
   * @param resource $result Query result
   * @return array the result
   **/
  abstract function fetchRow($result);

  /**
   * Get a result row as an associative array
   * @param resource $result Query result
   * @return array the result
   **/
  abstract function fetchAssoc($result);

  /**
   * Get a result row as both associative and enumerative array
   * @param resource $result Query result
   * @return array the result
   **/
  abstract function fetchArray($result);

  /**
   * Get a result row as an object
   * @param resource $result Query result
   * @return object the result
   **/
  abstract function fetchObject($result);

  /**
   * Escape value
   * @param string $value
   * @return string the escaped value
   **/
  abstract function escape($value);

  /**
   * Get the DB engine version
   * @return string the version number
   **/
  abstract function version();  
  
  /**
   * Get queries for creation of a base on the server and a user with access to it
   * @param string $user user name
   * @param string $pass user password
   * @param string $base database name
   * @return array key-named queries
   */
  abstract function queriesForDSN($user, $pass, $base); 
  
  /**
   * Initialize a data source by creating the link to the data base
   */
  function init($dsn) {
    $this->dsn = $dsn;
    
    global $dPconfig;
    $this->config = $dPconfig["db"][$dsn];
    
    
    $this->chrono->start = new Chronometer;
    $this->link = $this->connect(
	    $this->config["dbhost"],
	    $this->config["dbname"],
	    $this->config["dbuser"],
	    $this->config["dbpass"]
    );
    
    if (!$this->link) {
      trigger_error( "FATAL ERROR: link to '$this->dsn' not found.", E_USER_ERROR );
    }
  }
  
  /**
   * Execute a any query
   * @param string $query SQL Query
   * @return resource The result resource on SELECT, true on others, false if failed 
   **/
    function exec($query) {
    if (CSQLDataSource::$trace) {
      trigger_error("Exécution SQL : $query", E_USER_NOTICE);
    }
    
    $this->chrono->start();
    $result = $this->query($query);
    $this->chrono->stop();

    if (!$result) {
      trigger_error("Exécution SQL : $query", E_USER_NOTICE);
      trigger_error("Erreur SQL : ".$this->error(), E_USER_WARNING);
      return false;
    }
  
	  return $result;
  }
  
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
   * @param string The SQL query
   * @param strong The db identifier
   * @return The value returned in the query or null if the query failed.
   */
  function loadResult($sql) {
    $cur = $this->exec($sql);
    $cur or exit($this->error());
    $ret = null;
    if ($row = $this->fetchRow($cur)) {
      $ret = reset($row);
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
    $class = get_class($object);
    if ($object != null) {
      if (null == $hash = $this->loadHash($sql)) {
        return false;
      }
      
      bindHashToObject($hash, $object, false);
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
      if ($maxrows == count($list)) {
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
   * Insert a row matching object fields
   * null and underscored vars are skipped
   * @param string $table The table name
   * @param object $object The object with fields
   * @param string $keyName The var name of the key to set
   * @return bool job done
   **/
  function insertObject($table, &$object, $keyName = null) {
    global $dPconfig;
    if ($dPconfig["readonly"]) {
      return false;
    }
    
    foreach (get_object_vars($object) as $k => $v) {
      // Skip null, arrays and objects
      if ($v === null || is_array($v) || is_object($v)) {
        continue;
      }
      
      // Skip underscored
      if ($k[0] == "_") {
        continue;
      }
      
      $v = trim($v);
      
      // Skip empty vars
      if ($v === "" && $k != $keyName) {
        continue;
      }
      
      $fields[] = "`$k`";
      $values[] = "'" . $this->escape($v) . "'";
    }
    
    $fields = implode(",", $fields);
    $values = implode(",", $values);
    
    $query = "INSERT INTO $table ($fields) VALUES ($values)";
   
    if (!$this->exec($query)) {
      return false;
    }

    // Valuate id
    $id = $this->insertId();
    if ($keyName && $id) {
      $object->$keyName = $id;
    }
    
    return true;
  }
  
  /**
   * Update a row matching object fields
   * null and underscored vars are skipped
   * @param string $table The table name
   * @param object $object The object with fields
   * @param string $keyName The var name of the key to set
   * @return bool job done
   **/
  function updateObject($table, &$object, $keyName) {
    global $dPconfig;
    if ($dPconfig["readonly"]) {
      return false;
    }
    
    $tmp = array();
    foreach (get_object_vars($object) as $k => $v) {
      // Where clause on key name
      if ($k == $keyName) { 
        $where = "`$keyName`='" . $this->escape($v) . "'";
        continue;
      }
      
      // Skip null, arrays and objects
      if ($v === null || is_array($v) || is_object($v)) {
        continue;
      }
      
      // Skip underscored
      if ($k[0] == "_") { // internal field
        continue;
      }

      $v = trim($v);

      // Nullify empty values or escape
      $val = $v === "" ? "NULL" : "'" . $this->escape($v) . "'";
        
      $tmp[] = "`$k`=$val";
    }
    
    $values = implode(",", $tmp);
    $query = "UPDATE $table SET $values WHERE $where";
 
    return $this->exec($query);
    
  }
  
  /**
   * Escapes up to nine values for SQL queries
   * => prepare("INSERT INTO table_name VALUES (%)", $value);
   * => prepare("INSERT INTO table_name VALUES (%1, %2)", $value1, $value2);
   * @param $string $query
   * @param $string Unlimited values
   * @return strin The prepared query
   **/
  function prepare($query) {
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
    return strtr($query, $trans);
  }

  /**
   * Prepares an IN where clause with a given array of values
   * Prepares a standard where clause when alternate value is supplied
   * @param array $values
   * @param string $alternate
   * @return string The prepared where clause
   **/
  function prepareIn($values, $alternate = null) {
    if ($alternate) {
      return "= '$alternate'";
    }
    
    if (!count($values)) {
      return "IS NULL AND 0";
    }
    
    $str = join($values, ", ");
    return "IN ($str)";
  }
}
?>