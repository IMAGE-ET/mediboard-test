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
 * Class CSQLGrammarSQLite
 */
class CSQLGrammarSQLite extends CSQLGrammar {

  /**
   * Compile the "order by" portions of the query.
   *
   * @param  Illuminate\Database\Query\Builder  $query
   * @param  array  $orders
   *
   * @return string
   */
  /*
  protected function compileOrders(CSQLQuery $query, $orders) {
    $me = $this;

    return 'order by '.implode(
      ', ',
      array_map(
        function ($order) use ($me) {
          return $me->wrap($order['column']).' collate nocase '.$order['direction'];
        }
        , $orders
      )
    );
  }
  */

  protected function compileOrders(CSQLQuery $query, $orders) {
    $me = $this;

    return 'order by '.implode(
      ', ',
      call_user_func(array($this, 'compileOrdersClosure'), $orders)
    );
  }

  public function compileOrdersClosure($orders) {
    foreach ($orders as $k => $_order) {
      $orders[$k] = $this->wrap($_order['column']).' collate nocase '.$_order['direction'];
    }

    return $orders;
  }

  /**
   * Compile an insert statement into SQL.
   *
   * @param  Illuminate\Database\Query\Builder  $query
   * @param  array  $values
   *
   * @return string
   */
  public function compileInsert(CSQLQuery $query, array $values) {
    // Essentially we will force every insert to be treated as a batch insert which
    // simply makes creating the SQL easier for us since we can utilize the same
    // basic routine regardless of an amount of records given to us to insert.
    $table = $this->wrapTable($query->from);

    if ( ! is_array(reset($values))) {
      $values = array($values);
    }

    // If there is only one record being inserted, we will just use the usual query
    // grammar insert builder because no special syntax is needed for the single
    // row inserts in SQLite. However, if there are multiples, we'll continue.
    if (count($values) == 1) {
      return parent::compileInsert($query, $values[0]);
    }

    $names = $this->columnize(array_keys($values[0]));

    $columns = array();

    // SQLite requires us to build the multi-row insert as a listing of select with
    // unions joining them together. So we'll build out this list of columns and
    // then join them all together with select unions to complete the queries.
    foreach (array_keys($values[0]) as $column) {
      $columns[] = '? as '.$this->wrap($column);
    }

    $columns = array_fill(0, count($values), implode(', ', $columns));

    return "insert into $table ($names) select ".implode(' union select ', $columns);
  }

  /**
   * Compile a truncate table statement into SQL.
   *
   * @param  Illuminate\Database\Query\Builder  $query
   *
   * @return array
   */
  public function compileTruncate(CSQLQuery $query) {
    $sql = array('delete from sqlite_sequence where name = ?' => array($query->from));

    $sql['delete from '.$this->wrapTable($query->from)] = array();

    return $sql;
  }

}