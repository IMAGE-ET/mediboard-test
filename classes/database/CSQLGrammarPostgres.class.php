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
 * Class CSQLGrammarPostgres
 */
class CSQLGrammarPostgres extends CSQLGrammar {

  /**
   * Compile an insert and get ID statement into SQL.
   *
   * @param  Illuminate\Database\Query\Builder  $query
   * @param  array   $values
   * @param  string  $sequence
   *
   * @return string
   */
  public function compileInsertGetId(CSQLQuery $query, $values, $sequence) {
    if (is_null($sequence)) {
      $sequence = 'id';
    }

    return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence);
  }

  /**
   * Compile a truncate table statement into SQL.
   *
   * @param  Illuminate\Database\Query\Builder  $query
   *
   * @return array
   */
  public function compileTruncate(CSQLQuery $query) {
    return array('truncate '.$this->wrapTable($query->from).' restart identity' => array());
  }

}