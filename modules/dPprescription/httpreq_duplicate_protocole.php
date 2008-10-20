<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

$protocole_id = mbGetValueFromGet("protocole_id");

// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);
$protocole->loadRefsLinesMed();
$protocole->loadRefsLinesElement();
$protocole->loadRefsLinesAllComments();

// Creation du nouveau protocole
$protocole->_id = "";
$protocole->libelle = "Copie de $protocole->libelle";
$msg = $protocole->store();
$AppUI->displayMsg($msg, "CPrescription-msg-create");

// Parcours des medicaments
foreach($protocole->_ref_prescription_lines as $line){
	$line->loadRefsPrises();
	$line->prescription_id = $protocole->_id;
	$line->_id = "";
	$msg = $line->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
	
	// Parcours des prises
	foreach($line->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line->_id;
		$msg = $prise->store();
	  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
	}
}

// Parcours des elements
foreach($protocole->_ref_prescription_lines_element as $line_element){
	$line_element->loadRefsPrises();
	$line_element->prescription_id = $protocole->_id;
	$line_element->_id = "";
	$line_element->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineElement-msg-create");
	
  // Parcours des prises
	foreach($line_element->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line_element->_id;
		$msg = $prise->store();
	  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
	}
}

// Parcours des commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$line_comment->prescription_id = $protocole->_id;
	$line_comment->_id = "";
	$line_comment->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineComment-msg-create");
}


// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Protocole.edit($protocole->_id)</script>";
echo $AppUI->getMsg();
CApp::rip();
?>