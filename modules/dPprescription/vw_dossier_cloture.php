<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m, $g;

// Chargement de la prescription
$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);

$sejour =& $prescription->_ref_object;
$sejour->loadRefPatient();

$dossier = array();
$lines_med = array();
$lines_elt = array();

// Chargement des lignes
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElementByCat();

// Chargement des perfusions
$prescription->loadRefsPerfusions();
foreach($prescription->_ref_perfusions as $_perfusion){
  $_perfusion->loadRefsLines();  
  $dossier[$_perfusion->date_debut]["perfusion"][$_perfusion->_id] = $_perfusion;
}

// Chargement de toutes les transmissions du sejour
$sejour->loadRefsTransmissions();
foreach($sejour->_ref_transmissions as &$_transmission){
	$_transmission->loadRefsFwd();
}

// Parcours des lignes de medicament et stockage du dossier cloturé
foreach($prescription->_ref_prescription_lines as $_line_med){
	$_line_med->_ref_produit->loadConditionnement();
	$lines_med[$_line_med->_id] = $_line_med;
	$_line_med->loadRefsAdministrations();
	foreach($_line_med->_ref_administrations as $_administration_med){
	  if(!$_administration_med->planification){
		  $dossier[mbDate($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
	  }
	}
}

// Parcours des lignes d'elements
foreach($prescription->_ref_prescription_lines_element_by_cat as $chap => $_lines_by_chap){
	foreach($_lines_by_chap as $_lines_by_cat){
		foreach($_lines_by_cat["element"] as $_line_elt){
			$lines_elt[$_line_elt->_id] = $_line_elt;
		  $_line_elt->loadRefsAdministrations();
		  foreach($_line_elt->_ref_administrations as $_administration_elt){
		    if(!$_administration_elt->planification){
		      $dossier[mbDate($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
		    }
		  }
		}
	}
}

ksort($dossier);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dateTime", mbDateTime());
$smarty->assign("sejour", $sejour);
$smarty->assign("lines_med", $lines_med);
$smarty->assign("lines_elt", $lines_elt);
$smarty->assign("dossier", $dossier);
$smarty->display("vw_dossier_cloture.tpl");

?>