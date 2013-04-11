<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link       http://www.mediboard.org
 */

/**
 * Class CSQLQuery
 * Based from : Laravel Framework
 */
class CSQLQuery {

  /**
   * The database connection instance.
   *
   * @var CSQLDataSource
   */
  protected $connection;

  /**
   * The database query grammar instance.
   *
   * @var CSQLGrammar
   */
  protected $grammar;

  /**
   * The database query post processor instance.
   *
   * @var CSQLProcessor
   */
  protected $processor;

  /**
   * The current query value bindings.
   *
   * @var array
   */
  protected $bindings = array();

  /**
   * An aggregate function and column to be run.
   *
   * @var array
   */
  public $aggregate;

  /**
   * The columns that should be returned.
   *
   * @var array
   */
  public $columns;

  /**
   * Indicates if the query returns distinct results.
   *
   * @var bool
   */
  public $distinct = false;

  /**
   * The table which the query is targeting.
   *
   * @var string
   */
  public $from;

  /**
   * The table joins for the query.
   *
   * @var array
   */
  public $joins;

  /**
   * The where constraints for the query.
   *
   * @var array
   */
  public $wheres;

  /**
   * The groupings for the query.
   *
   * @var array
   */
  public $groups;

  /**
   * The having constraints for the query.
   *
   * @var array
   */
  public $havings;

  /**
   * The orderings for the query.
   *
   * @var array
   */
  public $orders;

  /**
   * The maximum number of records to return.
   *
   * @var int
   */
  public $limit;

  /**
   * The number of records to skip.
   *
   * @var int
   */
  public $offset;

  /**
   * All of the available clause operators.
   *
   * @var array
   */
  protected $operators = array(
    '=', '<', '>', '<=', '>=', '<>', '!=',
    'like', 'not like', 'between', 'ilike',
  );

  /**
   * Primary key field name
   *
   * @var string
   */
  public $primaryKeyField;

  /**
   * Object class to fetch in
   *
   * @var string
   */
  public $classToFetch;

  /**
   * Fetch mode
   *
   * @var string
   */
  public $fetchMode = PDO::FETCH_CLASS;

  /**
   * Create a new query CSQLQuery instance.
   *
   * @param CPDODataSource $connection      PDO object
   * @param string         $from            From table name
   * @param string         $primaryKeyField Primary key field
   * @param string         $classToFetch    Object class to fetch in
   */
  public function __construct(CPDODataSource $connection, $from = null, $primaryKeyField = "id", $classToFetch = null) {
    $this->connection       = $connection;
    $this->grammar          = $connection->getQueryGrammar();
    $this->processor        = $connection->getProcessor();

    $this->from             = $from;
    $this->primaryKeyField  = $primaryKeyField;

    $this->classToFetch      = $classToFetch;
  }

  /**
   * Set the columns to be selected.
   *
   * @param array $columns Columns to select
   *
   * @return CSQLQuery
   */
  /*
  public function select($columns = array('*')) {
    $this->columns = is_array($columns) ? $columns : func_get_args();

    return $this;
  }
  */

  public function select($columns = array('*')) {
    $args = func_get_args();
    $this->columns = is_array($columns) ? $columns : $args;

    return $this;
  }

  /**
   * Force the query to only return distinct results.
   *
   * @return CSQLQuery
   */
  public function distinct() {
    $this->distinct = true;

    return $this;
  }

  /**
   * Set the table which the query is targeting.
   *
   * @param string $table Table name
   *
   * @return CSQLQuery
   */
  public function from($table) {
    $this->from = $table;

    return $this;
  }

  /**
   * Add a join clause to the query.
   *
   * @param string $table    Table to join
   * @param string $first    Right operand
   * @param string $operator Operator
   * @param string $second   Left operand
   * @param string $type     Join type
   *
   * @return CSQLQuery
   */
  public function join($table, $first, $operator = null, $second = null, $type = 'inner') {
    // If the first "column" of the join is really a Closure instance the developer
    // is trying to build a join with a complex "on" clause containing more than
    // one condition, so we'll add the join and call a Closure with the query.
    if ($first instanceof Closure) {
      $this->joins[] = new CSQLJoinClause($type, $table);

      call_user_func($first, end($this->joins));
    }
    // If the column is simply a string, we can assume the join simply has a basic
    // "on" clause with a single condition. So we will just build the join with
    // this simple join clauses attached to it. There is not a join callback.
    else {
      $join = new CSQLJoinClause($type, $table);

      $join->on($first, $operator, $second);

      $this->joins[] = $join;
    }

    return $this;
  }

