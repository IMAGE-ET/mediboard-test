<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$line_id              = mbGetValueFromGet("line_id");
$object_class         = mbGetValueFromGet("object_class");
$quantite             = mbGetValueFromGet("quantite");
$key_tab              = mbGetValueFromGet("key_tab");
$date                 = mbGetValueFromGet("date");
$heure                = mbGetValueFromGet("heure");
$quantite             = is_numeric($quantite) ? $quantite : '';
$prise_id             = is_numeric($key_tab) ? $key_tab : '';
$unite_prise          = !is_numeric($key_tab) ? utf8_decode($key_tab) : '';
$date_sel             = mbGetValueFromGet("date_sel");
$mode_plan            = mbGetValueFromGet("mode_plan", false);
$prescription_id      = mbGetValueFromGet("prescription_id");
$list_administrations = mbGetValueFromGet("administrations");
$planification_id       = mbGetValueFromGet("planification_id");
$mode_dossier = mbGetValueFromGet("mode_dossier");
$administrations      = array();
$planification = new CAdministration();

// Chargement de la ligne
$line = new $object_class;
$line->load($line_id);

if($line->_class_name == "CPrescriptionLineMedicament"){
  $line->_ref_produit->loadConditionnement();
}

if($list_administrations){
  $_administrations = explode("|",$list_administrations);
  foreach($_administrations as $_administration_id){
    $administration = new CAdministration();
    $administration->load($_administration_id);
    $administration->loadRefsFwd();
    $administration->loadRefLog();
    $line =& $administration->_ref_object;
    $line->loadRefsFwd();
    if($line->_class_name == "CPrescriptionLineMedicament"){
      $line->_ref_produit->loadConditionnement();
    }
    $administrations[$administration->_id] = $administration;
  }
}

if($planification_id){
  $planification->load($planification_id);
  $planification->loadRefsFwd();
  $planification->loadRefLog();
  $line =& $planification->_ref_object;
  $line->loadRefsFwd();
  if($line->_class_name == "CPrescriptionLineMedicament"){
    $line->_ref_produit->loadConditionnement();
  }
}

// Si une prise est specifiée (pas de moment unitaire), on charge la prise pour stocker l'unite de prise
if($prise_id){
	$prise = new CPrisePosologie();
	$prise->load($prise_id);
	$unite_prise = $prise->unite_prise;
}

$prise = new CPrisePosologie();
$prise->quantite = $quantite;

$dateTime = "$date $heure:00:00";

// Chargement du sejour
$line->_ref_prescription->loadRefObject();
$sejour = $line->_ref_prescription->_ref_object;
$sejour->loadRefPatient();
$sejour->_ref_patient->loadRefsAffectations();
$sejour->_ref_patient->_ref_curr_affectation->updateFormFields();

// Heures disponibles pour l'administration
$hours = range(0,23);
foreach($hours as &$_hour){
  $_hour = str_pad($_hour, 2, "0", STR_PAD_LEFT);
}

// Transmission
$transmission = new CTransmissionMedicale();
$transmission->loadAides($AppUI->user_id);
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("key_tab", $key_tab);
$smarty->assign("date_sel", $date_sel);
$smarty->assign("administrations", $administrations);
$smarty->assign("planification", $planification);
$smarty->assign("transmission", $transmission);
$smarty->assign("line", $line);
$smarty->assign("unite_prise", $unite_prise);
$smarty->assign("prise", $prise);
$smarty->assign("sejour", $sejour);
$smarty->assign("date", $date);
$smarty->assign("prise_id", $prise_id);
$smarty->assign("dateTime", $dateTime);
$smarty->assign("date", $date);
$smarty->assign("notToday", $date != mbDate());
$smarty->assign("mode_plan", $mode_plan);
$smarty->assign("hours", $hours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->display("../../dPprescription/templates/inc_vw_add_administration.tpl");
?>