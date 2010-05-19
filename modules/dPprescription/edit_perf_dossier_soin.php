<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$prescription_line_mix_id = CValue::get("prescription_line_mix_id");
$date         = CValue::get("date");
$mode_dossier = CValue::get("mode_dossier");
$sejour_id    = CValue::getOrSession("sejour_id");
$mode_refresh = CValue::get("mode_refresh");

// Chargement de la prescription_line_mix
$prescription_line_mix = new CPrescriptionLineMix();
$prescription_line_mix->load($prescription_line_mix_id);
$prescription_line_mix->loadRefsLines();
$prescription_line_mix->loadVoies();
$prescription_line_mix->loadRefsVariations();

// Refresh des transmissions
if($mode_refresh == "trans"){
  $transmissions = array();
  // Chargement des transmissions
  $transmission = new CTransmissionMedicale();
  $transmission->object_id = $prescription_line_mix_id;
  $transmission->object_class = "CPrescriptionLineMix";
  $transmissions = $transmission->loadMatchingList();
  foreach($transmissions as $_transmission){
    $_transmission->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription_line_mix", $prescription_line_mix);
$smarty->assign("date", $date);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("sejour_id", $sejour_id);

if($mode_refresh == "timing"){
  $smarty->display("inc_perf_timing.tpl");
} 
if($mode_refresh == "trans"){
  $smarty->assign("transmission", $transmission);
  $smarty->assign("transmissions", $transmissions);
  $smarty->display("inc_perf_transmissions.tpl");
}

if(!$mode_refresh){
  $smarty->display("edit_perf_dossier_soin.tpl");
}

?>