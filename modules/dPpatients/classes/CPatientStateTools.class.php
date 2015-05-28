<?php

/**
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Tools for state patient
 */
class CPatientStateTools {
  static $color = array(
    "PROV"   => "#33B1FF",
    "VALI"   => "#CC9900",
    "DPOT"   => "#9999CC",
    "ANOM"   => "#FF66FF",
    "CACH"   => "#B2B2B3",
    "merged" => "#EEA072"
  );

  /**
   * Set the PROV status for the patient stateless
   *
   * @param String $state patient state
   *
   * @return int
   */
  static function createStatus($state = "PROV") {
    $ds = CSQLDataSource::get("std");

    $ds->exec("UPDATE `patients` SET `status`='$state' WHERE `status` IS NULL;");

    return $ds->affectedRows();
  }

  /**
   * Get the number patient stateless
   *
   * @return int
   */
  static function verifyStatus() {
    $patient = new CPatient();
    $where   = array(
      "status" => "IS NULL"
    );

    return $patient->countList($where);
  }

  /**
   * get the patient by date
   *
   * @param Date $before before date
   * @param Date $now    now date
   *
   * @return array
   */
  static function getPatientStateByDate($before, $now) {
    $ds      = CSQLDataSource::get("std");
    $request = new CRequest();
    $request->addSelect("DATE(datetime) AS 'date', state, count(*) as 'total'");
    $request->addTable("patient_state");
    $request->addWhere("DATE(datetime) BETWEEN '$before' AND '$now'");
    $request->addGroup("DAY(datetime), state");

    return $ds->loadList($request->makeSelect());
  }

  /**
   * Get the patient merge by date
   *
   * @param Date $before before date
   * @param Date $now    now date
   *
   * @return array
   */
  static function getPatientMergeByDate($before, $now) {
    $where = array(
      "date >= '$before 00:00:00'",
      "date <= '$now 23:59:59'",
      "type = 'merge'",
      "object_class = 'CPatient'"
    );

    $ds = CSQLDataSource::get("std");
    $ds->exec("SET SESSION group_concat_max_len = 100000;");

    $request = new CRequest();
    $request->addSelect("DATE(date) AS 'date', COUNT(*) AS 'total', GROUP_CONCAT( object_id  SEPARATOR '-') as ids");
    $request->addTable("user_log");
    $request->addWhere($where);
    $request->addGroup("DATE(date)");

    return $ds->loadList($request->makeSelect());
  }

  /**
   * Create the pie graph
   *
   * @param String[] $count_status number patient by status
   *
   * @return array
   */
  static function createGraphPie($count_status) {
    $series = array(
      "title"   => "CPatientState.proportion",
      "count"   => null,
      "unit"    => lcfirst(CAppUI::tr("CPatient|pl")),
      "datum"   => array(),
      "options" => null
    );

    $total = 0;
    foreach ($count_status as $_count) {
      $count  = $_count["total"];
      $status = $_count["status"];
      $total += $count;
      $series["datum"][] = array(
        "label" => utf8_encode(CAppUI::tr("CPatient.status.$status")),
        "data"  => $count,
        "color" => self::$color[$status]
      );
    }

    $series["count"]   = $total;
    $series["options"] = array(
      "series" => array(
        "unit" => lcfirst(CAppUI::tr("CPatient|pl")),
        "pie"  => array(
          "innerRadius" => 0.5,
          "show"        => true,
          "label"       => array(
            "show"      => true,
            "threshold" => 0.02
          )
        )
      ),
      "legend" => array(
        "show" => false
      ),
      "grid"   => array(
        "hoverable" => true
      )
    );

    return $series;
  }

  /**
   * Create the bar graph
   *
   * @param array   $values   number patient status by date
   * @param Integer $interval interval between two date
   *
   * @return array
   */
  static function createGraphBar($values, $interval) {
    $series2 = array(
      "title"   => "CPatientState.dayproportion",
      "unit"    => lcfirst(CAppUI::tr("CPatient|pl")),
      "count"   => 0,
      "datum"   => null,
      "options" => array(
        "xaxis"  => array(
          "position" => "bottom",
          "min"      => 0,
          "max"      => $interval + 1,
          "ticks"    => array(),
        ),
        "yaxes"  => array(
          "0" => array(
            "position"     => "left",
            "tickDecimals" => false
          ),
          "1" => array(
            "position" => "right",
          )
        ),
        "legend" => array(
          "show" => true
        ),
        "series" => array(
          "stack" => true
        ),
        "grid"   => array(
          "hoverable" => true
        )
      )
    );

    if (array_key_exists('merged', $values)) {
      $series2['options']['grid']['clickable'] = true;
    }

    $total = 0;
    $datum = array();
    foreach ($values as $_status => $_result) {
      $abscisse = -1;
      $data     = array();

      foreach ($_result as $_day => $_count) {
        // When merged patients searched, value if count + patient IDs
        if (is_array($_count) && $_status == 'merged') {
          $_ids   = $_count['ids'];
          $_count = $_count['count'];
        }
        else {
          $_ids = null;
        }

        $abscisse += 1;
        $series2["options"]["xaxis"]["ticks"][] = array($abscisse + 0.5, utf8_encode(CMbDT::transform(null, $_day, "%d/%m")));

        $data[] = array(
          $abscisse,
          $_count,
          'day' => utf8_encode(CMbDT::transform(null, $_day, "%d/%m/%Y")),
          'ids' => $_ids
        );

        $total += $_count;
      }

      $datum[] = array(
        "data"  => $data,
        "yaxis" => 1,
        "label" => utf8_encode(CAppUI::tr("CPatient.status." . $_status)),
        "color" => self::$color[$_status],
        "unit"  => lcfirst(CAppUI::tr("CPatient|pl")),
        "bars"  => array(
          "show" => true
        )
      );
    }

    $series2["datum"] = $datum;
    $series2['count'] = $total;

    return $series2;
  }
}
