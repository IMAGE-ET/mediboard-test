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
 * Class CSQLJoinClause
 */
class CSQLJoinClause {

  /**
   * The type of join being performed.
   *
   * @var string
   */
  public $type;

  /**
   * The table the join clause is joining to.
   *
   * @var string
   */
  public $table;

  /**
   * The "on" clauses for the join.
   *
   * @var array
   */
  public $clauses = array();

  /**
   * Create a new join clause instance.
   *
   * @param string $type
   * @param string $table
   */
  public function __construct($type, $table)
  {
    $this->type = $type;
    $this->table = $table;
  }

  /**
   * Add an "on" clause to the join.
   *
   * @param string $first
   * @param string $operator
   * @param string $second
   * @param string $boolean
   *
   * @return CSQLJoinClause
   */
  public function on($first, $operator, $second, $boolean = 'and')
  {
    $this->clauses[] = compact('first', 'operator', 'second', 'boolean');

    return $this;
  }

  /**
   * Add an "or on" clause to the join.
   *
   * @param string $first
   * @param string $operator
   * @param string $second
   *
   * @return CSQLJoinClause
   */
  public function orOn($first, $operator, $second)
  {
    return $this->on($first, $operator, $second, 'or');
  }

}