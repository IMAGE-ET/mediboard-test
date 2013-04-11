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
 * Class CPDODataSource
 */
abstract class CPDODataSource extends CSQLDataSource {
  protected $driver_name;
  protected $affected_rows;

  /**
   * Indicates if the connection is in a "dry run".
   *
   * @var bool
   */
  protected $pretending = false;

  /**
   * @var PDO
   */
  public $link;

  /**
   * Connection
   *
   * @param string $host
   * @param string $name
   * @param string $user
   * @param string $pass
   *
   * @return PDO|resource
   */
  function connect($host, $name, $user, $pass) {
    if (!class_exists("PDO")) {
      trigger_error("FATAL ERROR: PDO support not available. Please check your configuration.", E_USER_ERROR);
      return;
    }

    $dsn = "$this->driver_name:dbname=$name;host=$host";
    $this->link = new PDO($dsn, $user, $pass);

    return $this->link;
  }

  function error() {
    $errorInfo = $this->link->errorInfo();
    return $errorInfo[2];
  }

  function errno() {
    return $this->link->errorCode();
  }

  function insertId() {
    return $this->link->lastInsertId();
  }

  function query($query) {
    $stmt = $this->link->query($query);

    if ($stmt !== false) {
      $this->affected_rows = $stmt->rowCount();
    }

    return $stmt;
  }

  function freeResult($result) {
    //$result->free();
  }

  function numRows($result) {
    return $result->rowCount();
  }

  function affectedRows() {
    return $this->affected_rows;
  }

  function foundRows() {
    // No such implementation
    return;
  }

  function fetchRow($result) {
    return $result->fetch(PDO::FETCH_NUM);
  }

  function fetchAssoc($result) {
    return $result->fetch(PDO::FETCH_ASSOC);
  }

  function fetchArray($result) {
    return $result->fetch(PDO::FETCH_BOTH);
  }

  function fetchObject($result, $class_name = null, $params = array()) {
    if (empty($class_name)) {
      return $result->fetchObject();
    }

    if (empty($params)) {
      return $result->fetchObject($class_name);
    }

    return $result->fetchObject($class_name, $params);
  }

  function escape($value) {
    return substr($this->link->quote($value), 1, -1); // remove the quotes around
    /*
    return strtr($value, array(
      "'" => "''",
      '"' => '\"',
    ));*/
  }

  function prepareLike($value) {
    $value = preg_replace('`\\\\`', '\\\\\\', $value);
    return $this->prepare("LIKE %", $value);
  }

  function version() {
    return $this->link->server_info;
  }

  function renameTable($old, $new) {
    $query = "ALTER TABLE `$old` RENAME TO `$new`";
    return $this->exec($query);
  }

  function loadTable($table) {
    $query = $this->prepare("SHOW TABLES LIKE %", $table);
    return $this->loadResult($query);
  }

  function loadTables($table = "") {
    $query = $this->prepare("SHOW TABLES LIKE %", "$table%");
    return $this->loadColumn($query);
  }

  function loadField($table, $field) {
    $query = $this->prepare("SHOW COLUMNS FROM `$table` LIKE %", $field);
    return $this->loadResult($query);
  }

  function queriesForDSN($user, $pass, $base) {
    $queries = array();
    $host = "localhost";

    // Create database
    $queries["create-db"] = "CREATE DATABASE `$base` ;";

    // Create user with global permissions
    $queries["global-privileges"] = 
      "GRANT USAGE
        ON * . * 
        TO '$user'@'$host'
        IDENTIFIED BY '$pass';";

    // Grant user with database permissions
    $queries["base-privileges"] = 
      "GRANT ALL PRIVILEGES
        ON `$base` . *
        TO '$user'@'$host';";

    return $queries;
  }

  /**
   * Get the used grammar
   *
   * @return mixed
   */
  abstract function getQueryGrammar();

  /**
   * Get the used processor
   *
   * @return CSQLProcessor
   */
  function getProcessor() {
    return new CSQLProcessor();
  }

  /**
   * Get a new raw query expression.
   *
   * @param mixed $value Expression value
   *
   * @return CSQLExpression
   */
  public function raw($value) {
    return new CSQLExpression($value);
  }

  /**
   * Run a select statement and return a single result.
   *
   * @param string $query       Query to execute
   * @param array  $bindings    Binding params
   * @param int    $fetchMode   Fetch mode
   * @param null   $objectClass Object class to fetch in
   *
   * @return mixed|null
   */
  public function selectOne($query, $bindings = array(), $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    $records = $this->select($query, $bindings, $fetchMode, $objectClass);

    return count($records) > 0 ? reset($records) : null;
  }