  /**
   * Add a left join to the query.
   *
   * @param string $table    Table to join
   * @param string $first    Right operand
   * @param string $operator Operator
   * @param string $second   Left operand
   *
   * @return CSQLQuery
   */
  public function leftJoin($table, $first, $operator = null, $second = null) {
    return $this->join($table, $first, $operator, $second, 'left');
  }

  /**
   * Add a basic where clause to the query.
   *
   * @param string $column   Column to search in
   * @param string $operator Operator
   * @param mixed  $value    Value to search
   * @param string $boolean  Operator to add
   *
   * @return CSQLQuery
   */
  public function where($column, $operator = null, $value = null, $boolean = 'and') {
    // If the columns is actually a Closure instance, we will assume the developer
    // wants to begin a nested where statement which is wrapped in parenthesis.
    // We'll add that Closure to the query then return back out immediately.
    if ($column instanceof Closure) {
      return $this->whereNested($column, $boolean);
    }

    // If the given operator is not found in the list of valid operators we will
    // assume that the developer is just short-cutting the '=' operators and
    // we will set the operators to '=' and set the values appropriately.
    if ( ! in_array(strtolower($operator), $this->operators, true)) {
      list($value, $operator) = array($operator, '=');
    }

    // If the value is a Closure, it means the developer is performing an entire
    // sub-select within the query and we will need to compile the sub-select
    // within the where clause to get the appropriate query record results.
    if ($value instanceof Closure) {
      return $this->whereSub($column, $operator, $value, $boolean);
    }

    // If the value is "null", we will just assume the developer wants to add a
    // where null clause to the query. So, we will allow a short-cut here to
    // that method for convenience so the developer doesn't have to check.
    if (is_null($value)) {
      return $this->whereNull($column, $boolean);
    }

    // Now that we are working with just a simple query we can put the elements
    // in our array and add the query binding to our array of bindings that
    // will be bound to each SQL statements when it is finally executed.
    $type = 'Basic';

    $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

    if ( ! $value instanceof CSQLExpression) {
      $this->bindings[] = $value;
    }

    return $this;
  }

  /**
   * Add an "or where" clause to the query.
   *
   * @param string $column   Column to search in
   * @param string $operator Operator
   * @param mixed  $value    Value to search
   *
   * @return CSQLQuery
   */
  public function orWhere($column, $operator = null, $value = null) {
    return $this->where($column, $operator, $value, 'or');
  }

  /**
   * Add a raw where clause to the query.
   *
   * @param string $sql      SQL string
   * @param array  $bindings Binding params
   * @param string $boolean  Operator to add
   *
   * @return CSQLQuery
   */
  public function whereRaw($sql, array $bindings = array(), $boolean = 'and') {
    $type = 'raw';

    $this->wheres[] = compact('type', 'sql', 'boolean');

    $this->bindings = array_merge($this->bindings, $bindings);

    return $this;
  }

  /**
   * Add a raw or where clause to the query.
   *
   * @param string $sql      SQL string
   * @param array  $bindings Binding params
   *
   * @return CSQLQuery
   */
  public function orWhereRaw($sql, array $bindings = array()) {
    return $this->whereRaw($sql, $bindings, 'or');
  }

  /**
   * Add a where between statement to the query.
   *
   * @param string $column  Column to search in
   * @param array  $values  Values to search
   * @param string $boolean Operator to add
   *
   * @return CSQLQuery
   */
  public function whereBetween($column, array $values, $boolean = 'and') {
    $type = 'between';

    $this->wheres[] = compact('column', 'type', 'boolean');

    $this->bindings = array_merge($this->bindings, $values);

    return $this;
  }

