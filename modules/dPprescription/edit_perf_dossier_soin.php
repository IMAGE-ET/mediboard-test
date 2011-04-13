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

$transmission = new CTransmissionMedicale();
$transmission->sejour_id = $sejour_id;
$transmission->user_id   = CAppUI::$user->_id;
$transmission->object_id = $prescription_line_mix_id;
$transmission->object_class = "CPrescriptionLineMix";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription_line_mix", $prescription_line_mix);
$smarty->assign("date"        , $date);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("sejour_id"   , $sejour_id);
$smarty->assign("transmission", $transmission);
$smarty->assign("date"        , mbDate());
$smarty->assign("hour"        , mbTransformTime(null, mbTime(), "%H"));

if($mode_refresh == "timing"){
  $smarty->display("inc_perf_timing.tpl");
} 

if(!$mode_refresh){
  $smarty->display("edit_perf_dossier_soin.tpl");
}

?>