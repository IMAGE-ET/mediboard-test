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
$protocole_id    = mbGetValueFromPost("protocole_id");
$date_sel  = mbGetValueFromPost("debut", mbDate());
$praticien_id    = mbGetValueFromPost("praticien_id", $AppUI->user_id);

if(!$protocole_id){
	exit();
}
// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);

// Chargement des lignes de medicaments, d'elements et de commentaires
$protocole->loadRefsLinesMed();
$protocole->loadRefsLinesElement();
$protocole->loadRefsLinesAllComments();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

$sejour = new CSejour();
if($prescription->_ref_object->_class_name == "CSejour"){
	$sejour =& $prescription->_ref_object;
}

 
// Parcours des lignes de prescription
foreach($protocole->_ref_prescription_lines as $line){
	$line->loadRefsPrises();
	  
  $line->_id = "";
  $line->unite_duree = "jour";
  
  // Cas du sejour
  switch($line->jour_decalage){
  	case 'E': 
  	  $date = $sejour->_entree;
  		break;
  	case 'I': 
  	  $date = $date_sel;	
  		break;
  	case 'S':  
  	  $date = $sejour->_sortie;
  		break;
  	case 'N':  
  	  $date = mbDate();	
  		break;
  }
  
  // Cas d'une consultation
  if(!$line->jour_decalage){
  	$date = $date_sel;
  }
  
  $signe = ($line->decalage_line >= 0) ? "+" : "";
  if($line->decalage_line){
    $line->debut = mbDate("$signe $line->decalage_line DAYS", $date);	
  } else {
  	$line->debut = $date;
  }
  $line->prescription_id = $prescription_id;
  $line->praticien_id = $praticien_id;
  $line->creator_id = $AppUI->user_id;
  $msg = $line->store();
  $AppUI->displayMsg($msg, "msg-CPrescriptionLineMedicament-create");  
  	
	// Parcours des prises
	foreach($line->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line->_id;
		$prise->object_class = "CPrescriptionLineMedicament";
	  $msg = $prise->store();
	  $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

// Parcours des lignes d'elements
foreach($protocole->_ref_prescription_lines_element as $line_element){
  $line_element->loadRefsPrises();
	$line_element->_id = "";
  $line_element->unite_duree = "jour";
  
  // Cas du sejour
  switch($line_element->jour_decalage){
  	case 'E': 
  	  $date = $sejour->_entree;
  		break;
  	case 'I': 
  	  $date = $date_sel;	
  		break;
  	case 'S':  
  	  $date = $sejour->_sortie;
  		break;
  	case 'N':  
  	  $date = mbDate();	
  		break;
  }
  
  // Cas d'une consultation
  if(!$line_element->jour_decalage){
  	$date = $date_sel;
  }
  
  $chapitre = $line_element->_ref_element_prescription->_ref_category_prescription->chapitre;
  
  if($chapitre != "dmi"){
	  $signe = ($line_element->decalage_line >= 0) ? "+" : "";
	  if($line_element->decalage_line){
	    $line_element->debut = mbDate("$signe $line_element->decalage_line DAYS", $date);	
	  } else {
	  	$line_element->debut = $date;
	  }	
  }
  
  $line_element->prescription_id = $prescription_id;
  $line_element->praticien_id = $praticien_id;
  $line_element->creator_id = $AppUI->user_id;
  $msg = $line_element->store();
  $AppUI->displayMsg($msg, "msg-CPrescriptionLineElement-create");  
  
  // Parcours des prises
	foreach($line_element->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line_element->_id;
		$prise->object_class = "CPrescriptionLineElement";
	  $msg = $prise->store();
	  $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

// Parcours des lignes de commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$line_comment->_id = "";
	$line_comment->prescription_id = $prescription_id;
	$line_comment->praticien_id = $praticien_id;
	$line_comment->creator_id = $AppUI->user_id;
	$msg = $line_comment->store();
	$AppUI->displayMsg($msg, "msg-CPrescriptionLineComment-create");
}

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id)</script>";
echo $AppUI->getMsg();
exit();   

?>