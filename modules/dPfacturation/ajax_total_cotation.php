<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
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
$total_by_class = array(
  "CConsultation" => array(),
  "CSejour"       => array(),
  "COperation"    => array()
);
$total = 0;
$tab =  array( "sect1" => 0, "sect2" => 0);
$tab_actes = array();

if (CAppUI::conf("ref_pays") == 1) {
  $tab_actes["ccam"] = $tab;
  $tab_actes["ngap"] = $tab;
}
else {
  $tab_actes["tarmed"] = $tab;
  $tab_actes["caisse"] = $tab;
}
foreach ($total_by_class as $classe => $value) {
  foreach ($tab_actes as $nom => $other) {
    $total_by_class[$classe][$nom] = 0;
  }
}
foreach ($prats as $_chir_id => $_prat) {
  $_prat->loadRefFunction();
  $cotation[$_chir_id] = array();
  $total_by_prat[$_chir_id] = 0;

  foreach ($object_classes as $_object_class) {
    $cotation[$_chir_id][$_object_class] = $tab_actes;
    foreach ($cotation[$_chir_id][$_object_class] as $key => $type) {
      $result = null;
      if ($key == "ccam") {
        $request = "SELECT SUM(montant_base) AS sect1, SUM(montant_depassement) AS sect2
          FROM acte_ccam
          WHERE object_class = '$_object_class'
          AND executant_id = '$_chir_id'
          AND DATE(execution) BETWEEN '$debut' AND '$fin';";
    
        $result = $ds->loadHash($request);
      }
      else {
        $request = "SELECT SUM(montant_base) AS sect1, SUM(montant_depassement) AS sect2
          FROM acte_$key ";
    
        switch ($_object_class) {
          case "CConsultation":
            $request .= "LEFT JOIN consultation ON consultation.consultation_id = acte_$key.object_id
              LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
              WHERE plageconsult.date BETWEEN '$debut' AND '$fin' ";
            break;
          case "CSejour":
            $request .= "LEFT JOIN sejour ON sejour.sejour_id = acte_$key.object_id
              WHERE DATE(sejour.entree) <= '$fin' AND DATE(sejour.sortie) >= '$debut' ";
            break;
          case "COperation":
            $request .= "LEFT JOIN operations ON operations.operation_id = acte_$key.object_id
              LEFT JOIN plagesop ON plagesop.plageop_id = operations.plageop_id
              WHERE ((DATE(plagesop.date) BETWEEN '$debut' AND '$fin')
                     OR (plagesop.plageop_id IS NULL AND operations.date BETWEEN '$debut' AND '$fin'))";
        }
    
        $request .= "AND object_class = '$_object_class'
          AND executant_id = '$_chir_id'";
    
        $result = $ds->loadHash($request);
      }
      $sect1 = round($result["sect1"], 2);
      $sect2 = round($result["sect2"], 2);
      $total_sect = $sect1 + $sect2;
      $cotation[$_chir_id][$_object_class][$key]["sect1"] = $sect1;
      $cotation[$_chir_id][$_object_class][$key]["sect2"] = $sect2;
      
      $total_by_prat[$_chir_id] += $total_sect;
      
      $total_by_class[$_object_class][$key] += $total_sect;
      $total += $total_sect;
    }
  }
}

foreach ($total_by_prat as $_chir_id => $_total) {
  if ($_total == 0) {
    unset($total_by_prat[$_chir_id]);
    unset($cotation[$_chir_id]);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("tab_actes" , $tab_actes);
$smarty->assign("debut"     , $debut);
$smarty->assign("fin"       , $fin);
$smarty->assign("cotation"  , $cotation);
$smarty->assign("total"     , $total);
$smarty->assign("prats"     , $prats);
$smarty->assign("total_by_prat" , $total_by_prat);
$smarty->assign("total_by_class", $total_by_class);
$smarty->assign("object_classes", $object_classes);

$smarty->display("inc_total_cotation.tpl");