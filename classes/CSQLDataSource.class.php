<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Abstract class of SQL Data source engines
 */
abstract class CSQLDataSource { 
  static $engines = array (
    "mysql"      => "CMySQLDataSource",
    "mysqli"     => "CMySQLiDataSource",
    "ingres"     => "CIngresDataSource",
    "oracle"     => "COracleDataSource",
    "pdo_sqlsrv" => "CPDOSQLServerDataSource",
    "pdo_mysql"  => "CPDOMySQLDataSource",
    "pdo_oci"    => "CPDOOracleDataSource",
  );

  /** @var CSQLDataSource[] */
  static $dataSources = array();
  static $trace       = false;
  static $report      = false;

  static $report_data = array();

  public $dsn         = null;
  public $link        = null;

  /** @var Chronometer */
  public $chronoInit  = null;

  /** @var Chronometer */
  public $chrono      = null;

  /** @var Chronometer */
  public $chronoFetch = null;

  // Columns to be never quoted: hack for some SQLDataSources unable to cast implicitly
  public $unquotable = array(
    "table" => array("column"),
  );
  
  public $config = array();

  /**
   * Init a chronometer
   */
  function __construct() {
    $this->chronoInit  = new Chronometer();
    $this->chrono      = new Chronometer();
    $this->chronoFetch = new Chronometer();
  }
  
  /**
   * Get the data source with given name.
   * Create it if necessary
   * 
   * @param string $dsn   Data source name
   * @param bool   $quiet Won't trigger errors if true
   * 
   * @return CSQLDataSource|null
   */
  static function get($dsn, $quiet = false) {
    if ($dsn === "std" && CView::$slavestate) {
      $dsn = "slave";
    }

    if (array_key_exists($dsn, self::$dataSources)) {
      return self::$dataSources[$dsn];
    }

    $reporting = null;
    if ($quiet) {
      $reporting = error_reporting(0);
    }

    if (null == $dbtype = CAppUI::conf("db $dsn dbtype")) {
      trigger_error("FATAL ERROR: Undefined type DSN type for '$dsn'.", E_USER_ERROR);
      return null;
    }

    if (empty(self::$engines[$dbtype])) {
      trigger_error("FATAL ERROR: DSN type '$dbtype' unhandled.", E_USER_ERROR);
      return null;
    }

    $dsClass = self::$engines[$dbtype];

    /** @var self $dataSource */
    $dataSource = new $dsClass;
    $dataSource->init($dsn);
    self::$dataSources[$dsn] = $dataSource->link ? $dataSource : null;

    if ($quiet) {
      error_reporting($reporting);
    }

    return self::$dataSources[$dsn];
  }
  
  /**
   * Create connection to database
   * 
   * @param string $host Host name
   * @param string $name Database name
   * @param string $user Database user name
   * @param string $pass Database user password
   * 
   * @return resource|null Database link
   */
  abstract function connect($host, $name, $user, $pass);

  /**
   * Launch the actual query
   * 
   * @param string $query SQL query
   * 
   * @return resource result
   */
  abstract function query($query);
  
  /**
   * Rename a table
   * 
   * @param string $old Old name
   * @param string $new New name
   * 
   * @return bool job done 
   */
  abstract function renameTable($old, $new);
  
  /**
   * Get the first table like given name
   * 
   * @param string $table Table name
   */
  abstract function loadTable($table);

  /**
   * Get all tables with given prefix
   * 
   * @param array[string] $table Table names 
   */
  abstract function loadTables($table = null);
  
  /**
   * Get the first field like given name in table
   * 
   * @param string $table The table
   * @param string $field The field
   */
  abstract function loadField($table, $field);
  
  /**
   * Get the last error message
   * 
   * @return string The message
   */
  abstract function error();

  /**
   * Get the last error number
   * 
   * @return integer The number
   */
  abstract function errno();

  /**
   * Get the last autoincremented id
   * 
   * @return integer The Id
   */
  abstract function insertId();

  /**
   * Free a query result
   * 
   * @param resource $result The result to free
   */
  abstract function freeResult($result);

  /**
   * Returns number of rows for given result
   * 
   * @param resource $result Query result
   * 
   * @return int the rows count
   */
  abstract function numRows($result);
  
  /**
   * Returns number of rows for given result
   * 
   * @return int the rows count
   */
  abstract function foundRows();
 
