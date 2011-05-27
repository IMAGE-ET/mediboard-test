<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$user = CUser::get();

$prescription_id   = CValue::post("prescription_id");
$pack_protocole_id = CValue::post("pack_protocole_id");

$date_sel        = CValue::post("debut");
$time_sel        = CValue::post("time_debut");

$praticien_id    = CValue::post("praticien_id", $user->_id);
$operation_id    = CValue::post("operation_id");
$pratSel_id      = CValue::post("pratSel_id");
$protocole_sel_id    = CValue::post("protocole_id");

// Si aucun pack/protocole selectionne, on ne fait rien
if (!$pack_protocole_id){
  CAppUI::setMsg("Aucun protocole n'a été sélectionné", UI_MSG_ERROR);
  echo CAppUI::getMsg();
  CApp::rip();
}

// Si l'utilisateur est une infirmiere, on verifie si le protocole ne contient pas des lignes non prescriptibles
$current_user = new CMediusers();
$current_user->load($user->_id);
if($current_user->isExecutantPrescription() && !CModule::getCanDo("dPprescription")->admin){
	$count = array();
	
  $pack_protocole = explode("-", $pack_protocole_id);
  $pack_id = ($pack_protocole[0] === "pack") ? $pack_protocole[1] : "";
  $protocole_id = ($pack_protocole[0] === "prot") ? $pack_protocole[1] : "";

  if($pack_id){
  	$_pack = new CPrescriptionProtocolePack();
		$_pack->load($pack_id);
		$_pack->countElementsByChapitre();
    $count = $_pack->_counts_by_chapitre;
	}
	if($protocole_id){
		$_prot = new CPrescription();
		$_prot->load($protocole_id);
	  $_prot->countLinesMedsElements();
	  foreach($_prot->_counts_by_chapitre as $chapitre => $_count_chapitre){
	    if(!$_count_chapitre){
	      unset($_prot->_counts_by_chapitre[$chapitre]);
	    }
	  }
		$count = $_prot->_counts_by_chapitre; 
	}
	
	// Parcours des chapitres non vides
	$errors = array();
	$errors_role = array();
	foreach($count as $chapitre => $count_by_chap){
		if(!CAppUI::conf("dPprescription CPrescription droits_infirmiers_$chapitre")){
			$errors[] = $chapitre;
		}
	}
	
	// Cas des prescriptions en roles propre, verification que les lignes peuvent etre prescrites
	if(!count($errors) && CAppUI::conf("dPprescription CPrescription role_propre")){
		$selected_user = new CMediusers();
		$selected_user->load($praticien_id);
		
		$is_inf = $selected_user->isInfirmiere();
		$is_as = $selected_user->isAideSoignant();
    $is_kine = $selected_user->isKine();
    
		if($protocole_id){
			$_prot->loadRefsLinesElement();
			foreach ($_prot->_ref_prescription_lines_element as $_line_element){
				if(($is_inf && !$_line_element->_ref_element_prescription->prescriptible_infirmiere) || 
				   ($is_as && !$_line_element->_ref_element_prescription->prescriptible_AS) || 
					 ($is_kine && !$_line_element->_ref_element_prescription->prescriptible_kine)){
					 $errors_role[] = $_line_element->_view;
		    }
			}
		}
		if($pack_id){
			foreach($_pack->_ref_protocole_pack_items as $_pack_item){
	      $_pack_item->loadRefPrescription();
	      $_prescription = $_pack_item->_ref_prescription; 
				$_prescription->loadRefsLinesElement();
	      foreach ($_prescription->_ref_prescription_lines_element as $_line_element){
          if(($is_inf && !$_line_element->_ref_element_prescription->prescriptible_infirmiere) || 
             ($is_as && !$_line_element->_ref_element_prescription->prescriptible_AS) || 
             ($is_kine && !$_line_element->_ref_element_prescription->prescriptible_kine)){
            $errors_role[] = $_line_element->_view;
          }
        }
	    }
		}
	}
	if(count($errors) || count($errors_role)){
		if(count($errors)){
		  CAppUI::setMsg("Impossible d'appliquer le protocole sélectionné car le compte utilisé ne permet pas de créer des lignes dans les chapitres suivants: ".join(", ", $errors), UI_MSG_ERROR);
    }
		if(count($errors_role)){
      CAppUI::setMsg("Impossible d'appliquer le protocole sélectionné car le compte utilisé ne permet pas de créer ces lignes en role propre: ".join(", ", $errors_role), UI_MSG_ERROR);
    }
		if ($prescription_id) {
		  echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id, null, null, null, null, null, null, null, '$pratSel_id', null, '$praticien_id')</script>";
		}
    echo CAppUI::getMsg();
    CApp::rip();
	}
}

// Chargement de la prescription
$prescription = new CPrescription();
if ($prescription_id) {
  $prescription->load($prescription_id);
} else {
  $operation = new COperation();
  $operation->load($operation_id);
  $prescription->object_class = 'CSejour';
  $prescription->object_id = $operation->sejour_id;
  $prescription->type = 'sejour';
  if ($msg = $prescription->store()) {
	  CAppUI::setMsg($msg, UI_MSG_ERROR);
	}
}

// On applique le protocole ou le pack
$prescription->applyPackOrProtocole($pack_protocole_id, $praticien_id, $date_sel, $time_sel, $operation_id, $protocole_sel_id);

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>if(window.selectLines){ selectLines('$prescription->_id', '$protocole_sel_id'); }</script>";
echo CAppUI::getMsg();
CApp::rip();

?>