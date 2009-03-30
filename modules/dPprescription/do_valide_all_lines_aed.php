<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

/*
 * Permet de valider une ou toutes les lignes
 * en mode pharmacien ou praticien
 */


// Fonction permettant d'inserer $lines dans $prescription
function insertMedElts($lines, $prescription, $sejour){
	global $AppUI;
	
  if($prescription->type == "sejour"){ 
    $limit = $sejour->_entree;	
  }
  if($prescription->type == "sortie"){ 
    $limit = $sejour->_sortie;	
  }
  				
  // Chargement des lignes de medicament deja presents dans la prescription
  $medicaments = array();
  $line_medicament = new CPrescriptionLineMedicament();
  $line_medicament->prescription_id = $prescription->_id;
  $medicaments = $line_medicament->loadMatchingList();
  $produits = array();
  foreach($medicaments as &$_medicament){
    $produits[$_medicament->code_cip] = $_medicament;  	
  }	
  
  // Chargement des elements deja presents dans la prescription
  $elements = array();
  $line_element = new CPrescriptionLineElement();
  $line_element->prescription_id = $prescription->_id;
  $elements = $line_element->loadMatchingList();
  
  // Parcours des elements de la prescription courante
  foreach($lines as $cat => $lines_by_cat){
  	foreach($lines_by_cat as &$line){
  		// Si le medicament est deja present dans la prescription, on passe a la ligne suivante
  		if($cat == "medicament" && array_key_exists($line->code_cip,$produits)){
      	continue;
  		}
  		// Si l'element est deja present, on passe a la ligne suivante
  		if($cat == "element" && array_key_exists($line->_id, $elements)){
  			continue;
  		}	
	
  		// On ne duplique pas la ligne si elle est finie
  		if($line->_fin_reelle && $line->_fin_reelle < $limit){
	      continue;
	    }
		
  		// Chargement des prises 
			$line->loadRefsPrises();
			
  		// Adaptation des dates
			if($line->debut < mbDate($limit)){
				$diff_duree = mbDaysRelative($line->debut, mbDate($limit));
				if( $line->duree - $diff_duree > 0){
					$line->duree = $line->duree - $diff_duree;
					$line->debut = mbDate($limit);
				}
			}					
			$line->_id = "";
	    $line->prescription_id = $prescription->_id;
	    $line->signee = 0;
	    if($cat == "medicament"){
	      $line->valide_pharma = 0;
	    }
	    $msg = $line->store();
	    $AppUI->displayMsg($msg, "msg-$line->_class_name-create");  
	  
		  // Parcours des prises et creation des nouvelles prises
		  foreach($line->_ref_prises as $prise){
			  $prise->_id = "";
			  $prise->object_id = $line->_id;
			  $msg = $prise->store();
		    $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");  	
		  }
  	}
  }
}



global $AppUI;

// Dans le cas de la validation de la totalite des prescriptions
$prescription_id = mbGetValueFromPost("prescription_id");
$prescription_reelle_id = mbGetValueFromPost("prescription_reelle_id");
$mode_pharma = mbGetValueFromPost("mode_pharma");
$chapitre = mbGetValueFromPost("chapitre", "medicament");
$annulation = mbGetValueFromGet("annulation", "0");
$search_value = $annulation ? 1 : 0;
$new_value = $annulation ? 0 : 1;

$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->isPraticien();

if(!$mode_pharma){
	if($mediuser->_is_praticien){
	  // Si le user est un praticien
	  $praticien_id = $AppUI->user_id;  
	} else {
	  // Sinon, on controle son password
	  $praticien_id = mbGetValueFromPost("praticien_id");
	  $password = mbGetValueFromPost("password");
	  
	  $praticien = new CMediusers();
	  $praticien->load($praticien_id);
	  
	  // Test du password
		$user = new CUser();
		$user->user_username = $praticien->_user_username;
		$user->_user_password = $password;
	
		if(!$password){
			if(!$user->_id){
			  $AppUI->displayMsg("Veuillez saisir un mot de passe", "Signature des lignes");
	      return;
		  }
		}
		$user->loadMatchingObject();
		if(!$user->_id){
		  $AppUI->displayMsg("Login incorrect","Signature des lignes");
	    return;
		}	
	}
}

