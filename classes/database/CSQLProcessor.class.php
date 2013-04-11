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
 * Class CSQLProcessor
 */
class CSQLProcessor {

  /**
	 * Process the results of a "select" query.
	 *
	 * @param CSQLQuery $query   Query
	 * @param array     $results Results
   *
	 * @return array
	 */
  public function processSelect(CSQLQuery $query, $results) {
    switch ($query->fetchMode) {
      case PDO::FETCH_CLASS:
        $list = array();

        /** @var CStoredObject[] $results */
        foreach ($results as $_object) {
          $_object->checkConfidential();
          $_object->updateFormFields();
          $_object->registerCache();

          if ($_object->_id) {
            $list[$_object->_id] = $_object;
          }
          else {
            $list[] = $_object;
          }
        }

        return $list;
    }

    return $results;
  }

  /**
	 * Process an  "insert get ID" query.
	 *
	 * @param CSQLQuery $query    Query
	 * @param string    $sql      SQL
	 * @param array     $values   Values
	 * @param string    $sequence Sequence
   *
	 * @return int
	 */
  public function processInsertGetId(CSQLQuery $query, $sql, $values, $sequence = null) {
    $query->getConnection()->insert($sql, $values);

    $id = $query->getConnection()->getPdo()->lastInsertId($sequence);

    return is_numeric($id) ? (int) $id : $id;
  }
}