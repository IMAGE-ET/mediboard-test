<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$etablissement = new CGroups();
$etablissement->load($g);

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();
$prescription->_ref_praticien->loadRefsFwd();

$prescription->loadRefsLinesMedComments();

$prescription->loadRefsLinesElementsComments();

$nb_ald["medicament"] = 0;
$nb_ald["dmi"] = 0;
$nb_ald["anapath"] = 0;
$nb_ald["biologie"] = 0;
$nb_ald["imagerie"] = 0;
$nb_ald["consult"] = 0;
$nb_ald["kine"] = 0;
$nb_ald["soin"] = 0;


// Initialisation des tableaux
$lines["medicament"]["element"]["ald"] = array();
$lines["medicament"]["comment"]["ald"] = array();
$lines["medicament"]["element"]["no_ald"] = array();
$lines["medicament"]["comment"]["no_ald"] = array();

foreach($prescription->_ref_lines_elements_comments as $chap => $elts){
  $lines[$chap]["element"]["ald"] = array();
  $lines[$chap]["comment"]["ald"] = array();
  $lines[$chap]["element"]["no_ald"] = array();
  $lines[$chap]["comment"]["no_ald"] = array();
}

// Recherche de medicaments ALD 
foreach($prescription->_ref_lines_med_comments["med"] as $key => $med){
  if($med->ald){
	  $lines["medicament"]["element"]["ald"][] = $med;
  } else {
  	$lines["medicament"]["element"]["no_ald"][] = $med;
  }
}
foreach($prescription->_ref_lines_med_comments["comment"] as $key => $comment){
	if($comment->ald){
	  $lines["medicament"]["comment"]["ald"][] = $comment;
	} else {
		$lines["medicament"]["comment"]["no_ald"][] = $comment;
	}
}



// Recherche de produits ALD
foreach($prescription->_ref_lines_elements_comments as $nom_chap => $chapitre){
	foreach($chapitre["element"] as &$element){
		if($element->ald){
		  $lines[$nom_chap]["element"]["ald"][] = $element;
		} else {
			$lines[$nom_chap]["element"]["no_ald"][] = $element;
		}
	}
	foreach($chapitre["comment"] as &$comment){
	  if($comment->ald){
		  $lines[$nom_chap]["comment"]["ald"][] = $comment;
		} else {
			$lines[$nom_chap]["comment"]["no_ald"][] = $comment;
		}
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"         , mbDate());
$smarty->assign("etablissement", $etablissement);
$smarty->assign("prescription" , $prescription);
$smarty->assign("lines", $lines);
$smarty->display("print_prescription.tpl");

?>