  /**
   * Add an or where between statement to the query.
   *
   * @param string $column Column to search in
   * @param array  $values Values to search
   *
   * @return CSQLQuery
   */
  public function orWhereBetween($column, array $values) {
    return $this->whereBetween($column, $values, 'or');
  }

  /**
   * Add a nested where statement to the query.
   *
   * @param Closure $callback Callback function to call
   * @param string  $boolean  Operator to add
   *
   * @return CSQLQuery
   */
  /*
  public function whereNested(Closure $callback, $boolean = 'and') {
    // To handle nested queries we'll actually create a brand new query instance
    // and pass it off to the Closure that we have. The Closure can simply do
    // do whatever it wants to a query then we will store it for compiling.
    $type = 'Nested';

    $query = $this->newQuery();

    $query->from($this->from);

    call_user_func($callback, $query);

    // Once we have let the Closure do its things, we can gather the bindings on
    // the nested query CSQLQuery and merge them into these bindings since they
    // need to get extracted out of the children and assigned to the array.
    $this->wheres[] = compact('type', 'query', 'boolean');

    $this->mergeBindings($query);

    return $this;
  }
  */

  /**
   * Add a full sub-select to the query.
   *
   * @param string  $column   Column
   * @param string  $operator Operator
   * @param Closure $callback Callback function to call
   * @param string  $boolean  Operator to add beforwe WHERE
   *
   * @return CSQLQuery
   */
  /*
  protected function whereSub($column, $operator, Closure $callback, $boolean) {
    $type = 'Sub';

    $query = $this->newQuery();

    // Once we have the query instance we can simply execute it so it can add all
    // of the sub-select's conditions to itself, and then we can cache it off
    // in the array of where clauses for the "main" parent query instance.
    call_user_func($callback, $query);

    $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

    $this->mergeBindings($query);

    return $this;
  }
  */

  /**
   * Add an exists clause to the query.
   *
   * @param Closure $callback Callback function to call
   * @param string  $boolean  Operator to add
   * @param bool    $not      Exists or NotExists
   *
   * @return CSQLQuery
   */
  /*
  public function whereExists(Closure $callback, $boolean = 'and', $not = false) {
    $type = $not ? 'NotExists' : 'Exists';

    $query = $this->newQuery();

    // Similar to the sub-select clause, we will create a new query instance so
    // the developer may cleanly specify the entire exists query and we will
    // compile the whole thing in the grammar and insert it into the SQL.
    call_user_func($callback, $query);

    $this->wheres[] = compact('type', 'operator', 'query', 'boolean');

    $this->mergeBindings($query);

    return $this;
  }
  */

  /**
   * Add an or exists clause to the query.
   *
   * @param Closure $callback Callback function to call
   * @param bool    $not      Exists or notExists
   *
   * @return CSQLQuery
   */
  /*
  public function orWhereExists(Closure $callback, $not = false) {
    return $this->whereExists($callback, 'or', $not);
  }
  */

  /**
   * Add a where not exists clause to the query.
   *
   * @param Closure $callback Callback function to call
   * @param string  $boolean  Operator to add
   *
   * @return CSQLQuery
   */
  /*
  public function whereNotExists(Closure $callback, $boolean = 'and') {
    return $this->whereExists($callback, $boolean, true);
  }
  */

  /**
   * Add a where not exists clause to the query.
   *
   * @param Closure $callback Callback function to call
   *
   * @return CSQLQuery
   */
  /*
  public function orWhereNotExists(Closure $callback) {
    return $this->orWhereExists($callback, true);
  }
  */

  /**
   * Add a "where in" clause to the query.
   *
   * @param string $column  Column to search in
   * @param mixed  $values  Values to search
   * @param string $boolean Operator to add
   * @param bool   $not     In or NotIn
   *
   * @return CSQLQuery
   */
  public function whereIn($column, $values, $boolean = 'and', $not = false) {
    $type = $not ? 'NotIn' : 'In';

    // If the value of the where in clause is actually a Closure, we will assume that
    // the developer is using a full sub-select for this "in" statement, and will
    // execute those Closures, then we can re-construct the entire sub-selects.
    if ($values instanceof Closure) {
      return $this->whereInSub($column, $values, $boolean, $not);
    }

    $this->wheres[] = compact('type', 'column', 'values', 'boolean');

    $this->bindings = array_merge($this->bindings, $values);

    return $this;
  }

