<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$prescription_id = mbGetValueFromGet("prescription_id");
$type = mbGetValueFromGet("type");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->loadRefsLinesMed();
$prescription->loadRefsPerfusions();

// Chargement de la prescription traitement
$prescription->loadRefObject();
$object =& $prescription->_ref_object;
$object->loadRefPrescriptionTraitement();
$prescription_traitement =& $object->_ref_prescription_traitement;
if($prescription_traitement->_id){
  $prescription_traitement->loadRefsLinesMed('','1');
}
// Tableau d'historique des lignes
$hist = array();
$lines = array();

$med_lines = array();
$med_lines["med"] = $prescription->_ref_prescription_lines;
$med_lines["perf"] = $prescription->_ref_perfusions;
$med_lines["traitement"] = $prescription_traitement->_ref_prescription_lines;

// Chargement de l'historique de chaque ligne
foreach($med_lines as $_type_line => $meds_by_cat){
	if(is_array($meds_by_cat)){
		foreach($meds_by_cat as &$line){
		  if($line->_class_name == "CPerfusion"){
        $parent_lines = $line->loadRefsParents();
        $lines["perf"][$line->_id]= $line;
			  foreach($parent_lines as &$_parent_line){
			  	$_parent_line->loadRefsLines();
			  }
			  $hist["perf"][$line->_id] = $parent_lines;
		  } else {
				// Chargement des parents lines
	      if ($type == "historique"){
				  $parent_lines = $line->loadRefsParents();
	      } else {
	      	$parent_lines = $line->loadRefsPrevLines();
	      }
				ksort($parent_lines);
				if(count($parent_lines) < 2 && $type != "historique"){
					continue;
				}
				$lines["line"][$line->_id]= $line;
			  foreach($parent_lines as &$_parent_line){
			  	$_parent_line->loadRefsPrises();
			  }
			  $hist["line"][$line->_id] = $parent_lines;
		  }
		}
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("hist", $hist);
$smarty->assign("type", $type);
$smarty->display("view_historique.tpl");

?>