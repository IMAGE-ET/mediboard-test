<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6300 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$perfusion_id = CValue::get("perfusion_id");
$date = CValue::get("date");
$hour = CValue::get("hour");
$time_prevue = CValue::get("time_prevue");
$mode_dossier = CValue::get("mode_dossier");
$sejour_id = CValue::get("sejour_id");
$date_sel  = CValue::get("date_sel");

$perfusion = new CPerfusion();
$perfusion->load($perfusion_id);
$perfusion->loadRefsLines();
$perfusion->loadVoies();
$perfusion->calculQuantiteTotal();

if($time_prevue){
  $_hour = mbTransformTime(null, $time_prevue, '%H');
  $dateTime = "$date $time_prevue";
} else {
  $_hour = $hour;
  $dateTime = "$date $hour:00:00";
}

// Chargement des administrations deja effectuée pour l'heure donnée
$administrations = array();
foreach($perfusion->_ref_lines as $_perf_line){
  $administration = new CAdministration();
  $where = array();
  $where["object_id"] = " = '$_perf_line->_id'";
  $where["object_class"] = " = '$_perf_line->_class_name'";
  $where["dateTime"] = " LIKE '$date $_hour%'";
  $administrations[$_perf_line->_id] = $administration->loadList($where);
}

foreach($administrations as $_administrations){
  foreach($_administrations as $_admin){
    $_admin->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("perfusion", $perfusion);
$smarty->assign("dateTime", $dateTime);
$smarty->assign("administration", new CAdministration());
$smarty->assign("administrations", $administrations);
$smarty->assign("date", $date);
$smarty->assign("hour", $hour);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("date_sel", $date_sel);
$smarty->display("inc_vw_add_administration_perf.tpl");

?>