<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

// Dans le cas de la validation de la totalite des prescriptions
$prescription_id = CValue::post("prescription_id");
$prescription_reelle_id = CValue::post("prescription_reelle_id");
$mode_pharma = CValue::post("mode_pharma");
$chapitre = CValue::post("chapitre", "medicament");
$annulation = CValue::request("annulation", "0");
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
	  $praticien_id = CValue::post("praticien_id");
	  $password = CValue::post("password");
	  
	  $praticien = new CMediusers();
	  $praticien->load($praticien_id);
	  
	  // Test du password
		$user = new CUser();
		$user->user_username = $praticien->_user_username;
		$user->_user_password = $password;
	
		if(!$password){
			if(!$user->_id){
			  CAppUI::displayMsg("Veuillez saisir un mot de passe", "Signature des lignes");
	      return;
		  }
		}
		$user->loadMatchingObject();
		if(!$user->_id){
		  CAppUI::displayMsg("Login incorrect","Signature des lignes");
	    return;
		}	
	}
}

if($prescription_id){
	$prescription = new CPrescription();
	$prescription->load($prescription_id);
}

// Pour la validation d'une ligne precise
$prescription_line_guid = CValue::post("prescription_line_guid");

// Initalisation du tableau de lignes
$lines = array("CPrescriptionLineMedicament" => array(), "CPrescriptionLineMix" => array(), "CPrescriptionLineElement" => array(), "CPrescriptionLineComment" => array());

/*
 * Signature d'une ligne (medicament ou prescription_line_mix)
 */
if($prescription_line_guid){
	// Chargement de la ligne de prescription (medicament ou prescription_line_mix)
	$prescription_line = CMbObject::loadFromGuid($prescription_line_guid);
	
	// On rajoute la ligne passée au tableau des lignes à traiter
	$lines[$prescription_line->_class_name][$prescription_line->_id] = $prescription_line;

	// Si la ligne peut etre substituée par les infirmieres, elle est automatiquement signée
	if($prescription_line->substitute_for_id){
		$original_line = new $prescription_line->substitute_for_class;
		$original_line->load($prescription_line->substitute_for_id);
		$subst_plan_soin = $original_line->substitution_plan_soin;
	} else {
		$subst_plan_soin = $prescription_line->substitution_plan_soin;
	}
	if($subst_plan_soin){
		$prescription_line->loadRefsSubstitutionLines();
		foreach($prescription_line->_ref_substitution_lines as $_subst_lines_by_type){
		  foreach($_subst_lines_by_type as $_subst_line){
		  	$lines[$_subst_line->_class_name][$_subst_line->_id] = $_subst_line;
		  }
		}
	}
  // Chargement de la prescription
  $prescription = new CPrescription();
  $prescription->load($prescription_line->prescription_id);
}


/*
 * Signature de toutes les lignes de medicaments / prescription_line_mixes
 */
if($prescription_id && ($chapitre=="medicament" || $chapitre == "all") && !$mode_pharma){
	// Chargement de toutes les lignes du user_courant non validées
	$prescriptionLineMedicament = new CPrescriptionLineMedicament();
	$where = array();
	$where["prescription_id"] = " = '$prescription_id'";
	$where["praticien_id"] = " = '$praticien_id'";
	$where["signee"] = " = '$search_value'";
  $where["substitution_active"] = " = '1'";
	$lines_med = $prescriptionLineMedicament->loadList($where);
	foreach($lines_med as $_line_med){
		$lines[$_line_med->_class_name][$_line_med->_id] = $_line_med;
	  $_line_med->countSubstitutionsLines();
		if($_line_med->_count_substitution_lines){
			$_line_med->loadRefsSubstitutionLines();
			if($_line_med->_ref_substitute_for->substitution_plan_soin){
		    foreach($_line_med->_ref_substitution_lines as $_subst_lines_by_type){
		      foreach($_subst_lines_by_type as $_subst_line){
		        $lines[$_subst_line->_class_name][$_subst_line->_id] = $_subst_line;
		      } 
		    }
			}
		}
	}

	// Chargement des prescription_line_mixes
  $prescription_line_mix = new CPrescriptionLineMix();
  $where = array();
  $where["prescription_id"] = " = '$prescription_id'";
  $where["praticien_id"] = " = '$praticien_id'";
  $where["signature_prat"] = " = '$search_value'";
  $where["substitution_active"] = " = '1'";
  $lines_perf = $prescription_line_mix->loadList($where);
  foreach($lines_perf as $_line_perf){
  	$lines[$_line_perf->_class_name][$_line_perf->_id] = $_line_perf;
    $_line_perf->countSubstitutionsLines();
    if($_line_perf->_count_substitution_lines){
      $_line_perf->loadRefsSubstitutionLines();
      if($_line_perf->_ref_substitute_for->substitution_plan_soin){
        foreach($_line_perf->_ref_substitution_lines as $_subst_lines_by_type){
          foreach($_subst_lines_by_type as $_subst_line){
            $lines[$_subst_line->_class_name][$_subst_line->_id] = $_subst_line;
          } 
        }
      }
    }
  }

  $prescriptionLineComment = new CPrescriptionLineComment();
  $where = array();
  $where["prescription_id"] = " = '$prescription_id'";
  $where["praticien_id"] = " = '$praticien_id'";
  $where["category_prescription_id"] = "IS NULL";
  $where["signee"] = " = '$search_value'";
  $where["child_id"] = "IS NULL";
  $lines["CPrescriptionLineComment"] = $prescriptionLineComment->loadList($where);
	
	// Chargement de la prescription
	$prescription = new CPrescription();
  $prescription->load($prescription_id);
}

