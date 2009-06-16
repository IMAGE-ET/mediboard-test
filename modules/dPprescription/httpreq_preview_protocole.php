<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can, $m;

$protocole_id = mbGetValueFromGet("protocole_id");
$pack_id      = mbGetValueFromGet("pack_id");
$_entree      = mbGetValueFromGet("_entree");
$_sortie      = mbGetValueFromGet("_sortie");
$_datetime    = mbGetValueFromGet("_datetime");

// Chargement des categories
$categories = CCategoryPrescription::loadCategoriesByChap();

$config_service = new CConfigService();
$configs = $config_service->getConfigForService();

$matin = range($configs["Borne matin min"], $configs["Borne matin max"]);
$soir = range($configs["Borne soir min"], $configs["Borne soir max"]);
$nuit_soir = range($configs["Borne nuit min"], 23);
$nuit_matin = range(00, $configs["Borne nuit max"]);

// Creation du tableau de dates
$tabHours = array();


foreach($matin as &$_hour_matin){
  $_hour_matin = str_pad($_hour_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($soir as &$_soir_matin){
  $_soir_matin = str_pad($_soir_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($nuit_soir as &$_hour_nuit_soir){
  $nuit[] = str_pad($_hour_nuit_soir, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_matin as &$_hour_nuit_matin){
  $nuit[] = str_pad($_hour_nuit_matin, 2, "0", STR_PAD_LEFT);
}


$dates = array();
if($_entree && $_sortie && $_datetime){
	$date = mbDate($_entree);
	while($date < $_sortie){
	  $dates[mbDate($date)] = array("matin" => $matin, "soir" => $soir, "nuit" => $nuit);
	  $date = mbDate("+ 1 DAY", $date);
	}
}
$prescription = new CPrescription();


$composition_dossier = array();
foreach($dates as $curr_date => $_date){
  foreach($_date as $moment_journee => $_hours){
    $composition_dossier[] = "$curr_date-$moment_journee";
    foreach($_hours as $_hour){
      $date_reelle = $curr_date;
      if($moment_journee == "nuit" && $_hour < "12:00:00"){
        $date_reelle = mbDate("+ 1 DAY", $curr_date);
      }
      $_dates[$date_reelle] = $date_reelle;
      $tabHours[$curr_date][$moment_journee][$date_reelle]["$_hour:00:00"] = $_hour;
    }
  }
}

if($_entree && $_sortie && $_datetime){
	if($protocole_id){  
	  // Chargement de la prescription
	  $prescription = $prescription->applyProtocole($protocole_id, null, null, null, $_entree, $_sortie, $_datetime);
	  
	  foreach($prescription->_ref_prescription_lines as &$line){
	    $line->loadRefPraticien();
	    $line->updateFormFields();
	  }
	  foreach($prescription->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
		  foreach($elements_chap as $name_cat => $elements_cat){
		    foreach($elements_cat as &$_elements){
		 	    foreach($_elements as &$_line_element){
		 	      $_line_element->updateFormFields();  
		 	      $_line_element->loadRefPraticien();
		 	    }
		    }
		  }
	  }
	  foreach($prescription->_ref_perfusions as &$_perfusion){
	    $_perfusion->loadRefPraticien();
	    $_perfusion->updateFormFields();
	  }
	}

	if($pack_id){
	  $prescriptions = array();  
	  $prescription = new CPrescription();
	  $prescription_globale = new CPrescription();
	  $pack = new CPrescriptionProtocolePack();
	  $pack->load($pack_id);
	  $pack->loadRefsPackItems();
	  
	  // On applique le protocole pour chaque protocole du pack
	  foreach($pack->_ref_protocole_pack_items as $_pack_item){
	    $_pack_item->loadRefPrescription();
	    $_prescription =& $_pack_item->_ref_prescription;
	    $prescriptions[$_prescription->_id] = $prescription->applyProtocole($_prescription->_id, null, null, null, $_entree, $_sortie, $_datetime);
	  }
	  
	  // Reconstrution de la prescription globale
	  foreach($prescriptions as $curr_prescription){
		  foreach($curr_prescription->_ref_prescription_lines as &$line){
		    $line->updateFormFields();
		    $prescription_globale->_ref_prescription_lines[$line->_id] = $line;
		  }
		  foreach($curr_prescription->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
			  foreach($elements_chap as $name_cat => $elements_cat){
			    foreach($elements_cat as $type => $_elements){
			 	    foreach($_elements as &$_line_element){
			 	      $_line_element->updateFormFields();  
			 	      $prescription_globale->_ref_prescription_lines_element_by_cat[$name_chap][$name_cat][$type][$_line_element->_id] = $_line_element;
			 	    }
			    }
			  }
		  } 
	  }
	  $prescription =& $prescription_globale;
	}
	
  $types = array("med", "elt");
  foreach($types as $type){
    $prescription->_lines[$type] = array();
    $prescription->_intitule_prise[$type] = array();
  }
  // Calcul du plan de soin 
  foreach($dates as $_date => $_hours){
    $prescription->calculPlanSoin($_date, 1);
  }  
}



// Remplissage des filter fields
$operation = new COperation();
$operation->_datetime = $_datetime;

$sejour = new CSejour();
$sejour->_entree = $_entree;
$sejour->_sortie = $_sortie;


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("dates", $dates);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("operation", $operation);
$smarty->assign("sejour", $sejour);
$smarty->assign("last_log", new CUserLog());
$smarty->assign("pharmacien", new CMediusers());
$smarty->assign("categories", $categories);
$smarty->assign("patient", new CPatient());
$smarty->assign("pack_id", $pack_id);
$smarty->assign("protocole_id", $protocole_id);
$smarty->assign("matin", $matin);
$smarty->assign("soir", $soir);
$smarty->assign("nuit", $nuit);
$smarty->assign("chapitre", "");
$smarty->display("inc_preview_protocole.tpl");

?>