<?php 

/**
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CDailySalleMiner extends CStoredObject {
  static $mine_delay     = "+4 weeks";
  static $remine_delay   = "+0 weeks";
  static $postmine_delay = "-4 weeks";

  // Table key
  public $miner_id;

  // Plain fields
  public $salle_id;
  public $date;
  public $status;

  // Count fields
  public $_count_unmined;
  public $_count_unremined;
  public $_count_unpostmined;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "miner_id";
    $spec->loggable = false;
    $spec->uniques["salles_day"] = array("salle_id", "date");
    return $spec;
  }

  /**
   * @see parent::getSpec()
   */
  function getProps() {
    $props = parent::getProps();
    $props["salle_id"]     = "ref class|CSalle notNull";
    $props["date"]         = "date notNull";
    $props["status"]       = "enum list|mined|remined|postmined";
    return $props;
  }

  /**
   * Count all salles before a given date
   *
   * @param date $before If null, count all operations ever
   *
   * @return int
   */
  static function countSallesDaily($before = null) {
    $salle = new CSalle();
    $nb_salles = $salle->countList();

    // max de la premiere opération et du 01012001
    $op = new COperation();
    $op->loadObject(array('date' => ' IS NOT NULL'), "date ASC");
    $first_date = max($op->date, "2000-01-01");
    $day_relative = CMbDT::daysRelative($first_date, CMbDT::date($before));
    return $nb_salles*$day_relative;
  }

  /**
   * Make all salles counts
   *
   * @return int[] Keys being: overall, tobemined, toberemined
   */
  static function makeSalleDailyCounts() {
    return array(
      "overall"       => self::countSallesDaily(),
      "tobemined"     => self::countSallesDaily(CMbDT::date(self::$mine_delay)),
      "toberemined"   => self::countSallesDaily(CMbDT::date(self::$remine_delay)),
      "tobepostmined" => self::countSallesDaily(CMbDT::date(self::$postmine_delay)),
    );
  }

  /**
   * Count operations that have not been mined yet
   *
   * @return int
   */
  function countUnmined() {
    return $this->_count_unmined = self::countSallesDaily(CMbDT::date(self::$mine_delay)) - $this->countList();
  }

  /**
   * Count mining that have not been remined yet
   *
   * @return int
   */
  function countUnremined() {
    $date             = CMbDT::date(self::$remine_delay);
    $where["date"]    = "< '$date'";
    $where["status"] = "= 'mined'";
    return $this->_count_unremined = $this->countList($where);
  }

  /**
   * Count mining that have not been remined yet
   *
   * @return int
   */
  function countUnpostmined() {
    $date               = CMbDT::date(self::$postmine_delay);
    $where["date"]      = "< '$date'";
    $where["status"] = " IN ('remined', 'postmined')";
    return $this->_count_unpostmined = $this->countList($where);
  }

  /**
   * Mine or remine the first availables salles
   *
   * @param int    $limit
   * @param string $phase
   *
   * @return array Success/failure counts report
   */
  function mineSome($limit = 100, $phase = "mine", $date = null) {
    $date = CMbDT::date($date);
    $report = array(
      "success" => 0,
      "failure" => 0,
    );

    if (!$limit) {
      return $report;
    }

    $salle = new CSalle();
    $ds = $salle->getDS();
    $salle_ids = $salle->loadIds();

    $phases = array(
      "mine"      => array("mined", "remined", "postmined"),
      "remine"    => array("remined", "postmined"),
      "postmine"  => array("postmined"),
    );

    $ref_dates = array(
      "mine"      => CMbDT::date(self::$mine_delay, $date),
      "remine"    => CMbDT::date(self::$remine_delay, $date),
      "postmine"  => CMbDT::date(self::$postmine_delay, $date),
    );

    $sql = "SELECT sallesbloc.salle_id, date, miner_id as occupation
      FROM sallesbloc
      LEFT JOIN salle_daily_occupation ON salle_daily_occupation.salle_id = sallesbloc.salle_id
      WHERE salle_daily_occupation.date = '".$ref_dates[$phase]."'";
    $result = $ds->loadList($sql);
    $result_by_salle = CMbArray::pluck($result, "salle_id");
    $to_do = array_diff($salle_ids, $result_by_salle);
    $nb = 0;
    foreach($to_do as $_to_do) {
      if ($nb > $limit) {
        continue;
      }
      $this->mine($_to_do, $ref_dates[$phase]);
      if ($msg = $this->store()) {
        $report["failure"]++;
      }
      else {
        $report["success"]++;
      }
      $nb++;
    }

    return $report;
  }

  /**
   * Operation sur les ranges
   *
   * @param int  $salle_id Salle id
   * @param date $date     date to mine
   *
   * @return null
   */
  function mine($salle_id, $date) {
    $this->nullifyProperties();
    $this->salle_id = $salle_id;
    $this->date = $date;
    $this->loadMatchingObject();

    if ($this->date <= CMbDT::date(self::$postmine_delay)) {
      $this->status = "postmined";
    }
    elseif ($this->date <= CMbDT::date(self::$remine_delay)) {
      $this->status = "remined";
    }
    else {
      $this->status = "mined";
    }
  }
}
