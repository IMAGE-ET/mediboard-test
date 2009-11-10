<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$can->needsRead();

$praticien_sortie_id = CValue::get("praticien_sortie_id");
$print = CValue::get("print", 0);
$linesDMI = array();

// Chargement de l'etablissement
$etablissement = CGroups::loadCurrent();

$prescription_id = CValue::getOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();

// Chargement du poids du patient
$poids = "-";
if($prescription->object_id){
	$patient =& $prescription->_ref_patient;
	$patient->loadRefConstantesMedicales();
	$constantes_medicales = $patient->_ref_constantes_medicales;
	$poids = $constantes_medicales->poids;
}

// Chargement du praticien
$praticien = new CMediusers();
if($praticien_sortie_id){
  $praticien->load($praticien_sortie_id);	
}

// si le user courant est un praticien, on affiche ces lignes
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if($mediuser->isPraticien()){
	$praticien = $mediuser;
}

if($prescription->type == "externe"){
  $consultation =& $prescription->_ref_object;
  $consultation->loadRefPlageConsult();
  $praticien->load($consultation->_praticien_id);
}

if($praticien->_id){
  $praticien->loadRefsFwd();
}

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement de la liste des executants
$executant = new CExecutantPrescriptionLine();
$_executants = $executant->loadList();
foreach($_executants as $_executant){
  $executants[$_executant->_guid] = $_executant;
}

$mediuser = new CMediusers();
$mediusers = $mediuser->loadList();
foreach($mediusers as $_mediuser){
  $executants[$_mediuser->_guid] = $_mediuser;
}

// Chargement des lignes de prescriptions
$prescription->loadRefsLinesMedComments("1", "debut ASC");
$prescription->loadRefsLinesElementsComments("1","","debut ASC");

// Chargement des perfusions
$prescription->loadRefsPerfusions();
foreach($prescription->_ref_perfusions as $curr_perfusion){
  $curr_perfusion->loadRefsLines();
}

if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
  $prescription->loadRefsLinesDMI();
}

$medicament = 0;
$comment = 0;

// Parcours des medicaments, pas de gestion d'executant pour les medicaments
$lines["medicaments"]["med"]["ald"] = array();
$lines["medicaments"]["med"]["no_ald"] = array();
$lines["medicaments"]["comment"]["ald"] = array();
$lines["medicaments"]["comment"]["no_ald"] = array();
foreach($prescription->_ref_lines_med_comments as $key => $lines_medicament_type){
	foreach($lines_medicament_type as $line_medicament){
	  $line_medicament->loadRefsFwd();
	  if(!$prescription->object_id){
	    if($line_medicament->_class_name == "CPrescriptionLineMedicament"){
		    $line_medicament->loadRefsSubstitutionLines();
		    foreach($line_medicament->_ref_substitution_lines as $_subst_by_chap){
		      foreach($_subst_by_chap as $_subst_line){
		        if($_subst_line->_class_name == "CPrescriptionLineMedicament"){
						  $_subst_line->loadRefsPrises();
					  } else {
					  	$_subst_line->loadRefsLines();
					  }
		        $_subst_line->loadRefsFwd();
		      }
		    }
	    }
	  }
		if($praticien->_id && $line_medicament->praticien_id != $praticien->_id){
			continue;
		}	
		if($line_medicament->child_id){
			continue;
		}
		if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$line_medicament->signee && $prescription->object_id){
		  continue;
		}
	  if($line_medicament->ald){
	    $lines["medicaments"][$key]["ald"][] = $line_medicament;
	  } else {
	  	$lines["medicaments"][$key]["no_ald"][] = $line_medicament;
	  }
	}
}

