<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}

global $AppUI;

// Dans le cas de la validation de la totalite des prescriptions
$prescription_id = mbGetValueFromPost("prescription_id");
$chapitre = mbGetValueFromPost("chapitre", "medicament");

if($prescription_id){
	$prescription = new CPrescription();
	$prescription->load($prescription_id);
}

// Pour la validation d'une ligne precise
$prescription_line_id = mbGetValueFromPost("prescription_line_id");

$medicaments = array();
$elements = array();
$comments = array();

// Validation d'une ligne
if($prescription_line_id){	
	// Ligne de médicaments
	if($chapitre == "medicament"){
		// Chargement de la ligne de prescription
	  $prescription_line = new CPrescriptionLineMedicament();
	  $prescription_line->load($prescription_line_id);
		$medicaments[$prescription_line->_id] = $prescription_line;
	  // Chargement de la prescription
	  $prescription = new CPrescription();
	  $prescription->load($prescription_line->prescription_id);
	}
}


// Validation de tous les medicaments
if($prescription_id && $chapitre=="medicament"){
	// Chargement de toutes les lignes du user_courant non validées
	$prescriptionLineMedicament = new CPrescriptionLineMedicament();
	$prescriptionLineMedicament->prescription_id = $prescription_id;
	$prescriptionLineMedicament->praticien_id = $AppUI->user_id;
	$prescriptionLineMedicament->signee = "0";
	$medicaments = $prescriptionLineMedicament->loadMatchingList();
	
	
	$prescriptionLineComment = new CPrescriptionLineComment();
	$prescriptionLineComment->prescription_id = $prescription_id;
	$prescriptionLineComment->praticien_id = $AppUI->user_id;
	$prescriptionLineComment->signee = "0";
	$comments = $prescriptionLineComment->loadMatchingList();
	
	// Chargement de la prescription
	$prescription = new CPrescription();
  $prescription->load($prescription_id);
}


// Validations de tous les $element
if($prescription_id && $chapitre!="medicament"){
	// Elements
	$ljoinElement["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	$ljoinElement["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	
	// Comments
	$ljoinComment["category_prescription"] = "category_prescription.category_prescription_id = prescription_line_comment.category_prescription_id";
	
	$where = array();
	$where["prescription_id"] = " = '$prescription_id'";
	$where["praticien_id"] = " = '$AppUI->user_id'";
	$where["signee"] = " = '0'";
	
	$where["category_prescription.chapitre"] = " = '$chapitre'";
	
	$prescription_line_element = new CPrescriptionLineElement();
	$elements = $prescription_line_element->loadList($where, null, null, null, $ljoinElement);
	
	$prescription_line_comment = new CPrescriptionLineComment();
	$comments = $prescription_line_comment->loadList($where, null, null, null, $ljoinComment);
}

// Parcours des medicaments et passage de valide à 1
foreach($medicaments as $key => $lineMedicament){
	$lineMedicament->signee = 1;
	$msg = $lineMedicament->store();
	viewMsg($msg, "msg-CPrescriptionLineMedicament-modify");	
}

// Parcours des medicaments et passage de valide à 1
foreach($elements as $key => $lineElement){
	$lineElement->signee = 1;
	$msg = $lineElement->store();
	viewMsg($msg, "msg-CPrescriptionLineElement-modify");	
}

// Parcours des medicaments et passage de valide à 1
foreach($comments as $key => $lineComment){
	$lineComment->signee = 1;
	$msg = $lineComment->store();
	viewMsg($msg, "msg-CPrescriptionLineComment-modify");	
}


// Si une ligne rajoutée dans la preadmission deborde sur le sejour alors que la prescription de sejour a deja ete créée
// On rajoute la ligne dans la prescription de sejour
if($prescription->type == "pre_admission" && $chapitre=="medicament"){
	// On teste s'il existe une prescription de sejour correspondant a l'object
  $prescription_sejour = new CPrescription();
  $prescription_sejour->object_id = $prescription->object_id;
  $prescription_sejour->object_class = $prescription->object_class;
  $prescription_sejour->type = "sejour";
  $prescription_sejour->loadMatchingObject();
  
  // Si la prescription de sejour existe
  if($prescription_sejour->_id){  	
  	// Chargement des tous les produits presents dans la prescription de sejour
    $medicaments_sejour = array();
    $line_medicament = new CPrescriptionLineMedicament();
    $line_medicament->prescription_id = $prescription_sejour->_id;
    $medicaments_sejour = $line_medicament->loadMatchingList();
    $produits = array();
    foreach($medicaments_sejour as &$medicament_line){
    	$produits[$medicament_line->code_cip] = $medicament_line;
    }
  	$sejour = new CSejour();
  	$sejour->load($prescription_sejour->object_id);
  	foreach($medicaments as &$line_med){
  		// Si le medicament est déja present la prescription de sejour, on ne le copie pas
      if(array_key_exists($line_med->code_cip, $produits)){
      	continue;
      }
  		// Si la ligne de prescription possede une duree (_fin calculée)
  		if($line_med->_fin && $line_med->debut && $line_med->duree && $line_med->unite_duree){
  			// si l'une des bornes de la ligne fait partie du sejour
  		  if($line_med->date_arret){
      	  // Si la ligne possède une date d'arret, on modifie la date de fin
          $fin_temp = $line_med->_fin;
      	  $line_med->_fin = $line_med->date_arret;
  		  }
  			if(($line_med->debut > mbDate($sejour->_entree) && $line_med->debut < mbDate($sejour->_sortie)) || 
  			   ($line_med->_fin > mbDate($sejour->_entree) && $line_med->_fin < mbDate($sejour->_sortie)) || 
  			   ($line_med->debut <= mbDate($sejour->_entree) && $line_med->_fin >= mbDate($sejour->_sortie))){
  			  // On duplique la ligne en mettant les valeurs appropriées
          // Chargement des prises
			    $line_med->loadRefsPrises();
          $line_med->_id = "";
          $line_med->prescription_id = $prescription_sejour->_id;
          
          // On ajuste la date d'entree et la duree
		      if($line_med->debut < mbDate($sejour->_entree)){
			 	    $diff_duree = mbDaysRelative($line_med->debut, mbDate($sejour->_entree));
			 	    $line_med->duree = $line_med->duree - $diff_duree;
				    $line_med->debut = mbDate($sejour->_entree);
			      $line_med->unite_duree = "jour";
		   	  }
		   	  
		   	  // Si il y a une date d'arret
		   	  if($line_med->date_arret){
		   	  	$diff_duree2 = mbDaysRelative($line_med->date_arret, $fin_temp);
		       	$line_med->duree = $line_med->duree - $diff_duree2;
		   	  }
		   	  
		   	  $line_med->date_arret = "";
			    $line_med->praticien_id = $AppUI->user_id;
			    $line_med->signee = 0;
			    $msg = $line_med->store();
			    viewMsg($msg, "msg-CPrescriptionLineMedicament-create");
			    
			    foreach($line_med->_ref_prises as &$prise){
			      $prise->_id = "";
			      $prise->prescription_line_id = $line_med->_id;
			      $msg = $prise->store();
			      viewMsg($msg, "msg-CPrisePosologie-create");
			    }
  			}
  		}
  	}
  }
}

echo "<script type='text/javascript'>Prescription.reload($prescription->_id,'', '$chapitre');</script>";
echo $AppUI->getMsg();
exit();

?>