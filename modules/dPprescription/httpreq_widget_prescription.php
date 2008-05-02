<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI;

$object_id = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");
$praticien_id = mbGetValueFromGet("praticien_id", $AppUI->user_id);
$suffixe = mbGetValueFromGet("suffixe");

$prescription = new CPrescription();
if (!$prescription->_ref_module) {
  CAppUI::stepAjax("Module Prescriptions non installé", UI_MSG_WARNING);
  return;
}

if(!$object_id || !$object_class){
	return; 
}

$prescriptions = array();


// Chargement de l'objet de la prescription
$object = new $object_class;
$object->load($object_id);

$totals_by_chapitre = array();
if ($object->_id){
	// Chargement des prescriptions
  $prescriptions = $object->loadBackRefs("prescriptions");
  if($prescriptions){
	  foreach ($prescriptions as &$_prescription){
	  	// Chargement du nombre d'elements pour chaque prescription
	  	$_prescription->loadRefPraticien();
	  	$_prescription->countLinesMedsElements();
	  	
	  	foreach ($_prescription->_counts_by_chapitre as $chapitre => $count) {
	  	  @$totals_by_chapitre[$chapitre]+= $count;
	  	}
	  }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , mbDate());
$smarty->assign("totals_by_chapitre" , $totals_by_chapitre);
$smarty->assign("object_id"          , $object_id);
$smarty->assign("object_class"       , $object_class);
$smarty->assign("suffixe"            , $suffixe);
$smarty->assign("praticien_id"       , $praticien_id);
$smarty->assign("prescriptions"      , $prescriptions);

$smarty->display("inc_widget_prescription.tpl");

?>