  /**
   * Add an "or where in" clause to the query.
   *
   * @param string $column Column to search in
   * @param mixed  $values Values to search
   *
   * @return CSQLQuery
   */
  public function orWhereIn($column, $values) {
    return $this->whereIn($column, $values, 'or');
  }

  /**
   * Add a "where not in" clause to the query.
   *
   * @param string $column  Column to search in
   * @param mixed  $values  Values to search
   * @param string $boolean Operator to add
   *
   * @return CSQLQuery
   */
  public function whereNotIn($column, $values, $boolean = 'and') {
    return $this->whereIn($column, $values, $boolean, true);
  }

  /**
   * Add an "or where not in" clause to the query.
   *
   * @param string $column Column to search in
   * @param mixed  $values Values to search
   *
   * @return CSQLQuery
   */
  public function orWhereNotIn($column, $values) {
    return $this->whereNotIn($column, $values, 'or');
  }

  /**
   * Add a where in with a sub-select to the query.
   *
   * @param string  $column   Column to search in
   * @param Closure $callback Callback function to call
   * @param string  $boolean  Operator to add
   * @param bool    $not      NotInSub or InSub
   *
   * @return CSQLQuery
   */
  /*
  protected function whereInSub($column, Closure $callback, $boolean, $not) {
    $type = $not ? 'NotInSub' : 'InSub';

    // To create the exists sub-select, we will actually create a query and call the
    // provided callback with the query so the developer may set any of the query
    // conditions they want for the in clause, then we'll put it in this array.
    call_user_func($callback, $query = $this->newQuery());

    $this->wheres[] = compact('type', 'column', 'query', 'boolean');

    $this->mergeBindings($query);

    return $this;
  }
  */

  /**
   * Add a "where null" clause to the query.
   *
   * @param string $column  Column to search in
   * @param string $boolean Operator to add
   * @param bool   $not     NotNull or Null
   *
   * @return CSQLQuery
   */
  public function whereNull($column, $boolean = 'and', $not = false) {
    $type = $not ? 'NotNull' : 'Null';

    $this->wheres[] = compact('type', 'column', 'boolean');

    return $this;
  }

  /**
   * Add an "or where null" clause to the query.
   *
   * @param string $column Column to search in
   *
   * @return CSQLQuery
   */
  public function orWhereNull($column) {
    return $this->whereNull($column, 'or');
  }

  /**
   * Add a "where not null" clause to the query.
   *
   * @param string $column  Column to search in
   * @param string $boolean Operator to add
   *
   * @return CSQLQuery
   */
  public function whereNotNull($column, $boolean = 'and') {
    return $this->whereNull($column, $boolean, true);
  }

  /**
   * Add an "or where not null" clause to the query.
   *
   * @param string $column Column to search in
   *
   * @return CSQLQuery
   */
  public function orWhereNotNull($column) {
    return $this->whereNotNull($column, 'or');
  }

  /**
   * Add a "group by" clause to the query.
   *
   * @return CSQLQuery
   */
  /*
  public function groupBy() {
    $this->groups = array_merge((array) $this->groups, func_get_args());

    return $this;
  }
  */

  public function groupBy() {
    $args = func_get_args();
    $this->groups = array_merge((array) $this->groups, $args);

    return $this;
  }

  /**
   * Add a "having" clause to the query.
   *
   * @param string $column   Column to search in
   * @param string $operator Operator
   * @param string $value    Value to search
   *
   * @return CSQLQuery
   */
  public function having($column, $operator = null, $value = null) {
    $type = 'basic';

    $this->havings[] = compact('type', 'column', 'operator', 'value');

    $this->bindings[] = $value;

    return $this;
  }