if($prescription_id){
	$prescription = new CPrescription();
	$prescription->load($prescription_id);
}

// Pour la validation d'une ligne precise
$prescription_line_id = mbGetValueFromPost("prescription_line_id");

$medicaments = array();
$elements = array();
$comments = array();
$lines = array();
$perfusions = array();

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



// Validation des traitements perso
if($prescription_id && ($chapitre == "medicament" || $chapitre == "all")){
  
	$prescription->_ref_object->loadRefPrescriptionTraitement();
	$prescription_traitement =& $prescription->_ref_object->_ref_prescription_traitement;
	$prescription_traitement->loadRefsLinesMed();
	foreach($prescription_traitement->_ref_prescription_lines as $_line_traitement){
		if($mode_pharma){
	  	$_line_traitement->valide_pharma = 1;		
		} else {
		  $_line_traitement->signee = $search_value;
		}
		$msg = $_line_traitement->store();
	  $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");	
	}
}


// Si mode pharma en validation globale, chargement de tous les medicaments de la prescription
if($prescription->_id && $mode_pharma){
	$prescription->loadRefsLinesMed();
  $prescription->loadRefsPerfusions();	
  foreach ($prescription->_ref_prescription_lines as &$_line_med) {
  	if(!$_line_med->child_id){
	    $medicaments[$_line_med->_id] = $_line_med;
  	}
  }
  foreach($prescription->_ref_perfusions as &$_perfusion){
    if(!$_perfusion->next_perf_id){
      $perfusions[$_perfusion->_id] = $_perfusion;
    }
  }
}

// Validation de tous les medicaments
if($prescription_id && ($chapitre=="medicament" || $chapitre == "all") && !$mode_pharma){
	// Chargement de toutes les lignes du user_courant non validées
	$prescriptionLineMedicament = new CPrescriptionLineMedicament();
	$prescriptionLineMedicament->prescription_id = $prescription_id;
  $prescriptionLineMedicament->praticien_id = $praticien_id;
	$prescriptionLineMedicament->signee = $search_value;
	$medicaments = $prescriptionLineMedicament->loadMatchingList();

	// Chargement des perfusions
  $perfusion = new CPerfusion();
  $perfusion->prescription_id = $prescription_id;
  $perfusion->praticien_id = $praticien_id;
  $perfusion->signature_prat = $search_value;
  $perfusions = $perfusion->loadMatchingList();

  $prescriptionLineComment = new CPrescriptionLineComment();
  $where = array();
  $where["prescription_id"] = " = '$prescription_id'";
  $where["praticien_id"] = " = '$praticien_id'";
  $where["category_prescription_id"] = "IS NULL";
  $where["signee"] = " = '$search_value'";
  $where["child_id"] = "IS NULL";
  $comments = $prescriptionLineComment->loadList($where);
	
	// Chargement de la prescription
	$prescription = new CPrescription();
  $prescription->load($prescription_id);
}


