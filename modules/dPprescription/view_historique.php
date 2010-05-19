<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$type = CValue::get("type");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->loadRefsLinesMed();
$prescription->loadRefsPrescriptionLineMixes();

// Tableau d'historique des lignes
$hist = array();
$lines = array();

$med_lines = array();
$med_lines["med"] = $prescription->_ref_prescription_lines;
if($type == "historique"){
  $med_lines["perf"] = $prescription->_ref_prescription_line_mixes;
}

// Chargement de l'historique de chaque ligne
foreach($med_lines as $_type_line => $meds_by_cat){
	if(is_array($meds_by_cat)){
		foreach($meds_by_cat as &$line){
		  if($line->_class_name == "CPrescriptionLineMix"){
		    $parent_lines = $line->loadRefsParents();
        $lines["perf"][$line->_id]= $line;
			  foreach($parent_lines as &$_parent_line){
			  	$_parent_line->loadRefsLines();
			  }
			  $hist["perf"][$line->_id] = $parent_lines;
		  } else {
				// Chargement des parents lines
			  $line->loadRefCreator();
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
			    $_parent_line->loadRefCreator();
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