  /**
   * Add a raw having clause to the query.
   *
   * @param string $sql      SQL string
   * @param array  $bindings Binding params
   * @param string $boolean  Operator to add
   *
   * @return CSQLQuery
   */
  public function havingRaw($sql, array $bindings = array(), $boolean = 'and') {
    $type = 'raw';

    $this->havings[] = compact('type', 'sql', 'boolean');

    $this->bindings = array_merge($this->bindings, $bindings);

    return $this;
  }

  /**
   * Add a raw or having clause to the query.
   *
   * @param string $sql      SQL string
   * @param array  $bindings Binding params
   *
   * @return CSQLQuery
   */
  public function orHavingRaw($sql, array $bindings = array()) {
    return $this->havingRaw($sql, $bindings, 'or');
  }

  /**
   * Add an "order by" clause to the query.
   *
   * @param string $column    Column name
   * @param string $direction Order direction
   *
   * @return CSQLQuery
   */
  public function orderBy($column, $direction = 'asc') {
    $this->orders[] = compact('column', 'direction');

    return $this;
  }

  /**
   * Set the "offset" value of the query.
   *
   * @param int $value Number of results to skip
   *
   * @return CSQLQuery
   */
  public function skip($value) {
    $this->offset = $value;

    return $this;
  }

  /**
   * Set the "limit" value of the query.
   *
   * @param int $value Number of results to take
   *
   * @return CSQLQuery
   */
  public function take($value) {
    $this->limit = $value;

    return $this;
  }

  /**
   * Set the limit and offset for a given page.
   *
   * @param int $page    Page number
   * @param int $perPage Offset
   *
   * @return CSQLQuery
   */
  public function forPage($page, $perPage = 15) {
    return $this->skip(($page - 1) * $perPage)->take($perPage);
  }

  /**
   * Get the SQL representation of the query.
   *
   * @return string
   */
  public function toSql() {
    return $this->grammar->compileSelect($this);
  }

  /**
   * Execute a query for a single record by ID.
   *
   * @param int   $id      ID value
   * @param array $columns Columns to select
   *
   * @return mixed
   */
  public function find($id, $columns = array('*')) {
    return $this->where($this->primaryKeyField, '=', $id)->first($columns);
  }

  /**
   * Pluck a single column from the database.
   *
   * @param string $column Column to pluck
   *
   * @return mixed
   */
  public function pluck($column) {
    $result = (array) $this->first(array($column));

    return count($result) > 0 ? reset($result) : null;
  }

  /**
   * Execute the query and get the first result.
   *
   * @param array $columns Columns to get
   *
   * @return mixed
   */
  public function first($columns = array('*')) {
    $results = $this->take(1)->get($columns);

    return count($results) > 0 ? reset($results) : null;
  }

  /**
   * Execute the query as a "select" statement.
   *
   * @param array $columns Columns to get
   *
   * @return array
   */
  public function get($columns = array('*')) {
    // If no columns have been specified for the select statement, we will set them
    // here to either the passed columns, or the standard default of retrieving
    // all of the columns on the table using the "wildcard" column character.
    if (is_null($this->columns)) {
      $this->columns = $columns;
    }

    $results = $this->connection->select($this->toSql(), $this->bindings, $this->fetchMode, $this->classToFetch);

    $this->processor->processSelect($this, $results);

    return $results;
  }

  public function getFirst($count, $columns = array("*")) {
    $this->take($count);

    return $this->get($columns);
  }