/*
 * Signature des lignes d'elements
 */
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
  $lines["CPrescriptionLineElement"] = $prescription_line_element->loadList($where, null, null, null, $ljoinElement);
  
  $prescription_line_comment = new CPrescriptionLineComment();
  $lines["CPrescriptionLineComment"] = $prescription_line_comment->loadList($where, null, null, null, $ljoinComment);
}


/*
 * Pharmacie => validation de toutes les lignes actives
 */ 
if($prescription->_id && $mode_pharma){
  $prescription->loadRefsLinesMed();
  $prescription->loadRefsPrescriptionLineMixes();  
  foreach ($prescription->_ref_prescription_lines as &$_line_med) {
    if(!$_line_med->child_id && $_line_med->substitution_active){
      $lines[$_line_med->_class_name][$_line_med->_id] = $_line_med;
    }
  }
  foreach($prescription->_ref_prescription_line_mixes as &$_prescription_line_mix){
    if(!$_prescription_line_mix->next_line_id && $_prescription_line_mix->substitution_active){
      $lines[$_prescription_line_mix->_class_name][$_prescription_line_mix->_id] = $_prescription_line_mix;
    }
  }
}

// Parcours du tableau et signature des lignes
foreach($lines as $_type_line => $_lines){
	foreach($_lines as $_line){
		switch($_type_line){
			case "CPrescriptionLineMedicament":
				$_line->countPrisesLine();
				$mode_pharma ? ($_line->valide_pharma = 1) : ($_line->signee = $new_value);
				if($new_value && !$_line->_count_prises_line && !$mode_pharma){
				  CAppUI::displayMsg("Impossible de signer une ligne qui ne possède pas de posologie", "$_line->_class_name-title-modify"); 
				} else {
					if(!$mode_pharma && !$new_value){
						$_line->valide_pharma = '0';
					}
					$msg = $_line->store();
	        CAppUI::displayMsg($msg, "$_line->_class_name-msg-modify"); 	
				}
        break;
			case "CPrescriptionLineMix":
				$mode_pharma ? ($_line->signature_pharma = 1) : ($_line->signature_prat = $new_value);
				if(!$mode_pharma && !$new_value){
          $_line->signature_pharma = '0';
        }
				$msg = $_line->store();
        CAppUI::displayMsg($msg, "$_line->_class_name-msg-modify"); 
				break;
			case "CPrescriptionLineElement":
			case "CPrescriptionLineComment":
				$_line->signee = $new_value;
			  $msg = $_line->store();
        CAppUI::displayMsg($msg, "$_line->_class_name-msg-modify"); 
      	break;
		}
	}
}

// Refresh de la prescription
$prescription_id = ($prescription_reelle_id) ? $prescription_reelle_id : $prescription->_id;
if($chapitre == "all"){
  if($mediuser->_is_praticien){
     // Dans le cas de la signature directement dans la prescription 
     echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null);</script>";  
     echo CAppUI::getMsg();
     CApp::rip();
  } else {
    // Dans le cas de la signature dans la popup (le user courant n'est pas un praticien)
    echo "<script type='text/javascript'>window.opener.Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null);</script>";  
  }
} else {
  // Dans le cas de la validation d'un chapitre ou d'une ligne de la prescription
  echo "<script type='text/javascript'>Prescription.reload($prescription_id,'', '$chapitre','','$mode_pharma');</script>";
  echo CAppUI::getMsg();
  CApp::rip();
}

?>