<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id = mbGetValueFromPost("protocole_id");

// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);
$protocole->loadRefsLines();
$protocole->loadRefsLinesElement();

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
  if($msg = $new_line->store()){
  	return $msg;
  }

  // Chargement des prises
  $line->loadRefsPrises();

	// Sauvegarde des nouvelles prises
	foreach($line->_ref_prises as $prise){
		$new_prise = new CPrisePosologie();
	  $new_prise->_id = "";
		$new_prise->prescription_line_id = $new_line->_id;
		$new_prise->moment_unitaire_id = $prise->moment_unitaire_id;
	  $new_prise->quantite = $prise->quantite;
	  $new_prise->nb_fois = $prise->nb_fois;
	  $new_prise->unite_fois = $prise->unite_fois;
	  $new_prise->nb_tous_les = $prise->nb_tous_les;
	  $new_prise->unite_tous_les = $prise->unite_tous_les;
	  if($msg = $new_prise->store()){
	  	return $msg;
	  }		
	}
}

foreach($protocole->_ref_prescription_lines_element as $line_element){
  $new_line_element = new CPrescriptionLineElement();
  $new_line_element = $line_element;
  $new_line_element->_id = "";
  $new_line_element->prescription_id = $prescription_id;
  $new_line_element->store();
}



// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($prescription_id)</script>";
exit();   
?>