  /**
   * Get an array with the values of a given column.
   *
   * @param string $column Column to get
   * @param string $key    Key to use
   *
   * @return array
   */
  /*
  public function lists($column, $key = null) {
    $columns = is_null($key) ? array($column) : array($column, $key);

    // First we will just get all of the column values for the record result set
    // then we can associate those values with the column if it was specified
    // otherwise we can just give these values back without a specific key.
    $values = array_map(
      function ($row) use ($column) {
        $row = (object) $row;

        return $row->$column;

      },
      $results = $this->get($columns)
    );


    // If a key was specified and we have results, we will go ahead and combine
    // the values with the keys of all of the records so that the values can
    // be accessed by the key of the rows instead of simply being numeric.
    if ( ! is_null($key) and count($results) > 0) {
      return array_combine(
        array_map(
          function ($row) use ($key) {
            $row = (object) $row;

            return $row->$key;

          },
          $results
        ),
        $values
      );
    }

    return $values;
  }
  */
  public function lists($column, $key = null) {
    $columns = is_null($key) ? array($column) : array($column, $key);

    // First we will just get all of the column values for the record result set
    // then we can associate those values with the column if it was specified
    // otherwise we can just give these values back without a specific key.
    $results = $this->get($columns);
    $values = CMbArray::pluck($results, $column);

    // If a key was specified and we have results, we will go ahead and combine
    // the values with the keys of all of the records so that the values can
    // be accessed by the key of the rows instead of simply being numeric.
    if ( ! is_null($key) and count($results) > 0) {
      return array_combine(
        CMbArray::pluck($results, $key),
        $values
      );
    }

    return $values;
  }

  /**
   * Get a paginator for the "select" statement.
   *
   * @param  int    $perPage
   * @param  array  $columns
   * @return Illuminate\Pagination\Paginator
   */
  /*public function paginate($perPage = 15, $columns = array('*')) {
    $paginator = $this->connection->getPaginator();

    if (isset($this->groups)) {
      return $this->groupedPaginate($paginator, $perPage, $columns);
    }
    else {
      return $this->ungroupedPaginate($paginator, $perPage, $columns);
    }
  }*/

  /**
   * Create a paginator for a grouped pagination statement.
   *
   * @param  Illuminate\Pagination\Environment  $paginator
   * @param  int    $perPage
   * @param  array  $columns
   * @return Illuminate\Pagination\Paginator
   */
  /*protected function groupedPaginate($paginator, $perPage, $columns) {
    $results = $this->get($columns);

    return $this->buildRawPaginator($paginator, $results, $perPage);
  }*/

  /**
   * Build a paginator instance from a raw result array.
   *
   * @param  Illuminate\Pagination\Environment  $paginator
   * @param  array  $results
   * @param  int    $perPage
   * @return Illuminate\Pagination\Paginator
   */
  /*public function buildRawPaginator($paginator, $results, $perPage) {
    // For queries which have a group by, we will actually retrieve the entire set
    // of rows from the table and "slice" them via PHP. This is inefficient and
    // the developer must be aware of this behavior; however, it's an option.
    $start = ($paginator->getCurrentPage() - 1) * $perPage;

    $sliced = array_slice($results, $start, $perPage);

    return $paginator->make($sliced, count($results), $perPage);
  }*/

  /**
   * Create a paginator for an un-grouped pagination statement.
   *
   * @param  Illuminate\Pagination\Environment  $paginator
   * @param  int    $perPage
   * @param  array  $columns
   * @return Illuminate\Pagination\Paginator
   */
  /*protected function ungroupedPaginate($paginator, $perPage, $columns) {
    $total = $this->getPaginationCount();

    // Once we have the total number of records to be paginated, we can grab the
    // current page and the result array. Then we are ready to create a brand
    // new Paginator instances for the results which will create the links.
    $page = $paginator->getCurrentPage();

    $results = $this->forPage($page, $perPage)->get($columns);

    return $paginator->make($results, $total, $perPage);
  }*/

  /**
   * Get the count of the total records for pagination.
   *
   * @return int
   */
  /*public function getPaginationCount() {
    list($orders, $this->orders) = array($this->orders, null);

    // Because some database engines may throw errors if we leave the ordering
    // statements on the query, we will "back them up" and remove them from
    // the query. Once we have the count we will put them back onto this.
    $total = $this->count();

    $this->orders = $orders;

    return $total;
  }*/

  /**
   * Determine if any rows exist for the current query.
   *
   * @return bool
   */
  public function exists() {
    return $this->count() > 0;
  }

  /**
   * Retrieve the "count" result of the query.
   *
   * @param string $column Column to count
   *
   * @return int
   */
  public function count($column = '*') {
    return $this->aggregate(__FUNCTION__, array($column));
  }

