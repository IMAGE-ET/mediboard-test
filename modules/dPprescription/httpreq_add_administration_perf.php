<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6300 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_mix_id = CValue::get("prescription_line_mix_id");
$date = CValue::get("date");
$hour = CValue::get("hour");
$time_prevue = CValue::get("time_prevue");
$mode_dossier = CValue::get("mode_dossier");
$sejour_id = CValue::get("sejour_id");
$date_sel  = CValue::get("date_sel");

$prescription_line_mix = new CPrescriptionLineMix();
$prescription_line_mix->load($prescription_line_mix_id);
$prescription_line_mix->loadRefsLines();
$prescription_line_mix->loadVoies();
$prescription_line_mix->calculQuantiteTotal();

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefCurrAffectation();
$service_id = $sejour->_ref_curr_affectation->_id ? $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->service_id : "none";
$configs = CConfigService::getAllFor($service_id);
$delai_adm = $configs["Delai administration"];

if($time_prevue){
  $_hour = mbTransformTime(null, $time_prevue, '%H');
  $dateTime = "$date $time_prevue";
} else {
  $_hour = $hour;
  $dateTime = "$date $hour:00:00";
}

// Chargement des administrations deja effectuée pour l'heure donnée
$administrations = array();
foreach($prescription_line_mix->_ref_lines as $_perf_line){
  $administration = new CAdministration();
  $where = array();
  $where["object_id"] = " = '$_perf_line->_id'";
  $where["object_class"] = " = '$_perf_line->_class'";
  $where["dateTime"] = " LIKE '$date $_hour%'";
	if($mode_dossier == "planification"){
	  $where["planification"] = " = '1'";
	} else {
	  $where["planification"] = " = '0'";
  }
  $administrations[$_perf_line->_id] = $administration->loadList($where);
}

$quantites = array();

// Si une planification a ete faite, chargement des valeur de celle-ci
if($mode_dossier == "administration"){
	foreach($prescription_line_mix->_ref_lines as $_perf_line){
		$planif = new CAdministration();
		$planifications = array();
	  $where = array();
	  $where["object_id"] = " = '$_perf_line->_id'";
	  $where["object_class"] = " = '$_perf_line->_class'";
	  $where["dateTime"] = " LIKE '$date $_hour%'";
	  $where["planification"] = " = '1'";
	  $planifications = $administration->loadList($where);
		
		if(count($planifications)){
			$_perf_line->_quantite_administration = 0;
	    foreach($planifications as $_planif){
	      $_perf_line->_quantite_administration += $_planif->quantite;
	    }	
		}
	}
}

foreach($administrations as $_administrations){
  foreach($_administrations as $_admin){
    $_admin->loadRefsFwd();
  }
}

$nb_hours = floor(abs(mbHoursRelative($dateTime, mbDateTime())));
$locked = $nb_hours > $delai_adm;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("locked", $locked);
$smarty->assign("delai_adm", $delai_adm);
$smarty->assign("prescription_line_mix", $prescription_line_mix);
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