  /**
   * Run a select statement against the database.
   *
   * @param string $query       Query to execute
   * @param array  $bindings    Binding params
   * @param int    $fetchMode   Fetch mode
   * @param null   $objectClass Object class to fetch in
   *
   * @return mixed
   */
  /*
  public function select($query, $bindings = array(), $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    return $this->run(
      $query,
      $bindings,
      function ($me, $query, $bindings, $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
        if ($me->pretending()) {
          return array();
        }

        // For select statements, we'll simply execute the query and return an array
        // of the database result set. Each element in the array will be a single
        // row from the database table, and will either be an array or objects.
        $statement = $me->getPdo()->prepare($query);

        $statement->execute($me->prepareBindings($bindings));

        if (!$objectClass) {
          return $statement->fetchAll($fetchMode);
        }
        return $statement->fetchAll($fetchMode, $objectClass);
      },
      $fetchMode,
      $objectClass
    );
  }
  */

  public function select($query, $bindings = array(), $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    return $this->run(
      $query,
      $bindings,
      'selectClosure',
      array($query, $bindings, $fetchMode, $objectClass),
      $fetchMode,
      $objectClass
    );
  }

  public function selectClosure($query, $bindings, $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    if ($this->pretending()) {
      return array();
    }

    // For select statements, we'll simply execute the query and return an array
    // of the database result set. Each element in the array will be a single
    // row from the database table, and will either be an array or objects.
    $statement = $this->getPdo()->prepare($query);

    $statement->execute($this->prepareBindings($bindings));

    if (!$objectClass) {
      return $statement->fetchAll($fetchMode);
    }

    return $statement->fetchAll($fetchMode, $objectClass);
  }

  /**
   * Run an insert statement against the database.
   *
   * @param string $query    Query to execute
   * @param array  $bindings Binding params
   *
   * @return bool
   */
  public function insert($query, $bindings = array()) {
    return $this->statement($query, $bindings);
  }

  /**
   * Run an update statement against the database.
   *
   * @param string $query    Query to execute
   * @param array  $bindings Binding params
   *
   * @return int
   */
  public function update($query, $bindings = array()) {
    return $this->affectingStatement($query, $bindings);
  }

  /**
   * Run a delete statement against the database.
   *
   * @param string $query    Query to execute
   * @param array  $bindings Binding params
   *
   * @return int
   */
  public function delete($query, $bindings = array()) {
    return $this->affectingStatement($query, $bindings);
  }

  /**
   * Execute an SQL statement and return the boolean result.
   *
   * @param string $query    Query to execute
   * @param array  $bindings Binding params
   *
   * @return bool
   */
  /*
  public function statement($query, $bindings = array()) {
    return $this->run(
      $query,
      $bindings,
      function ($me, $query, $bindings) {
        if ($me->pretending()) {
          return true;
        }

        $bindings = $me->prepareBindings($bindings);

        return $me->getPdo()->prepare($query)->execute($bindings);
      }
    );
  }
  */

  public function statement($query, $bindings = array()) {
    return $this->run(
      $query,
      $bindings,
      'statementClosure',
      array($query, $bindings)
    );
  }

  public function statementClosure($query, $bindings) {
    if ($this->pretending()) {
      return true;
    }

    $bindings = $this->prepareBindings($bindings);

    return $this->getPdo()->prepare($query)->execute($bindings);
  }

  /**
   * Run an SQL statement and get the number of rows affected.
   *
   * @param string $query    Query to execute
   * @param array  $bindings Binding params
   *
   * @return int
   */
  /*
  public function affectingStatement($query, $bindings = array()) {
    return $this->run(
      $query,
      $bindings,
      function ($me, $query, $bindings) {
        if ($me->pretending()) {
          return 0;
        }

        // For update or delete statements, we want to get the number of rows affected
        // by the statement and return that back to the developer. We'll first need
        // to execute the statement and then we'll use PDO to fetch the affected.
        $statement = $me->getPdo()->prepare($query);

        $statement->execute($me->prepareBindings($bindings));

        return $statement->rowCount();
      }
    );
  }
  */

  public function affectingStatement($query, $bindings = array()) {
    return $this->run(
      $query,
      $bindings,
      'affectingStatementClosure',
      array($query, $bindings)
    );
  }

  public function affectingStatementClosure($query, $bindings) {
    if ($this->pretending()) {
      return 0;
    }

    // For update or delete statements, we want to get the number of rows affected
    // by the statement and return that back to the developer. We'll first need
    // to execute the statement and then we'll use PDO to fetch the affected.
    $statement = $this->getPdo()->prepare($query);

    $statement->execute($this->prepareBindings($bindings));

    return $statement->rowCount();
  }

  /**
   * Run a raw, unprepared query against the PDO connection.
   *
   * @param string $query Query to execute
   *
   * @return bool
   */
  /*
  public function unprepared($query) {
    return $this->run(
      $query,
      array(),
      function ($me, $query, $bindings) {
        if ($me->pretending()) {
          return true;
        }

        return (bool) $me->getPdo()->exec($query);
      }
    );
  }
  */

  public function unprepared($query) {
    return $this->run(
      $query,
      array(),
      'unpreparedClosure',
      array($query)
    );
  }

  public function unpreparedClosure($query) {
    if ($this->pretending()) {
      return true;
    }

    return (bool) $this->getPdo()->exec($query);
  }