  /**
   * Retrieve the minimum value of a given column.
   *
   * @param string $column Column to search in
   *
   * @return mixed
   */
  public function min($column) {
    return $this->aggregate(__FUNCTION__, array($column));
  }

  /**
   * Retrieve the maximum value of a given column.
   *
   * @param string $column Column to search in
   *
   * @return mixed
   */
  public function max($column) {
    return $this->aggregate(__FUNCTION__, array($column));
  }

  /**
   * Retrieve the sum of the values of a given column.
   *
   * @param string $column Column to search in
   *
   * @return mixed
   */
  public function sum($column) {
    return $this->aggregate(__FUNCTION__, array($column));
  }

  /**
   * Retrieve the average of the values of a given column.
   *
   * @param string $column Column to search in
   *
   * @return mixed
   */
  public function avg($column) {
    return $this->aggregate(__FUNCTION__, array($column));
  }

  /**
   * Execute an aggregate function on the database.
   *
   * @param string $function Aggregate function to call
   * @param array  $columns  Columns to get
   *
   * @return mixed
   */
  public function aggregate($function, $columns = array('*')) {
    $this->aggregate = compact('function', 'columns');

    $results = $this->get($columns);

    // Once we have executed the query, we will reset the aggregate property so
    // that more select queries can be executed against the database without
    // the aggregate value getting in the way when the grammar builds it.
    $this->aggregate = null;

    $result = (array) $results[0];

    return $result['aggregate'];
  }

  /**
   * Insert a new record into the database.
   *
   * @param array $values Values to insert
   *
   * @return bool
   */
  public function insert(array $values) {
    // Since every insert gets treated like a batch insert, we will make sure the
    // bindings are structured in a way that is convenient for building these
    // inserts statements by verifying the elements are actually an array.
    if ( ! is_array(reset($values))) {
      $values = array($values);
    }

    $bindings = array();

    // We'll treat every insert like a batch insert so we can easily insert each
    // of the records into the database consistently. This will make it much
    // easier on the grammars to just handle one type of record insertion.
    foreach ($values as $record) {
      $bindings = array_merge($bindings, array_values($record));
    }

    $sql = $this->grammar->compileInsert($this, $values);

    // Once we have compiled the insert statement's SQL we can execute it on the
    // connection and return a result as a boolean success indicator as that
    // is the same type of result returned by the raw connection instance.
    $bindings = $this->cleanBindings($bindings);

    return $this->connection->insert($sql, $bindings);
  }

  /**
   * Insert a new record and get the value of the primary key.
   *
   * @param array  $values   Values to insert
   * @param string $sequence Sequence
   *
   * @return int
   */
  public function insertGetId(array $values, $sequence = null) {
    $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);

    $values = $this->cleanBindings($values);