  /**
   * Number of rows affected by last query
   * 
   * @return int the actual number
   */
  abstract function affectedRows();

  /**
   * Get a result row as an enumerative array
   * 
   * @param resource $result Query result
   * 
   * @return array the result
   */
  abstract function fetchRow($result);

  /**
   * Get a result row as an associative array
   * 
   * @param resource $result Query result
   * 
   * @return array the result
   */
  abstract function fetchAssoc($result);

  /**
   * Get a result row as both associative and enumerative array
   * 
   * @param resource $result Query result
   * 
   * @return array the result
   */
  abstract function fetchArray($result);

  /**
   * Get a result row as an object
   * 
   * @param resource $result Query result
   * @param string   $class  The class of the returned object
   * @param array    $params Params to be passed to the $class constructor 
   * 
   * @return object the result
   */
  abstract function fetchObject($result, $class = null, $params = array());

  /**
   * Escape value
   * 
   * @param string $value The value to escape
   * 
   * @return string the escaped value
   */
  abstract function escape($value);

  /**
   * Get the DB engine version
   * 
   * @return string the version number
   */
  abstract function version();  
  
  /**
   * Prepares a LIKE clause with a given value to search
   * 
   * @param string $value The LIKE value to prepare
   * 
   * @return string The prepared like clause
   */
  abstract function prepareLike($value);

  /**
   * Get queries for creation of a base on the server and a user with access to it
   *
   * @param string $user        User name
   * @param string $pass        User password
   * @param string $base        database name
   * @param string $client_host Client host name
   *
   * @return array key-named queries
   */
  abstract function queriesForDSN($user, $pass, $base, $client_host);
  
  /**
   * Initialize a data source by creating the link to the data base
   * 
   * @param string $dsn The data source name to init
   * 
   * @return void
   */
  function init($dsn) {
    $this->dsn = $dsn;
    $this->config = CAppUI::conf("db $dsn");

    $this->chronoInit->start();
    $this->link = $this->connect(
      $this->config["dbhost"],
      $this->config["dbname"],
      $this->config["dbuser"],
      $this->config["dbpass"]
    );
    $this->chronoInit->stop();

    if (!$this->link) {
      trigger_error("FATAL ERROR: link to '$this->dsn' not found.", E_USER_ERROR);
    }
  }
  
  /**
   * Execute a any query
   * 
   * @param string $query SQL Query
   * 
   * @return resource The result resource on SELECT, true on others, false if failed 
   **/
  function exec($query) {
    // Query colouring
    if (CSQLDataSource::$trace) {
      echo utf8_decode(CMbString::highlightCode("sql", $query, false, "white-space: pre-wrap;"));
    }
    
    // Chrono
    $this->chrono->start();
    $result = $this->query($query);
    $this->chrono->stop();
    
    // Error handling
    if (!$result) {
      trigger_error($this->error()." on SQL query <em>$query</em>", E_USER_WARNING);
      return false;
    }
  
    // Chrono messaging
    if (CSQLDataSource::$trace) {
      $step = $this->chrono->latestStep * 1000;
      $total = $this->chrono->total * 1000;
      
      $pace = floor(2*log10($step));
      $pace = max(0, min(6, $pace));
      $message = "query-pace-$pace";
      $type = floor(($pace+3)/2);
      CAppUI::stepMessage($type, $message, $this->dsn, $step, $total);
    }

    if (CSQLDataSource::$report) {
      $hash = self::hashQuery($query);
      if (!isset(self::$report_data[$hash])) {
        self::$report_data[$hash]   = array();
      }

      self::$report_data[$hash][]   = array(
        $this->chrono->latestStep * 1000,
        $query,
      );
    }

    return $result;
  }

  /**
   * Makes a hash from the query
   *
   * @param string $query The query to hash
   *
   * @return string
   */
  static function hashQuery($query) {
    $query = preg_replace('/\s+/', " ", $query);
    $query = preg_replace('/\'[^\']*\'/', "%", $query);
    $query = preg_replace('/IN ?\([%, ]+\)/', "IN (%)", $query);
    $query = preg_replace('/ \d+/', " %", $query);

    return md5($query);
  }

