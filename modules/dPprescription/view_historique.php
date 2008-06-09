<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$prescription_id = mbGetValueFromGet("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->loadRefsLines();

// Chargement de la prescription traitement
$prescription->loadRefObject();
$object =& $prescription->_ref_object;
$object->loadRefPrescriptionTraitement();
$prescription_traitement =& $object->_ref_prescription_traitement;
if($prescription_traitement->_id){
  $prescription_traitement->loadRefsLines();
}
// Tableau d'historique des lignes
$hist = array();
$lines = array();

$med_lines = array();
$med_lines["med"] = $prescription->_ref_prescription_lines;
$med_lines["traitement"] = $prescription_traitement->_ref_prescription_lines;

// Chargement de l'historique de chaque ligne
foreach($med_lines as $meds_by_cat){
	if(is_array($meds_by_cat)){
		foreach($meds_by_cat as &$line){
			// Chargement des parents lines
			$parent_lines = $line->loadRefsparents();
			ksort($parent_lines);
			//if(count($parent_lines) < 2){
			//	continue;
			//}
			$lines[$line->_id]= $line;
		  foreach($parent_lines as &$_parent_line){
		  	$_parent_line->loadRefsPrises();
		  }
		  $hist[$line->_id] = $parent_lines;
		}
	}
}



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("hist", $hist);
$smarty->display("view_historique.tpl");

?>