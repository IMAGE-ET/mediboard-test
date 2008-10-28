<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can;

$can->needsRead();

$ordonnance = mbGetValueFromGet("ordonnance");
$praticien_sortie_id = mbGetValueFromGet("praticien_sortie_id");
$print = mbGetValueFromGet("print", 0);
// Mode ordonnance
if($ordonnance){
  $now = mbDateTime();
  $user_id = $AppUI->user_id;
  $time_print_ordonnance = CAppUI::conf("dPprescription CPrescription time_print_ordonnance"); 
}

// Chargement de l'etablissement
$etablissement = CGroups::loadCurrent();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();

// L'impression de la prescription par praticien n'est effectuée que dans le cadre de la prescription de sortie
if($prescription->type != "sortie"){
  $praticien_sortie_id = "";
}

// Chargement du praticien
if($praticien_sortie_id){
  $praticien = new CMediusers();
  $praticien->load($praticien_sortie_id);	
} else {
	$praticien =& $prescription->_ref_praticien;
}

$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if($mediuser->isPraticien()){
	$praticien = $mediuser;
}
$praticien->loadRefsFwd();


// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement de la liste des executants
$executant = new CExecutantPrescriptionLine();
$executants = $executant->loadList();

// Chargement des lignes de prescriptions
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();

$traitements_arretes = array();

$prescription->_ref_object->loadRefPrescriptionTraitement();
if($prescription->_ref_object->_ref_prescription_traitement->_id){
	$prescription->_ref_object->_ref_prescription_traitement->loadRefsLinesMed();
	foreach($prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines as $line_med){
		if($line_med->date_arret){
			$traitements_arretes[] = $line_med;
		}
	}
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
	  if(!$prescription->object_id){
	    $line_medicament->loadRefsSubstitutionLines();
	    foreach($line_medicament->_ref_substitution_lines as &$_subst_line){
	      $_subst_line->loadRefsPrises();
	    }
	  }
		if($praticien_sortie_id && $line_medicament->praticien_id != $praticien_sortie_id){
			continue;
		}	
		if($line_medicament->child_id){
			continue;
		}
	  // mode ordonnnance
		if($ordonnance){

			if($line_medicament->_fin_reelle && $line_medicament->_fin_reelle < mbDate()){
				continue;
			}
			
			// si ligne non signée, ou que le praticien de la ligne n'est pas le user, on ne la prend pas en compte
		  
			if(!$line_medicament->signee || $line_medicament->praticien_id != $AppUI->user_id){
		  	continue;
		  }
		  
		  $line_medicament->loadRefLogSignee();
		  // si l'heure definie a ete depassée, on ne la prend pas non plus en compte
		  if($now > mbDateTime($line_medicament->_ref_log_signee->date. "+ $time_print_ordonnance hours")){
		  	continue;
		  }
	  }
	  if($line_medicament->ald){
	    $lines["medicaments"][$key]["ald"][] = $line_medicament;
	  } else {
	  	$lines["medicaments"][$key]["no_ald"][] = $line_medicament;
	  }
	}
}


$linesElt = array();

// Initialisation du tableau
if(count($prescription->_ref_lines_elements_comments)){
foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
	foreach($chap_element as $name_cat => $cat_element){
		foreach($cat_element as $type => $elements){
			foreach($elements as $element){
				if($element->_class_name == "CPrescriptionLineElement"){
				  $element->loadCompleteView();
				}
			if($ordonnance){
				  if($element->_fin_reelle && $element->_fin_reelle < mbDate()){
				  	continue;
				  }
				  
					// si ligne non signée, ou que le praticien de la ligne n'est pas le user, on ne la prend pas en compte
				  if(!$element->signee || $element->praticien_id != $AppUI->user_id){
				  	continue;
				  }
				  // si l'heure definie a ete depassée, on ne la prend pas non plus en compte
				  if($now > mbDateTime($element->_ref_log_signee->date. "+ $time_print_ordonnance hours")){
            continue;
				  }
			  }
			  
				$executant = "aucun";
		    if($element->executant_prescription_line_id){
		      $executant = $element->executant_prescription_line_id;
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
		  	if($praticien_sortie_id && $element->praticien_id != $praticien_sortie_id){
			    continue;
		    }	
				if($element->child_id){
					continue;
				}
			  // mode ordonnnance
				if($ordonnance){
          if($element->date_arret){
				  	$element->_fin = $element->date_arret;
				  }
				  if($element->_fin && $element->_fin < mbDate()){
				  	continue;
				  }
				  
					// si ligne non signée, ou que le praticien de la ligne n'est pas le user, on ne la prend pas en compte
				  if(!$element->signee || $element->praticien_id != $AppUI->user_id){
				  	continue;
				  }
				  // si l'heure definie a ete depassée, on ne la prend pas non plus en compte
				  if($now > mbDateTime($element->_ref_log_signee->date. "+ $time_print_ordonnance hours")){
            continue;
				  }
			  }
			  
				$executant = "aucun";
		    if($element->executant_prescription_line_id){
		      $executant = $element->executant_prescription_line_id;
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


$traduction = array();
$traduction["E"] = "l'entrée";
$traduction["I"] = "I";
$traduction["S"] = "la sortie";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("traduction"          , $traduction);
$smarty->assign("print"               , $print);
$smarty->assign("praticien"           , $praticien);
$smarty->assign("traitements_arretes" , $traitements_arretes);
$smarty->assign("ordonnance"          , $ordonnance);
$smarty->assign("date"                , mbDate());
$smarty->assign("etablissement"       , $etablissement);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("lines"               , $lines);
$smarty->assign("linesElt"            , $linesElt);
$smarty->assign("categories"          , $categories);
$smarty->assign("executants"          , $executants);
$smarty->display("print_prescription.tpl");

?>