    return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
  }

  /**
   * Update a record in the database.
   *
   * @param array $values Values to update
   *
   * @return int
   */
  public function update(array $values) {
    $bindings = array_values(array_merge($values, $this->bindings));

    $sql = $this->grammar->compileUpdate($this, $values);

    return $this->connection->update($sql, $this->cleanBindings($bindings));
  }

  /**
   * Increment a column's value by a given amount.
   *
   * @param string $column Column to increment
   * @param int    $amount Amount of increments
   *
   * @return int
   */
  public function increment($column, $amount = 1) {
    $wrapped = $this->grammar->wrap($column);

    return $this->update(array($column => $this->raw("$wrapped + $amount")));
  }

  /**
   * Decrement a column's value by a given amount.
   *
   * @param string $column Column to decrement
   * @param int    $amount Amount of decrements
   *
   * @return int
   */
  public function decrement($column, $amount = 1) {
    $wrapped = $this->grammar->wrap($column);

    return $this->update(array($column => $this->raw("$wrapped - $amount")));
  }

  /**
   * Delete a record from the database.
   *
   * @param mixed $id ID to delete
   *
   * @return int
   */
  public function delete($id = null) {
    // If an ID is passed to the method, we will set the where clause to check
    // the ID to allow developers to simply and quickly remove a single row
    // from their database without manually specifying the where clauses.
    if ( ! is_null($id)) {
      $this->where($this->primaryKeyField, '=', $id);
    }

    $sql = $this->grammar->compileDelete($this);

    return $this->connection->delete($sql, $this->bindings);
  }

  /**
   * Run a truncate statement on the table.
   *
   * @return void
   */
  public function truncate() {
    foreach ($this->grammar->compileTruncate($this) as $sql => $bindings) {
      $this->connection->statement($sql, $bindings);
    }
  }

  /**
   * Get a new instance of the query CSQLQuery.
   *
   * @return CSQLQuery
   */
  public function newQuery() {
    return new CSQLQuery($this->connection, $this->from, $this->primaryKeyField, $this->classToFetch);
  }

  /**
   * Merge an array of where clauses and bindings.
   *
   * @param array $wheres   Where array to merge
   * @param array $bindings Binding params
   *
   * @return void
   */
  public function mergeWheres($wheres, $bindings) {
    $this->wheres   = array_merge($this->wheres, (array) $wheres);
    $this->bindings = array_values(array_merge($this->bindings, (array) $bindings));
  }

  /**
   * Get a copy of the where clauses and bindings in an array.
   *
   * @return array
   */
  /*
  public function getAndResetWheres() {
    $values = array($this->wheres, $this->bindings);

    list($this->wheres, $this->bindings) = array(null, array());

    return $values;
  }
  */

  /**
   * Remove all of the expressions from a list of bindings.
   *
   * @param array $bindings Binding params
   *
   * @return array
   */
  /*
  protected function cleanBindings(array $bindings) {
    return array_values(
      array_filter(
        $bindings,
        function ($binding) {
          return ! $binding instanceof CSQLExpression;
        }
      )
    );
  }
  */
  protected function cleanBindings(array $bindings) {
    return array_values(
      array_filter(
        $bindings,
        array($this, 'cleanBindingsClosure')
      )
    );
  }

  function cleanBindingsClosure($binding) {
    return ! $binding instanceof CSQLExpression;
  }

  /**
   * Create a raw database expression.
   *
   * @param mixed $value SQL string
   *
   * @return CSQLExpression
   */
  public function raw($value) {
    return $this->connection->raw($value);
  }

  /**
   * Get the current query value bindings.
   *
   * @return array
   */
  public function getBindings() {
    return $this->bindings;
  }

  /**
   * Set the bindings on the query CSQLQuery.
   *
   * @param array $bindings Binding params
   *
   * @return void
   */
  public function setBindings(array $bindings) {
    $this->bindings = $bindings;
  }

  /**
   * Merge an array of bindings into our bindings.
   *
   * @param CSQLQuery $query Query object
   *
   * @return void
   */
  public function mergeBindings(CSQLQuery $query) {
    $this->bindings = array_values(array_merge($this->bindings, $query->bindings));
  }

  /**
   * Get the database connection instance.
   *
   * @return CPDODataSource
   */
  public function getConnection() {
    return $this->connection;
  }

  /**
   * Get the database query processor instance.
   *
   * @return CSQLProcessor
   */
  public function getProcessor() {
    return $this->processor;
  }

  /**
   * Get the query grammar instance.
   *
   * @return CSQLBaseGrammar
   */
  public function getGrammar() {
    return $this->grammar;
  }

  /**
   * Get the default fetch mode for the connection.
   *
   * @return int
   */
  public function getFetchMode() {
    return $this->fetchMode;
  }

  /**
   * Set the default fetch mode for the connection.
   *
   * @param mixed $fetchMode Fetch mode
   *
   * @return void
   */
  public function setFetchMode($fetchMode) {
    $this->fetchMode = $fetchMode;

    return $this;
  }

  /**
   * Get the class to fetch in.
   *
   * @return int
   */
  public function getClassToFetch() {
    return $this->fetchMode;
  }

  /**
   * Set the class to fetch in.
   *
   * @param string $class Object class to fetch in
   *
   * @return void
   */
  public function setClassToFetch($class) {
    $this->classToFetch = $class;
  }
}