// Parcours des perfusions
foreach($prescription->_ref_perfusions as $_perfusion){
  $_perfusion->loadRefPraticien();
  $_perfusion->loadRefsSubstitutionLines();
  foreach($_perfusion->_ref_substitution_lines as $_subst_by_chap){
    foreach($_subst_by_chap as &$_subst_perf){
      $_subst_perf->loadRefPraticien();
			if($_subst_perf->_class_name == "CPerfusion"){
        $_subst_perf->loadRefsLines();
		  } else {
		  	$_subst_perf->loadRefsPrises();
		  }
    }
  }	   
  if($praticien->_id && $_perfusion->praticien_id != $praticien->_id){
		continue;
	}	
	if($_perfusion->next_perf_id){
		continue;
	}
  if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$_perfusion->signature_prat && $prescription->object_id){
	  continue;
  }
  $lines["medicaments"]["med"]["no_ald"][] = $_perfusion;
}


$linesElt = array();

// Initialisation du tableau
if(count($prescription->_ref_lines_elements_comments)){
	foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
		foreach($chap_element as $name_cat => $cat_element){
			foreach($cat_element as $type => $elements){
				foreach($elements as $element){
					if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$element->signee && $prescription->object_id){
					  continue;
				  }
				  if($element->_class_name == "CPrescriptionLineElement"){
					  $element->loadCompleteView();
					}
					$executant = "aucun";
			    if($element->_ref_executant){
			      $executant = $element->_ref_executant->_guid;
			    }
			    
		      $linesElt[$name_chap][$executant]["ald"] = array();
			    $linesElt[$name_chap][$executant]["no_ald"] = array();	
				}
			}
		}
	}
}

// Parcours des elements
if(count($prescription->_ref_lines_elements_comments)){
	foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
		foreach($chap_element as $name_cat => $cat_element){
			foreach($cat_element as $type => $elements){
				foreach($elements as $element){
			  	if($praticien->_id && $element->praticien_id != $praticien->_id){
				    continue;
			    }	
					if($element->child_id){
						continue;
					}
					if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$element->signee && $prescription->object_id){
					  continue;
				  }
				  $element->loadRefsFwd();
				  $executant = "aucun";
			    if($element->_ref_executant){
			      $executant = $element->_ref_executant->_guid;
			    }
			    if($element->ald){
			    	$linesElt[$name_chap][$executant]["ald"][$name_cat][] = $element;
			    } else {
			      $linesElt[$name_chap][$executant]["no_ald"][$name_cat][] = $element;
			    }
			  }
			}
	  }
	}
}

if(count($prescription->_ref_lines_dmi)){
  foreach($prescription->_ref_lines_dmi as $_line_dmi){
    $_line_dmi->loadRefsFwd();
    $linesDMI[$_line_dmi->_id] = $_line_dmi;
  }
}

// Tableau de traductions
$traduction = array("E" => "l'entrée", "I" => "I", "S" => "la sortie");

// Affectation du praticien selectionné
$prescription->_ref_selected_prat = $praticien;

// Chargement du header
$template_header = new CTemplateManager();
$prescription->fillTemplate($template_header);
$header = CPrescription::getPrescriptionTemplate("header", $praticien);
if($header->_id){
  $template_header->renderDocument($header->source);
}

// Chargement du footer
$template_footer = new CTemplateManager();
$prescription->fillTemplate($template_footer);
$footer = CPrescription::getPrescriptionTemplate("footer", $praticien);
if($footer->_id){
  $template_footer->renderDocument($footer->source);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("traduction"     , $traduction);
$smarty->assign("print"          , $print);
$smarty->assign("praticien"      , $praticien);
$smarty->assign("function"       , $praticien->_id ? $praticien->_ref_function : new CFunctions());
$smarty->assign("date"           , mbDate());
$smarty->assign("etablissement"  , $etablissement);
$smarty->assign("prescription"   , $prescription);
$smarty->assign("lines"          , $lines);
$smarty->assign("linesElt"       , $linesElt);
$smarty->assign("linesDMI"       , $linesDMI);
$smarty->assign("categories"     , $categories);
$smarty->assign("executants"     , $executants);
$smarty->assign("poids"          , $poids);
$smarty->assign("generated_header", $header->_id ? $template_header->document : "");
$smarty->assign("generated_footer", $footer->_id ? $template_footer->document : "");
$smarty->display("print_prescription.tpl");

?>