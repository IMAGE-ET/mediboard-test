<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

$token_med          = mbGetValueFromPost("token_med");
$token_elt          = mbGetValueFromPost("token_elt");
$prescription_id    = mbGetValueFromPost("prescription_id");
$debut              = mbGetValueFromPost("debut");
$time_debut         = mbGetValueFromPost("time_debut");
$duree              = mbGetValueFromPost("duree");
$unite_duree        = mbGetValueFromPost("unite_duree");
$quantite           = mbGetValueFromPost("quantite");
$nb_fois            = mbGetValueFromPost("nb_fois");
$unite_fois         = mbGetValueFromPost("unite_fois");
$moment_unitaire_id = mbGetValueFromPost("moment_unitaire_id");
$nb_tous_les        = mbGetValueFromPost("nb_tous_les");
$unite_tous_les     = mbGetValueFromPost("unite_tous_les");
$mode_protocole     = mbGetValueFromPost("mode_protocole","0");
$mode_pharma        = mbGetValueFromPost("mode_pharma","0");

$praticien_id = mbGetValueFromPost("praticien_id", $AppUI->user_id);

// Initialisation des tableaux
$lines = array();
$medicaments = array();
$elements = array();

// Explode des listes d'elements et de medicaments
if($token_med){
  $medicaments = explode("|",$token_med);
}
if($token_elt){
  $elements    = explode("|",$token_elt);
}

// Ajout des medicaments dans la prescription
foreach($medicaments as $code_cip){
	$line_medicament = new CPrescriptionLineMedicament();
	$line_medicament->code_cip = $code_cip;
	$line_medicament->prescription_id = $prescription_id;
	$line_medicament->praticien_id = $praticien_id;
	$line_medicament->creator_id = $AppUI->user_id;
	$msg = $line_medicament->store();
	$AppUI->displayMsg($msg, "msg-CPrescriptionLineMedicament-create");
  $lines["medicament"][$line_medicament->_id] = $line_medicament;
}

// Ajout des elements dans la prescription
foreach($elements as $element_id){
	$line_element = new CPrescriptionLineElement();
	$line_element->element_prescription_id = $element_id;
	$line_element->prescription_id = $prescription_id;
	$line_element->praticien_id = $praticien_id;
	$line_element->creator_id = $AppUI->user_id;
	$msg = $line_element->store();
	$AppUI->displayMsg($msg, "msg-CPrescriptionLineElement-create");
	$lines[$line_element->_ref_element_prescription->_ref_category_prescription->chapitre][$line_element->_id] = $line_element;
}

foreach($lines as $cat_name => $lines_by_cat){
	foreach($lines_by_cat as $_line){
		if($cat_name != "dmi"){
      $_line->debut = $debut;
      $_line->time_debut = $time_debut;
	    $_line->duree = $duree;
	    $_line->unite_duree = $unite_duree;
		  $_line->store();
		  if($cat_name != "dm"){
				$prise = new CPrisePosologie();
			  $prise->object_id = $_line->_id;
			  $prise->object_class = $_line->_class_name;	
				
			  // Prise Moment
				if($quantite && $moment_unitaire_id){
				  $prise->quantite = $quantite;
				  $prise->moment_unitaire_id = $moment_unitaire_id;
				  $msg = $prise->store();
				  $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");
				}
				// Prise Fois Par
			  if($quantite && $nb_fois && $unite_fois){
				  $prise->quantite = $quantite;
				  $prise->nb_fois = $nb_fois;
				  $prise->unite_fois = $unite_fois;
				  $msg = $prise->store(); 
			 	  $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");
			  }
			  // Prise Tous Les
			  if($quantite && $nb_tous_les && $unite_tous_les){
				  $prise->quantite = $quantite;
				  $prise->nb_tous_les = $nb_tous_les;
				  $prise->unite_tous_les = $unite_tous_les;
				  $msg = $prise->store();  	
			    $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");
			  } 
		  }
		}
	}
}

// Reload en full mode
if($mode_protocole || $mode_pharma){
echo "<script type='text/javascript'>window.opener.Prescription.reload('$prescription_id','','','$mode_protocole','$mode_pharma')</script>";
} else {
echo "<script type='text/javascript'>window.opener.Prescription.reloadPrescSejour('$prescription_id')</script>";
	
}
echo $AppUI->getMsg();
exit();

?>