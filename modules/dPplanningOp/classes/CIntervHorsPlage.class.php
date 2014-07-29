<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Almost empty class for permissions issues
 */
class CIntervHorsPlage extends CMbObject {
  // DB Table key
  public $interv_hors_plage_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'interv_hors_plages';
    $spec->key   = 'interv_hors_plage_id';
    return $spec;
  }

  /**
   * liste les interventions hors plage entre 2 date données
   *
   * @param date      $start     date de début
   * @param date|null $end       date de fin (si null = date de début)
   * @param array     $chir_ids  chirs targeted
   * @param array     $salle_ids salles to check
   *
   * @return COperation[]
   */
  static function getForDates($start, $end = null, $chir_ids = array(), $salle_ids = array()) {
    $d_start = $start;
    $d_end = $end ? $end : $start;
    $ljoin = array();
    $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    $where = array();
    if (count($chir_ids)) {
      $where["chir_id"] = CSQLDataSource::prepareIn($chir_ids);
    }
    if (count($salle_ids)) {
      $where[] = "operations.salle_id IS NULL OR operations.salle_id ".
        CSQLDataSource::prepareIn($salle_ids);
    }
    $where["operations.plageop_id"] = "IS NULL";
    $where["operations.date"]       = "BETWEEN '$d_start' AND '$d_end'";
    $where["operations.annulee"]    = "= '0'";
    $where["sejour.group_id"]    = "= '".CGroups::loadCurrent()->_id."'";
    $order = "operations.date, operations.chir_id";
    $op = new COperation();
    /** @var COperation[] $listHorsPlage */
    $listHorsPlage = $op->loadList($where, $order, null, null, $ljoin);
    return $listHorsPlage;
  }

  /**
   * count list of Op not linked to a plage
   *
   * @param date      $start    date de début
   * @param date|null $end      date de fin
   * @param array     $chir_ids chir targeted
   *
   * @return int number of HP found
   */
  static function countForDates($start, $end= null, $chir_ids = array()) {
    $d_start = $start;
    $d_end = $end ? $end : $start;
    $op = new COperation();
    $ljoin = array();
    $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    $where = array();
    if (count($chir_ids)) {
      $where["chir_id"] = CSQLDataSource::prepareIn($chir_ids);
    }
    $where["operations.plageop_id"] = "IS NULL";
    $where["operations.date"]       = "BETWEEN '$d_start' AND '$d_end'";
    $where["operations.annulee"]    = "= '0'";
    $where["sejour.group_id"]    = "= '".CGroups::loadCurrent()->_id."'";

    /** @var COperation[] $listHorsPlage */
    return $op->countList($where, null, $ljoin);
  }
}
