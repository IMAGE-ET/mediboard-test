<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

// Chargement de l'etablissement
$etablissement = new CGroups();
$etablissement->load($g);

// Chargement du praticien
$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();
$prescription->_ref_praticien->loadRefsFwd();

// Chargement de toutes les categories
$categorie = new CCategoryPrescription();
$cats = $categorie->loadList();
foreach($cats as $key => $cat){
	$categories["cat".$key] = $cat;
}

// Chargement de la liste des executants
$executant = new CExecutantPrescriptionLine();
$executants = $executant->loadList();

// Chargement des lignes de prescriptions
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();

$medicament = 0;
$comment = 0;


// Parcours des medicaments, pas de gestion d'executant pour les medicaments
$lines["medicaments"]["med"]["ald"] = array();
$lines["medicaments"]["med"]["no_ald"] = array();
$lines["medicaments"]["comment"]["ald"] = array();
$lines["medicaments"]["comment"]["no_ald"] = array();
foreach($prescription->_ref_lines_med_comments as $key => $lines_medicament_type){
	foreach($lines_medicament_type as $line_medicament){
	  if($line_medicament->ald){
	    $lines["medicaments"][$key]["ald"][] = $line_medicament;
	  } else {
	  	$lines["medicaments"][$key]["no_ald"][] = $line_medicament;
	  }
	}
}

$linesElt = array();

// Initialisation du tableau
foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
	foreach($chap_element as $name_cat => $cat_element){
		foreach($cat_element as $type => $elements){
			foreach($elements as $element){
			  $executant = "aucun";
		    if($element->executant_prescription_line_id){
		      $executant = $element->executant_prescription_line_id;
		    }
				
	      $linesElt[$name_chap][$name_cat][$executant]["element"]["ald"] = array();
		    $linesElt[$name_chap][$name_cat][$executant]["element"]["no_ald"] = array();	
		    
	      $linesElt[$name_chap][$name_cat][$executant]["comment"]["ald"] = array();
		    $linesElt[$name_chap][$name_cat][$executant]["comment"]["no_ald"] = array();
			}
		}
	}
}

// Parcours des elements
foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
	foreach($chap_element as $name_cat => $cat_element){
		foreach($cat_element as $type => $elements){
			foreach($elements as $element){
		    $executant = "aucun";
		    if($element->executant_prescription_line_id){
		      $executant = $element->executant_prescription_line_id;
		    }
		    if($element->ald){
		    	$linesElt[$name_chap][$name_cat][$executant][$type]["ald"][] = $element;
		    } else {
		      $linesElt[$name_chap][$name_cat][$executant][$type]["no_ald"][] = $element;
		    }
		  }
		}
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"         , mbDate());
$smarty->assign("etablissement", $etablissement);
$smarty->assign("prescription" , $prescription);
$smarty->assign("lines", $lines);
$smarty->assign("linesElt", $linesElt);
$smarty->assign("categories", $categories);
$smarty->assign("executants", $executants);
$smarty->display("print_prescription.tpl");

?>