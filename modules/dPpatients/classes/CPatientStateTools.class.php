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
    "PROV" => "#33B1FF",
    "VALI" => "#CC9900",
    "DPOT" => "#9999CC",
    "ANOM" => "#FF66FF",
    "CACH" => "#b2b2b3",
  );

  /**
   * Set the PROV status for the patient stateless
   *
   * @param String $state patient state
   *
   * @return int
   */
  static function createStatus($state="PROV") {
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
    $where = array(
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
    $ds = CSQLDataSource::get("std");
    $request = new CRequest();
    $request->addSelect("DATE(datetime) AS 'date', state, count(*) as 'total'");
    $request->addTable("patient_state");
    $request->addWhere("DATE(datetime) BETWEEN '$before' AND '$now'");
    $request->addGroup("DAY(datetime), state");

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
      "unit"    => CAppUI::tr("CPatient"),
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
        "color" => self::$color[$status],
      );
    }

    $series["count"] = $total;
    $series["options"] = array(
      "series" => array(
        "unit"    => CAppUI::tr("CPatient"),
        "pie" => array(
          "innerRadius" => 0.5,
          "show" => true,
          "label" => array(
            "show" => true,
            "threshold" => 0.02
          )
        )
      ),
      "legend" => array(
        "show" => false
      ),
      "grid" => array(
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
      "unit"    => CAppUI::tr("CPatient"),
      "count"   => null,
      "datum"   => null,
      "options" => array(
        "xaxis"  => array(
          "position" => "bottom",
          "min"      => 0,
          "max"      => $interval+1,
          "ticks"    => array()
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

    $datum = array();
    foreach ($values as $_status => $_result) {
      $abscisse = -1;
      $data = array();
      foreach ($_result as $_day => $_count) {
        $abscisse += 1;
        $series2["options"]["xaxis"]["ticks"][] = array($abscisse+0.5, utf8_encode(CMbDT::transform(null, $_day, "%d")));

        $data[] = array($abscisse, $_count);
      }

      $datum[] = array(
        "data" => $data,
        "yaxis" => 1,
        "label" => utf8_encode(CAppUI::tr("CPatient.status.".$_status)),
        "color" => self::$color[$_status],
        "unit"  => CAppUI::tr("CPatient"),
        "bars" => array(
          "show" => true
        ),
        "name" => $_day
      );
    }
    $series2["datum"] = $datum;

    return $series2;
  }
}