  /**
   * Displays the SQL report
   *
   * @return void
   */
  static function displayReport() {
    $totals = array();
    $distribution = array();
    foreach (self::$report_data as $_hash => $_data) {
      $totals[$_hash] = array_sum(CMbArray::pluck($_data, 0));

      $_distribution = array(
        "","","","","","","","","",
      );
      foreach ($_data as $_pair) {
        $duration = $_pair[0] * 1000;
        $log = (int)floor(log10($duration)+.5);

        if (!isset($_distribution[$log])) {
          $_distribution[$log] = "";
        }
        $_distribution[$log] .= "#";
      }

      $distribution[$_hash] = $_distribution;
    }

    arsort($totals);

    foreach ($totals as $_hash => $_total) {
      CAppUI::stepMessage(UI_MSG_OK, "Query was called %d times for %01.3fms", count(self::$report_data[$_hash]), $_total);
      echo utf8_decode(CMbString::highlightCode("sql", self::$report_data[$_hash][0][1], false, "white-space: pre-wrap;"));
      $_dist = $distribution[$_hash];
      // No input for 1탎 and 10탎 magnitudes (< 31.6탎)
      $lines = array(
        "100탎 $_dist[2]",
        "  1ms $_dist[3]",
        " 10ms $_dist[4]",
        "100ms $_dist[5]",
        "   1s $_dist[6]",
        "  10s $_dist[7]",
      );

      echo "<pre>".implode("\n", $lines)."</pre>";
    }
  }
  
  /**
   * Query an SQL dump
   * Will fail to and exit to the first error
   * 
   * @param string $dumpPath  The dump path
   * @param bool   $utfDecode Set to true if the $dumpPath data is encoded in UTF-8
   * 
   * @return int Number of queried lines, false if failed
   */
  function queryDump($dumpPath, $utfDecode = false) {
    $sqlLines  = file($dumpPath);
    $query     = "";
    $nbQueries = 0;
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
      if (substr($sqlLine, 0, 2) === "--" || substr($sqlLine, 0, 1) === "#") {
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
        $nbQueries++;
        $query = "";
      }
    }  

