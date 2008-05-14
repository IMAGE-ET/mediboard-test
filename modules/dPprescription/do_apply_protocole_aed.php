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

// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);

// Chargement des lignes de medicaments
$protocole->loadRefsLines();

// Chargement des lignes d'elements
$protocole->loadRefsLinesElement();

// Chargement des lignes de commentaire
$protocole->loadRefsLinesAllComments();

// Parcours des lignes de prescription
foreach($protocole->_ref_prescription_lines as $line){
  $new_line = new CPrescriptionLineMedicament();
  $new_line->_id = "";
  $new_line->code_cip = $line->code_cip;
  $new_line->no_poso = $line->no_poso;
  $new_line->commentaire = $line->commentaire;
  $new_line->debut = $line->debut;
  $new_line->duree = $line->duree;
  $new_line->unite_duree = $line->unite_duree;
  $new_line->ald = $line->ald;
  $new_line->prescription_id = $prescription_id;
  $new_line->praticien_id = $AppUI->user_id;
  $msg = $new_line->store();
  viewMsg($msg, "msg-CPrescriptionLineMedicament-create");  
  	
  // Chargement des prises
  $line->loadRefsPrises();

	// Parcours des prises
	foreach($line->_ref_prises as $prise){
		$new_prise = new CPrisePosologie();
	  $new_prise->_id = "";
		$new_prise->object_id = $new_line->_id;
		$new_prise->object_class = "CPrescriptionLineMedicament";
		$new_prise->moment_unitaire_id = $prise->moment_unitaire_id;
	  $new_prise->quantite = $prise->quantite;
	  $new_prise->nb_fois = $prise->nb_fois;
	  $new_prise->unite_fois = $prise->unite_fois;
	  $new_prise->nb_tous_les = $prise->nb_tous_les;
	  $new_prise->unite_tous_les = $prise->unite_tous_les;
	  $msg = $new_prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

// Parcours des lignes d'elements
foreach($protocole->_ref_prescription_lines_element as $line_element){
  $new_line_element = new CPrescriptionLineElement();
  $new_line_element = $line_element;
  $new_line_element->_id = "";
  $new_line_element->prescription_id = $prescription_id;
  $new_line_element->praticien_id = $AppUI->user_id;
  $msg = $new_line_element->store();
  viewMsg($msg, "msg-CPrescriptionLineElement-create");  
}

// Parcours des lignes de commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$new_line_comment = new CPrescriptionLineComment();
	$new_line_comment = $line_comment;
	$new_line_comment->_id = "";
	$new_line_comment->prescription_id = $prescription_id;
	$new_line_comment->praticien_id = $AppUI->user_id;
	$msg = $new_line_comment->store();
	viewMsg($msg, "msg-CPrescriptionLineComment-create");
}


// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($prescription_id)</script>";
echo $AppUI->getMsg();
exit();   

?>