if($prescription_id && ($chapitre!="medicament" || $chapitre == "all") && !$mode_pharma){
	// Elements
	$ljoinElement["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	$ljoinElement["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	
	// Comments
	$ljoinComment["category_prescription"] = "category_prescription.category_prescription_id = prescription_line_comment.category_prescription_id";
	
	$where = array();
	$where["prescription_id"] = " = '$prescription_id'";
	$where["praticien_id"] = " = '$praticien_id'";
	$where["signee"] = " = '$search_value'";
	$where["child_id"] = "IS NULL";
	if($chapitre != "all"){
	  $where["category_prescription.chapitre"] = " = '$chapitre'";
	}
	$prescription_line_element = new CPrescriptionLineElement();
	$elements = $prescription_line_element->loadList($where, null, null, null, $ljoinElement);
	
	$prescription_line_comment = new CPrescriptionLineComment();
	$comments = $prescription_line_comment->loadList($where, null, null, null, $ljoinComment);
}

// Parcours des medicaments et passage de valide à 1
foreach($medicaments as $key => $lineMedicament){
	if($mode_pharma){
		$lineMedicament->valide_pharma = 1;
	} else {
	  $lineMedicament->signee = $new_value;
	}
	$msg = $lineMedicament->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");	
}

// Parcours des perfusions et passage de valide a 1
foreach($perfusions as &$_perfusion){
  if($mode_pharma){
    $_perfusion->signature_pharma = 1;
  } else {
    $_perfusion->signature_prat = $new_value;
  }
  $msg = $_perfusion->store();
  $AppUI->displayMsg($msg, "CPerfusion-msg-store");
}

// Parcours des medicaments et passage de valide à 1
if(!$mode_pharma){
	foreach($elements as $key => $lineElement){
		$lineElement->signee = $new_value;
		$msg = $lineElement->store();
		$AppUI->displayMsg($msg, "CPrescriptionLineElement-msg-modify");	
	}
}

// Parcours des medicaments et passage de valide à 1
if(!$mode_pharma){
	foreach($comments as $key => $lineComment){
		$lineComment->signee = $new_value;
		$msg = $lineComment->store();
		$AppUI->displayMsg($msg, "CPrescriptionLineComment-msg-modify");	
	}
}


// Ajout des lignes a la prescription suivante si la ligne n'est pas déja incluse
if(!$mode_pharma){
	// Stockage dans un tableaux des medicaments et elements de la prescription courante
	$lines["medicament"] = $medicaments;
	$lines["element"] = $elements;
	  
  // Chargement de la prescription de sejour
  $prescription_sejour = new CPrescription();
  $prescription_sejour->object_id = $prescription->object_id;
  $prescription_sejour->object_class = $prescription->object_class;
  $prescription_sejour->type = "sejour";
  $prescription_sejour->loadMatchingObject();
  
  $sejour =& $prescription_sejour->_ref_object;
  
  // Chargement de la prescription de sortie
  $prescription_sortie = new CPrescription();
  $prescription_sortie->object_id = $prescription->object_id;
  $prescription_sortie->object_class = $prescription->object_class;
  $prescription_sortie->praticien_id = $praticien_id;
  $prescription_sortie->type = "sortie";
  $prescription_sortie->loadMatchingObject();
  
  // Si la prescription est de type pre_admission, on insere les medicaments et les elements dans les prescriptions de sejour et de sortie
	if($prescription->type == "pre_admission"){
	  if($prescription_sejour->_id){
		  // Insertion des medicaments et elements de pre_admission dans le sejour
		  insertMedElts($lines, $prescription_sejour, $sejour);
		}
	}

	// Si la prescription est de type pre_admission ou sejour
	if($prescription->type == "pre_admission" || $prescription->type == "sejour"){
		if($prescription_sejour->_id){
			insertMedElts($lines, $prescription_sejour, $sejour);
		}
	  if($prescription_sortie->_id){
      // Insertion des medicaments et elements de pre_admission dans la sortie
			insertMedElts($lines, $prescription_sortie, $sejour);
		}
	}
}

$prescription_id = ($prescription_reelle_id) ? $prescription_reelle_id : $prescription->_id;

if($chapitre == "all"){
  $lite = $AppUI->user_prefs['mode_readonly'] ? 0 : 1;
  if($mediuser->_is_praticien){
     // Dans le cas de la signature directement dans la prescription 
     echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null, true, $lite);</script>";  
     echo $AppUI->getMsg();
     CApp::rip();
  } else {
    // Dans le cas de la signature dans la popup (le user courant n'est pas un praticien)
    echo "<script type='text/javascript'>window.opener.Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null, true, $lite);</script>";  
  }
} else {
  // Dans le cas de la validation d'un chapitre ou d'une ligne de la prescription
  echo "<script type='text/javascript'>Prescription.reload($prescription_id,'', '$chapitre','','$mode_pharma');</script>";
  echo $AppUI->getMsg();
  CApp::rip();
}

?>