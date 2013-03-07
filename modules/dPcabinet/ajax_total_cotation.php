<?php 

/**
 * Totaux de la cotation par praticien
 *  
 * @category dPcabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$debut   = CValue::getOrSession("date_min", CMbDT::date());
$fin     = CValue::getOrSession("date_max", CMbDT::date());
$chir_id = CValue::getOrSession("chir_id");

$prats = array();

if (!$chir_id) {
  $user = CMediusers::get();
  $prats = $user->loadPraticiensCompta();
}
else {
  $chir = CMediusers::get($chir_id);
  $prats[$chir_id] = $chir;
}

CMbObject::massLoadFwdRef($prats, "function_id");

$ds = CSQLDataSource::get("std");
$object_classes = array("CConsultation", "CSejour", "COperation");

$cotation = array();
$total_by_prat = array();
$total_by_class = array();
$total = 0;

foreach ($prats as $_chir_id => $_prat) {
  $_prat->loadRefFunction();
  $cotation[$_chir_id] = array();
  @$total_by_prat[$_chir_id] = 0;

  foreach ($object_classes as $_object_class) {
    $cotation[$_chir_id][$_object_class] =
      array(
        "ccam" =>
          array(
            "sect1" => 0,
            "sect2" => 0
          ),
          "ngap" =>
          array(
            "sect1" => 0,
            "sect2" => 0
        )
    );

    // CCAM
    $request = "SELECT SUM(montant_base) AS sect1, SUM(montant_depassement) AS sect2
      FROM acte_ccam
      WHERE object_class = '$_object_class'
      AND executant_id = '$_chir_id'
      AND DATE(execution) BETWEEN '$debut' AND '$fin';";

    $result = $ds->loadHash($request);
    $cotation[$_chir_id][$_object_class]["ccam"]["sect1"] = round($result["sect1"], 2);
    $cotation[$_chir_id][$_object_class]["ccam"]["sect2"] = round($result["sect2"], 2);

    // NGAP
    $request = "SELECT SUM(montant_base) AS sect1, SUM(montant_depassement) AS sect2
      FROM acte_ngap ";

    switch ($_object_class) {
      case "CConsultation":
        $request .= "LEFT JOIN consultation ON consultation.consultation_id = acte_ngap.object_id
          LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
          WHERE plageconsult.date BETWEEN '$debut' AND '$fin' ";
        break;
      case "CSejour":
        $request .= "LEFT JOIN sejour ON sejour.sejour_id = acte_ngap.object_id
          WHERE DATE(sejour.entree) < '$fin' AND DATE(sejour.sortie) > '$debut' ";
        break;
      case "COperation":
        $request .= "LEFT JOIN operations ON operations.operation_id = acte_ngap.object_id
          LEFT JOIN plagesop ON plagesop.plageop_id = operations.plageop_id
          WHERE DATE(plagesop.date) BETWEEN '$debut' AND '$fin' ";
    }

    $request .= "AND object_class = '$_object_class'
      AND executant_id = '$_chir_id'";

    $result = $ds->loadHash($request);

    $cotation[$_chir_id][$_object_class]["ngap"]["sect1"] = round($result["sect1"], 2);
    $cotation[$_chir_id][$_object_class]["ngap"]["sect2"] = round($result["sect2"], 2);

    $total_by_prat[$_chir_id] += $cotation[$_chir_id][$_object_class]["ccam"]["sect1"];
    $total_by_prat[$_chir_id] += $cotation[$_chir_id][$_object_class]["ccam"]["sect2"];
    $total_by_prat[$_chir_id] += $cotation[$_chir_id][$_object_class]["ngap"]["sect1"];
    $total_by_prat[$_chir_id] += $cotation[$_chir_id][$_object_class]["ngap"]["sect2"];

    @$total_by_class[$_object_class]["ccam"] += $cotation[$_chir_id][$_object_class]["ccam"]["sect1"];
    @$total_by_class[$_object_class]["ccam"] += $cotation[$_chir_id][$_object_class]["ccam"]["sect2"];
    @$total_by_class[$_object_class]["ngap"] += $cotation[$_chir_id][$_object_class]["ngap"]["sect1"];
    @$total_by_class[$_object_class]["ngap"] += $cotation[$_chir_id][$_object_class]["ngap"]["sect2"];

    @$total += $cotation[$_chir_id][$_object_class]["ccam"]["sect1"];
    @$total += $cotation[$_chir_id][$_object_class]["ccam"]["sect2"];
    @$total += $cotation[$_chir_id][$_object_class]["ngap"]["sect1"];
    @$total += $cotation[$_chir_id][$_object_class]["ngap"]["sect2"];
  }
}

foreach ($total_by_prat as $_chir_id => $_total) {
  if ($_total == 0) {
    unset($total_by_prat[$_chir_id]);
    unset($cotation[$_chir_id]);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("debut"   , $debut);
$smarty->assign("fin"     , $fin);
$smarty->assign("cotation", $cotation);
$smarty->assign("total"   , $total);
$smarty->assign("prats"   , $prats);
$smarty->assign("total_by_prat" , $total_by_prat);
$smarty->assign("total_by_class", $total_by_class);

$smarty->display("inc_total_cotation.tpl");