    return $nbQueries;
  }

  /**
   * Loads the first field of the first row returned by the query.
   * 
   * @param string $sql The SQL query
   * 
   * @return string|null The value returned in the query or null if the query failed.
   */
  function loadResult($sql) {
    $cur = $this->exec($sql);
    $cur or CApp::rip();

    $this->chronoFetch->start();

    $ret = null;
    if ($row = $this->fetchRow($cur)) {
      $ret = reset($row);
    }

    $this->chronoFetch->stop();
    
    $this->freeResult($cur);
    return $ret;
  }

  
  /**
   * Loads the first row of a query into an object 
   *
   * If an object is passed to this function, the returned row is bound to the existing elements of $object.
   * If $object has a value of null, then all of the returned query fields returned in the object. 
   * 
   * @param string $sql     The SQL query
   * @param object &$object The address of variable
   *
   * @return bool
   */
  function loadObject($sql, &$object) {
    if ($object != null) {
      if (null == $hash = $this->loadHash($sql)) {
        return false;
      }
      
      bindHashToObject($hash, $object);
      return true;
    }
    else {
      $cur = $this->exec($sql);
      $cur or CApp::rip();

      $this->chronoFetch->start();

      $object = $this->fetchObject($cur);

      $this->chronoFetch->stop();

      $this->freeResult($cur);

      if ($object) {
        return true;
      }
      else {
        $object = null;
        return false;
      }
    }
  }

  /**
   * Execute query and returns first result row as array 
   * 
   * @param string $query The SQL query
   * 
   * @return array The hash, false if failed
   */
  function loadHash($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();

    $this->chronoFetch->start();

    $hash = $this->fetchAssoc($cur);

    $this->chronoFetch->stop();

    $this->freeResult($cur);
    return $hash;
  }

  /**
   * Returns a array as result of query where column 0 is key and column 1 is value
   * 
   * @param string $query The SQL query
   *
   * @return array
   */
  function loadHashList($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();

    $this->chronoFetch->start();

    $hashlist = array();
    while ($hash = $this->fetchArray($cur)) {
      $hashlist[$hash[0]] = $hash[1];
    }

    $this->chronoFetch->stop();

    $this->freeResult($cur);
    return $hashlist;
  }

  /**
   * Returns a recursive array tree as result of query where successive columns are branches of the tree
   *
   * @param string $query The SQL query
   *
   * @return array
   */
  function loadTree($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();

    $this->chronoFetch->start();

    $tree = array();
    while ($columns = $this->fetchRow($cur)) {
      $branch =& $tree;
      $leaf = array_pop($columns);
      foreach ($columns as $_column) {
        if (!isset($branch[$_column])) {
          $branch[$_column] = array();
        }
        $branch =& $branch[$_column];
      }
      $branch = $leaf;
    }

    $this->chronoFetch->stop();

    $this->freeResult($cur);
    return $tree;
  }

  /**
   * Returns a array as result of query where column 0 is key and all columns are values
   * 
   * @param string $query The SQL query
   *
   * @return array
   */
  function loadHashAssoc($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();

    $this->chronoFetch->start();

    $hashlist = array();
    while ($hash = $this->fetchAssoc($cur)) {
      $key = reset($hash);
      $hashlist[$key] = $hash;
    }

    $this->chronoFetch->stop();

    $this->freeResult($cur);
    return $hashlist;
  }
  

  /**
   * Return a list of associative array as the query result
   * 
   * @param string $query   The SQL query
   * @param int    $maxrows Maximum number of rows to return
   * 
   * @return array the query result
   */
  function loadList($query, $maxrows = null) {
    if (null == $result = $this->exec($query)) {
      CAppUI::setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }

    $this->chronoFetch->start();

    $list = array();
    while ($hash = $this->fetchAssoc($result)) {
      $list[] = $hash;
      if ($maxrows == count($list)) {
        break;
      }
    }

    $this->chronoFetch->stop();
    
    $this->freeResult($result);
    return $list;
  }
  
  function countRows($query) {
    if (null == $result = $this->exec($query)) {
      CAppUI::setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }

    $this->chronoFetch->start();

    $count = $this->numRows($result);

    $this->chronoFetch->stop();

    $this->freeResult($result);
    return $count;
  }

  /**
   * Return a array of the first column of the query result
   * 
   * @param string $query   The SQL query
   * @param int    $maxrows Maximum number of rows to return
   * 
   * @return array the query result
   */
  function loadColumn($query, $maxrows = null) {
    if (null == $cur = $this->exec($query)) {
      CAppUI::setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }

    $this->chronoFetch->start();
    
    $list = array();
    while ($row = $this->fetchRow($cur)) {
      $list[] = $row[0];
      if ($maxrows && $maxrows == count($list)) {
        break;
      }
    }

    $this->chronoFetch->stop();
    
    $this->freeResult($cur);
    return $list;
  }
    
  /**
   * Insert a row matching object fields
   * null and underscored vars are skipped
   * 
   * @param string $table          The table name
   * @param object $object         The object with fields
   * @param array  $vars           The array containing the object's values
   * @param string $keyName        The variable name of the key to set
   * @param bool   $insert_delayed Parameter of INSERT
   *
   * @return bool job done
   */
  function insertObject($table, $object, $vars, $keyName = null, $insert_delayed = false/*, $updateDuplicate = false*/) {
    if (CAppUI::conf("readonly")  || $this->dsn === "slave") {
      return false;
    }
    
    $fields = array();
    $values = array();
    
    foreach ($vars as $k => $v) {
      // Skip null, arrays and objects
      if ($v === null || is_array($v) || is_object($v)) {
        continue;
      }
      
      // Skip underscored
      if ($k[0] === "_") {
        continue;
      }
      
      // Skip empty vars
      if ($v === "" && $k !== $keyName) {
        continue;
      }
      
      $v = $this->escape($v);

      // Quote everything
      $this->quote($table, $k, $v);
      
      // Build array
      $fields[] = $k;
      $values[] = $v;
    }

    $fields_str = implode(",", $fields);
    $values_str = implode(",", $values);
    $delayed    = $insert_delayed ? " DELAYED " : "";
    $query = "INSERT $delayed INTO $table ($fields_str) VALUES ($values_str)";

    // Update object on duplicate key
    /*if ($updateDuplicate) {
      $update = array();
      foreach ($fields as $_field) {
        if (trim($_field, "`") !== $keyName) {
          $update[] = "$_field = VALUES($_field)";
        }
      }

      if (count($update)) {
        $query .= " ON DUPLICATE KEY UPDATE ".implode(", ", $update);
      }
    }*/

    $result = $this->exec($query);
    if (!$result) {
      return false;
    }

    // Valuate id
    $id = $this->insertId();
    if ($keyName && $id) {
      $object->$keyName = $id;
    }
    
    return true;
  }

  function deleteObject($table, $keyName, $keyValue) {
    if (CAppUI::conf("readonly")  || $this->dsn === "slave") {
      return false;
    }

    $query = "DELETE FROM $table WHERE $keyName = '$keyValue'";

    return $this->exec($query);
  }
  
  function insertMulti($table, $data, $step, $trim = true){
    $counter = 0;
    
    $keys = array_keys(reset($data));
    $fields = "`".implode("`, `", $keys)."`";
    
    $count_data = count($data);
    
    foreach ($data as $_data) {
      if ($counter % $step == 0) {
        $query = "INSERT INTO `$table` ($fields) VALUES ";
        $queries = array();
      }
      
      $_query = array();
      foreach ($_data as $_value) {
        if ($trim) {
          $_value = trim($_value);

          if ($_value === "") {
            $_query[] = "NULL";
          }
          else {
            $_query[] = "'".$this->escape($_value)."'";
          }
        }
        else {
          if ($_value === null) {
            $_query[] = "NULL";
          }
          else {
            $_query[] = "'".$this->escape($_value)."'";
          }
        }
      }
      
      $queries[] = "(".implode(", ", $_query).")";
      
      $counter++;
      
      if ($counter % $step == 0 || $counter == $count_data) {
        $query .= implode(",", $queries);
        $query .= ";";

        $result = $this->exec($query);
        if (!$result) {
          throw new CMbException($this->error());
        }
      }
    }
  }
  
  /**
   * Quote columns and values 
   * 
   * @param string $table Table name
   * @param mixed  &$k    in/out column name
   * @param string &$v    in/out column value
   */
  function quote($table, &$k, &$v) {
    if (!isset($this->unquotable[$table]) || !in_array($k, $this->unquotable[$table])) {
      $v = "'$v'";
    }
    $k = "`$k`";
  }

  /**
   * Update a row matching object fields
   * null and underscored vars are skipped
   *
   * @param string $table               The table name
   * @param object $object              The object with fields
   * @param array  $vars                The array containing the object's values
   * @param string $keyName             The variable name of the key to set
   * @param bool   $nullifyEmptyStrings Whether to nullify empty values
   *
   * @return bool job done
   */
  function updateObject($table, $vars, $keyName, $nullifyEmptyStrings = true) {
    if (CAppUI::conf("readonly") || $this->dsn === "slave") {
      return false;
    }
    
    $tmp = array();
    
    foreach ($vars as $k => $v) {
      // Where clause on key name
      if ($k === $keyName) { 
        $where = "`$keyName`='" . $this->escape($v) . "'";
        continue;
      }
      
      // Skip null, arrays and objects
      if ($v === null || is_array($v) || is_object($v)) {
        continue;
      }
      
      // Skip underscored
      if ($k[0] === "_") { // internal field
        continue;
      }
      
      $v = $this->escape($v);
      
      // Quote everything
      $this->quote($table, $k, $v);

      // Nullify empty values or escape
      $v = ($nullifyEmptyStrings && $v === "''") ? "NULL" : $v;
        
      $tmp[] = "$k=$v";
    }
    
    // No updates to make;
    if (!count($tmp)) {
      return true;
    }
    
    $values = implode(",", $tmp);
    $query = "UPDATE $table SET $values WHERE $where";

    $result = $this->exec($query);
    if (!$result) {
      return false;
    }

    return true;
  }
  
  /**
   * Escapes up to nine values for SQL queries
   * => prepare("INSERT INTO table_name VALUES (%)", $value);
   * => prepare("INSERT INTO table_name VALUES (%1, %2)", $value1, $value2);
   * 
   * @param string $query The query
   * @param string ...    Unlimited values
   * 
   * @return string The prepared query
   */
  function prepare($query) {
    $values = func_get_args();
    array_shift($values);
    $trans = array();
    
    for ($i = 0; $i < count($values); $i++) {
      $escaped = $this->escape($values[$i]);
      $quoted = "'$escaped'";
      if ($i == 0) {
        $trans["%"] = $quoted;
        $trans["?"] = $quoted;
      }
      $key = $i+1;
      $trans["%$key"] = $quoted;
      $trans["?$key"] = $quoted;
    }
    
    return strtr($query, $trans);
  }
  
  function getDBstruct($table, $field = null, $reduce_strings = false){
    $list_fields = $this->loadList("SHOW COLUMNS FROM `{$table}`");
    $fields = array();
    
    foreach ($list_fields as $curr_field) {
      if (!$field) {
        continue;
      }
      
      $field_name = $curr_field['Field'];
      $fields[$field_name] = array();
      
      $_field =& $fields[$field_name];
      
      $props = CMbFieldSpec::parseDBSpec($curr_field['Type']);
      
      $_field['type']     = $props['type'];
      $_field['unsigned'] = $props['unsigned'];
      $_field['zerofill'] = $props['zerofill'];
      $_field['null']     = ($curr_field['Null'] != 'NO');
      $_field['default']  = $curr_field['Default'];
      $_field['index']    = null;
      $_field['extra']    = $curr_field['Extra'];
      
      if ($reduce_strings && is_array($props['params'])) {
        foreach ($props['params'] as &$v) {
          if ($v[0] === "'") {
            $v = trim($v, "'");
          }
          else {
            $v = (int)$v;
          }
        }
      }
      
      $_field['params'] = $props['params'];
      
      if ($field === $field_name) {
        return $_field;
      }
    }
    
    return $fields;
  }

  /**
   * Prepares an IN where clause with a given array of values
   * Prepares a standard where clause when alternate value is supplied
   * 
   * @param array  $values    The values to include in the IN clause
   * @param string $alternate An alternate value
   * 
   * @return string The prepared where clause
   */
  static function prepareIn($values, $alternate = null) {
    if ($alternate) {
      return "= '$alternate'";
    }
    
    // '0' = '1' is multi base compatible
    if (!count($values)) {
      return "IS NULL AND '0' = '1'";
    }
    
    $quoted = array();
    foreach ($values as $value) {
      $quoted[] = "'$value'";
    }
    
    $str = implode(", ", $quoted);
    return "IN ($str)";
  }

  /**
   * Prepares an LIKE where clause with a given name-like value
   * tread all non non-characters as % wildcards
   * 
   * @param string $name The value to include in the LIKE clause
   * 
   * @return string The prepared where clause
   */
  function prepareLikeName($name) {
    return $this->prepare("LIKE %", preg_replace("/[\W]+/", "_", $name));
  }

  /**
   * Prepares an NOT IN where clause with a given array of values
   * Prepares a standard where clause when alternate value is supplied
   * 
   * @param array  $values    An array of values to include in the IN clause
   * @param string $alternate An alternate value
   * 
   * @return string The prepared where clause
   */
  static function prepareNotIn($values, $alternate = null) {
    if ($alternate) {
      return "<> '$alternate'";
    }
    
    if (!count($values)) {
      return "IS NOT NULL AND 1";
    }

    $quoted = array();
    foreach ($values as $value) {
      $quoted[] = "'$value'";
    }

    $str = implode(", ", $quoted);
    return "NOT IN ($str)";
  }
  
  static function getReplaceQuery($search, $replace, $subject) {
    if (!is_array($search)) {
      $search = array($search);
    }
    else {
      $search = array_values($search); // to have contiguous keys
    }
    
    if (!is_array($replace)) {
      $replace = array($replace);
    }
    else {
      $replace = array_values($replace); // to have contiguous keys
    }
    
    $query = "";
    
    foreach ($search as $_search) {
      $query .= "REPLACE( \n";
    }
    
    $query .= $subject; // can be of the form "foo" or foo or `foo`
        
    $replace_count = count($replace);
    foreach ($search as $i => $_search) {
      $query .= ", '".addslashes($_search)."', '".addslashes($replace[$i % $replace_count])."') \n";
    }
    
    return $query;
  }
  
  static function tempTableDates($date_min, $date_max) {
    if (!$date_min && !$date_max) {
      return;
    }
    
    $date_temp = $date_min;
    $dates = array();
    
    while ($date_temp <= $date_max) {
      $dates[] = "('$date_temp')";
      $date_temp = CMbDT::date("+1 day", $date_temp);
    }
    
    $ds = CSQLDataSource::get("std");
    
    $tab_name = substr(uniqid("dates_"), 0, 7);
    
    $query = "CREATE TEMPORARY TABLE $tab_name (date date not null);";
    $ds->exec($query);
    
    $query = "INSERT INTO $tab_name VALUES " . implode(",", $dates) . ";";
    $ds->exec($query);
    
    return $tab_name;
  } 
}
