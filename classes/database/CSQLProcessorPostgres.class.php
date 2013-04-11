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
 * Class CSQLProcessorPostgres
 */
class CSQLProcessorPostgres extends CSQLProcessor {

  /**
   * Process an "insert get ID" query.
   *
   * @param CSQLQuery $query
   * @param string    $sql
   * @param array     $values
   * @param string    $sequence
   *
   * @return int
   */
  public function processInsertGetId(CSQLQuery $query, $sql, $values, $sequence = null) {
    $results = $query->getConnection()->select($sql, $values);

    //$sequence = $sequence ?: 'id';
    if (!$sequence) {
      $sequence = 'id';
    }

    return $results[0]->$sequence;
  }

}