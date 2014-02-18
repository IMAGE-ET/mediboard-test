<?php
/**
 * $Id: CProtocole.class.php 21266 2013-12-04 14:41:23Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21266 $
 */

/**
 * Operation daily miner
 */
class COperationMiner extends CStoredObject {
  static $remine_delay = "-4 weeks";

  // Table key
  public $miner_id;

  // Plain fields
  public $operation_id;
  public $date;
  public $remined;

  // Count fields
  public $_count_unmined;
  public $_count_unremined;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "miner_id";
    $spec->loggable = false;
    $spec->uniques["operations"] = array("operation_id");
    return $spec;
  }

  /**
   * @see parent::getSpec()
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"] = "ref class|COperation notNull";
    $props["date"]         = "date notNull";
    $props["remined"]      = "bool notNull default|0";
    return $props;
  }

  /**
   * Count all operations before a given date
   *
   * @param date $before If null, count all operations ever
   *
   * @return int
   */
  static function countOperations($before = null) {
    $operation = new COperation;
    $where = null;
    $ljoin = null;
    if ($before) {
      $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
      $where[] = "operations.date < '$before' OR plagesop.date < '$before'";
    }

    return $operation->countList($where, null, $ljoin);
  }

  /**
   * Make all operation counts
   *
   * @return int[] Keys being: overall, tobemined, toberemined
   */
  static function makeOperationCounts() {
    return array(
      "overall"     => self::countOperations(),
      "tobemined"   => self::countOperations(CMbDT::date()),
      "toberemined" => self::countOperations(CMbDT::date(self::$remine_delay)),
    );
  }

  function makeMineCounts() {
    return array(
      "unmined"     => $this->countUnmined(),
      "unremined"   => $this->countUnremined(),
    );
  }

  /**
   * Count operations that have not been mined yet
   *
   * @return int
   */
  function countUnmined() {
    $today = CMbDT::date();
    $operation = new COperation;
    $table = $this->_spec->table;
    $ljoin[$table] = "$table.operation_id = operations.operation_id";
    $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
    $where["$table.operation_id"] = "IS NULL";
    $where[] = "operations.date < '$today' OR plagesop.date < '$today'";
    return $this->_count_unmined = $operation->countList($where, null, $ljoin);
  }

  /**
   * Count mining that have not been remined yet
   *
   * @return int
   */
  function countUnremined() {
    $date = CMbDT::date(self::$remine_delay);
    $where["date"] = "< '$date'";
    $where["remined"] = "= '0'";
    return $this->_count_unremined = $this->countList($where);
  }

  /**
   * Mine or remine the first available operations
   *
   * @param int  $limit
   * @param bool $remine
   *
   * @return array Success/failure counts report
   */
  function mineSome($limit = 100, $remine = false) {
    $report = array(
      "success" => 0,
      "failure" => 0,
    );

    if (!$limit) {
      return $report;
    }

    $operation = new COperation;
    /** @var COperation[] $operations */
    $operations = array();
    if ($remine) {
      $date = CMbDT::date(self::$remine_delay);
      $where["date"] = "< '$date'";
      $where["remined"] = "= '0'";
      $mined = $this->loadList($where, null, $limit);
      $operation_ids = CMbArray::pluck($mined, "operation_id");
      $operations = $operation->loadAll($operation_ids);
    }
    else {
      $today = CMbDT::date();
      $table = $this->_spec->table;
      $ljoin[$table]     = "$table.operation_id = operations.operation_id";
      $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
      $where[] = "operations.date < '$today' OR plagesop.date < '$today'";
      $where["$table.operation_id"] = "IS NULL";
      $operations = $operation->loadList($where, null, $limit, null, $ljoin);
    }

    $plages = CStoredObject::massLoadFwdRef($operations, "plageop_id");
    $salles = CStoredObject::massLoadFwdRef($plages, "salle_id");
    CStoredObject::massLoadFwdRef($salles, "bloc_id");

    foreach ($operations as $_operation) {
      $_operation->loadRefPlageOp();
      $this->mine($_operation);
      if ($msg = $this->store()) {
        trigger_error($msg, UI_MSG_ERROR);
        $report["failure"]++;
        continue;
      }

      $report["success"]++;
    }

    return $report;
  }

  /**
   * Mine the operation
   *
   * @param COperation $operation
   */
  function mine(COperation $operation) {
    $this->nullifyProperties();
    $this->operation_id = $operation->_id;
    $this->loadMatchingObject();
    $this->date = CMbDT::date($operation->_datetime);
    if ($this->date < CMbDT::date(self::$remine_delay)) {
     $this->remined = 1;
    };
  }
}
