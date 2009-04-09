<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$perfusion_id = mbGetValueFromGet("perfusion_id");
$date         = mbGetValueFromGet("date");
$mode_dossier = mbGetValueFromGet("mode_dossier");
$sejour_id    = mbGetValueFromGetOrSession("sejour_id");
$mode_refresh = mbGetValueFromGet("mode_refresh");

// Chargement de la perfusion
$perfusion = new CPerfusion();
$perfusion->load($perfusion_id);

// Refresh des transmissions
if($mode_refresh == "trans"){
  $transmissions = array();
  // Chargement des transmissions
  $transmission = new CTransmissionMedicale();
  $transmission->object_id = $perfusion_id;
  $transmission->object_class = "CPerfusion";
  $transmissions = $transmission->loadMatchingList();
  foreach($transmissions as $_transmission){
    $_transmission->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("perfusion", $perfusion);
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