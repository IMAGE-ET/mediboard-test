<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $dialog;
$can->needsRead();

$filter = new CPrescription();
$filter->object_class    = mbGetValueFromGet("object_class", "CSejour");
$filter->object_id       = mbGetValueFromGet("object_id");
$prescription_id    = mbGetValueFromGet("prescription_id");

// Initialisation
$patient = new CPatient();
$dossier_medical = new CDossierMedical();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement de l'object
$object = new $filter->object_class;
if($filter->object_id){
  $object->load($filter->object_id);	
}

if($object->_id){
  // Chargement du patient et de son dossier medical
	$object->loadRefPatient();
	$patient =& $object->_ref_patient;
	$patient->loadRefDossierMedical();
	$dossier_medical =& $patient->_ref_dossier_medical;
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->loadRefsTraitements();
  // Chargement des prescrptions
	$object->loadRefsPrescriptions();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->assign("prescription", $prescription);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("filter", $filter);
$smarty->assign("object", $object);
$smarty->display("vw_edit_prescription.tpl");

?>