<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id = mbGetValueFromPost("protocole_id");
$debut = mbGetValueFromPost("debut");
$praticien_id = mbGetValueFromPost("praticien_id", $AppUI->user_id);

if(!$debut){
	$debut = mbDate();
}
if(!$protocole_id){
	exit();
}
// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);

// Chargement des lignes de medicaments, d'elements et de commentaires
$protocole->loadRefsLines();
$protocole->loadRefsLinesElement();
$protocole->loadRefsLinesAllComments();

// Parcours des lignes de prescription
foreach($protocole->_ref_prescription_lines as $line){
	$line->loadRefsPrises();
	  
  $line->_id = "";
  $line->debut = $debut;
  $line->unite_duree = "jour";
  if($line->decalage_line && $line->decalage_line > 0){
    $line->debut = mbDate("+ $line->decalage_line DAYS", $line->debut);
  }
  $line->prescription_id = $prescription_id;
  $line->praticien_id = $praticien_id;
  $msg = $line->store();
  viewMsg($msg, "msg-CPrescriptionLineMedicament-create");  
  	
	// Parcours des prises
	foreach($line->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line->_id;
		$prise->object_class = "CPrescriptionLineMedicament";
	  $msg = $prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

// Parcours des lignes d'elements
foreach($protocole->_ref_prescription_lines_element as $line_element){
  $line_element->loadRefsPrises();
  
	$line_element->_id = "";
  $line_element->unite_duree = "jour";
  if($line_element->_ref_element_prescription->_ref_category_prescription->chapitre != "dmi"){
	  $line_element->debut = $debut;
	  if($line_element->decalage_line && $line_element->decalage_line > 0){
	    $line_element->debut = mbDate("+ $line_element->decalage_line DAYS", $line_element->debut);
	  }
  }
  $line_element->prescription_id = $prescription_id;
  $line_element->praticien_id = $praticien_id;
  $msg = $line_element->store();
  viewMsg($msg, "msg-CPrescriptionLineElement-create");  
  
  // Parcours des prises
	foreach($line_element->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line_element->_id;
		$prise->object_class = "CPrescriptionLineElement";
	  $msg = $prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

// Parcours des lignes de commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$line_comment->_id = "";
	$line_comment->prescription_id = $prescription_id;
	$line_comment->praticien_id = $praticien_id;
	$msg = $line_comment->store();
	viewMsg($msg, "msg-CPrescriptionLineComment-create");
}

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($prescription_id)</script>";
echo $AppUI->getMsg();
exit();   

?>