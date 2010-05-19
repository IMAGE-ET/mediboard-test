<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI;

$protocole_id = CValue::get("protocole_id");

// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);
$protocole->loadRefsLinesMed();
$protocole->loadRefsLinesElement();
$protocole->loadRefsLinesAllComments();
$protocole->loadRefsPrescriptionLineMixes();

// Creation du nouveau protocole
$protocole->_id = "";
$protocole->libelle = "Copie de $protocole->libelle";
$msg = $protocole->store();
CAppUI::displayMsg($msg, "CPrescription-msg-create");

// Parcours des medicaments
foreach($protocole->_ref_prescription_lines as $line){
	$line->loadRefsPrises();
	$line->loadRefsSubstitutionLines();
	$line->prescription_id = $protocole->_id;
	$line->_id = "";
	$msg = $line->store();
	CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
	
	// Parcours des prises
	foreach($line->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line->_id;
		$msg = $prise->store();
	  CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
	}
	
  //Parcours des lignes de substitution
  foreach($line->_ref_substitution_lines as $_lines_subst_by_type){
    foreach($_lines_subst_by_type as $_line_subst){
      $_line_subst->substitute_for_id = $line->_id;
      $_line_subst->prescription_id = $protocole->_id;	    
	    
      // Medicaments
      if($_line_subst->_class_name == "CPrescriptionLineMedicament"){
	      $_line_subst->loadRefsPrises();
	
		    $_line_subst->_id = "";
			  $msg = $_line_subst->store();
		  	CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
		  
		  	// Parcours des prises des lignes de substitutions
		    foreach($_line_subst->_ref_prises as $_prise_line_subst){
		      $_prise_line_subst->_id = "";
				  $_prise_line_subst->object_id = $_line_subst->_id;
				  $msg = $_prise_line_subst->store();
			    CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
		    }
      } 
      // Perfusions
      else {
        $_line_subst->loadRefsLines();
				$_line_subst->loadVoies();
        $_line_subst->_id = "";
			  $msg = $_line_subst->store();
			  CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-create");
        foreach($_line_subst->_ref_lines as $_perf_subst_line){
          $_perf_subst_line->_id = "";
          $_perf_subst_line->prescription_line_mix_id = $_line_subst->_id;
          $msg = $_perf_subst_line->store();
          CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");
        }
      }
    }
  }
}

// Parcours des prescription_line_mixes
foreach($protocole->_ref_prescription_line_mixes as $_prescription_line_mix){
  $_prescription_line_mix->loadRefsLines();
	$_prescription_line_mix->loadVoies();
  $_prescription_line_mix->loadRefsSubstitutionLines();
  $_prescription_line_mix->prescription_id = $protocole->_id;
  $_prescription_line_mix->_id = "";
  $msg = $_prescription_line_mix->store();
  CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-create");
  
  //Parcours des lignes de substitution
  foreach($_prescription_line_mix->_ref_substitution_lines as $_lines_subst_by_type){
    foreach($_lines_subst_by_type as $_line_subst){
      $_line_subst->substitute_for_id = $_prescription_line_mix->_id;
      $_line_subst->prescription_id = $protocole->_id;	    
	    
      // Medicaments
      if($_line_subst->_class_name == "CPrescriptionLineMedicament"){
	      $_line_subst->loadRefsPrises();
	
		    $_line_subst->_id = "";
			  $msg = $_line_subst->store();
		  	CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
		  
		  	// Parcours des prises des lignes de substitutions
		    foreach($_line_subst->_ref_prises as $_prise_line_subst){
		      $_prise_line_subst->_id = "";
				  $_prise_line_subst->object_id = $_line_subst->_id;
				  $msg = $_prise_line_subst->store();
			    CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
		    }
      } 
      // Perfusions
      else {
        $_line_subst->loadRefsLines();
				$_line_subst->loadVoies();
        $_line_subst->_id = "";
			  $msg = $_line_subst->store();
			  CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-create");
        foreach($_line_subst->_ref_lines as $_perf_subst_line){
          $_perf_subst_line->_id = "";
          $_perf_subst_line->prescription_line_mix_id = $_line_subst->_id;
          $msg = $_perf_subst_line->store();
          CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");
        }
      }
    }
  }
  
  // Parcours des lignes de prescription_line_mixes
  foreach($_prescription_line_mix->_ref_lines as $_perf_line){
    $_perf_line->_id = "";
    $_perf_line->prescription_line_mix_id = $_prescription_line_mix->_id;
    $msg = $_perf_line->store();
	  CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");
  }
}

// Parcours des elements
foreach($protocole->_ref_prescription_lines_element as $line_element){
	$line_element->loadRefsPrises();
	$line_element->prescription_id = $protocole->_id;
	$line_element->_id = "";
	$line_element->store();
	CAppUI::displayMsg($msg, "CPrescriptionLineElement-msg-create");
	
  // Parcours des prises
	foreach($line_element->_ref_prises as $prise){
	  $prise->_id = "";
		$prise->object_id = $line_element->_id;
		$msg = $prise->store();
	  CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
	}
}

// Parcours des commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$line_comment->prescription_id = $protocole->_id;
	$line_comment->_id = "";
	$line_comment->store();
	CAppUI::displayMsg($msg, "CPrescriptionLineComment-msg-create");
}


// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Protocole.edit($protocole->_id); Protocole.refreshList($protocole->_id);</script>";
echo CAppUI::getMsg();
CApp::rip();
?>