  /**
   * Prepare the query bindings for execution.
   *
   * @param array $bindings Binding params
   *
   * @return array
   */
  public function prepareBindings(array $bindings) {
    $grammar = $this->getQueryGrammar();

    foreach ($bindings as $key => $value) {
      // We need to transform all instances of the DateTime class into an actual
      // date string. Each query grammar maintains its own date string format
      // so we'll just ask the grammar for the format to get from the date.
      if ($value instanceof DateTime) {
        $bindings[$key] = $value->format($grammar->getDateFormat());
      }
      elseif ($value === false) {
        $bindings[$key] = 0;
      }
    }

    return $bindings;
  }

  /**
   * Execute a Closure within a transaction.
   *
   * @param callable $callback Callback function to call
   *
   * @return array|bool|int
   * @throws Exception
   */
  /*
  public function transaction(Closure $callback) {
    $this->link->beginTransaction();

    // We'll simply execute the given callback within a try / catch block
    // and if we catch any exception we can rollback the transaction
    // so that none of the changes are persisted to the database.
    try {
      $result = $callback($this);

      $this->link->commit();
    }

    // If we catch an exception, we will roll back so nothing gets messed
    // up in the database. Then we'll re-throw the exception so it can
    // be handled how the developer sees fit for their applications.
    catch (Exception $e) {
      $this->link->rollBack();

      throw $e;
    }

    return $result;
  }
  */

  public function transaction($callback) {
    $this->link->beginTransaction();

    // We'll simply execute the given callback within a try / catch block
    // and if we catch any exception we can rollback the transaction
    // so that none of the changes are persisted to the database.
    try {
      $result = call_user_func(array($this, $callback));

      $this->link->commit();
    }

    // If we catch an exception, we will roll back so nothing gets messed
    // up in the database. Then we'll re-throw the exception so it can
    // be handled how the developer sees fit for their applications.
    catch (Exception $e) {
      $this->link->rollBack();

      throw $e;
    }

    return $result;
  }

  public function transactionClosure() {}

  /**
   * Execute the given callback in "dry run" mode.
   *
   * @param Closure $callback Callback function to call
   *
   * @return array
   */
  /*
  public function pretend(Closure $callback) {
    $this->pretending = true;

    // Basically to make the database connection "pretend", we will just return
    // the default values for all the query methods, then we will return an
    // array of queries that were "executed" within the Closure callback.
    $callback($this);

    $this->pretending = false;

    return $this;
  }
  */

  public function pretend(Closure $callback) {
    $this->pretending = true;

    // Basically to make the database connection "pretend", we will just return
    // the default values for all the query methods, then we will return an
    // array of queries that were "executed" within the Closure callback.
    call_user_func(array($this, $callback));

    $this->pretending = false;

    return $this;
  }

  public function pretendClosure() {}

  /**
   * Run a SQL statement and log its execution context.
   *
   * @param string   $query       Query to execute
   * @param array    $bindings    Binding params
   * @param callable $callback    Callback function to call
   * @param int      $fetchMode   Fetch mode
   * @param null     $objectClass Object class to fetch in
   *
   * @return array|bool|int
   */
  /*
  protected function run($query, $bindings, Closure $callback, $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    // To execute the statement, we'll simply call the callback, which will actually
    // run the SQL against the PDO connection. Then we can calculate the time it
    // took to execute and log the query SQL, bindings and time in our memory.
    try {
      $result = $callback($this, $query, $bindings, $fetchMode, $objectClass);
    }
    catch (Exception $e) {
      $this->handleQueryException($e, $query, $bindings);
    }

    return $result;
  }
  */

  protected function run($query, $bindings, $callback, $params = array(), $fetchMode = PDO::FETCH_ASSOC, $objectClass = null) {
    // To execute the statement, we'll simply call the callback, which will actually
    // run the SQL against the PDO connection. Then we can calculate the time it
    // took to execute and log the query SQL, bindings and time in our memory.
    $params[] = $fetchMode;
    $params[] = $objectClass;
    try {
      $result = call_user_func_array(array($this, $callback), $params);
    }
    catch (Exception $e) {
      $this->handleQueryException($e, $query, $bindings);
    }

    return $result;
  }

  /**
   * Handle an exception that occurred during a query.
   *
   * @param Exception $e        Exception
   * @param string    $query    Query to execute
   * @param array     $bindings Binding params
   *
   * @throws Exception
   */
  protected function handleQueryException(Exception $e, $query, $bindings) {
    $bindings = var_export($bindings, true);

    $message = $e->getMessage()." (SQL: {$query}) (Bindings: {$bindings})";

    throw new Exception($message, 0);
  }

  /**
   * Get the currently used PDO connection.
   *
   * @return PDO
   */
  public function getPdo() {
    return $this->link;
  }

  /**
   * Determine if the connection in a "dry run".
   *
   * @return bool
   */
  public function pretending() {
    return $this->pretending === true;
  }
}
