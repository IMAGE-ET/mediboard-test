<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$date = mbGetValueFromGet("date");
$prescription_id = mbGetValueFromGet("prescription_id");
$sejour = new CSejour();
$patient = new CPatient();

// Creation du tableau de dates
$dates = array(mbDate("-2 DAYS", $date),mbDate("-1 DAYS", $date),$date,mbDate("+1 DAYS", $date),mbDate("+2 DAYS", $date));

// Chargement de la prescription
$prescription = new CPrescription();
if($prescription_id){
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMed("1","1","service");
  $prescription->loadRefsLinesElementByCat("1","","service");
  $prescription->loadRefsPerfusions("1","service");
  foreach($prescription->_ref_perfusions as &$_perfusion){
    $_perfusion->loadRefsLines();
    $_perfusion->loadRefPraticien();
	  $_perfusion->_ref_praticien->loadRefFunction();
  }

  // Chargement du poids et de la chambre du patient
  $sejour =& $prescription->_ref_object;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefConstantesMedicales();

  // Calcul du plan de soin 
  foreach($dates as $_date){
    $prescription->calculPlanSoin($_date, 0, 1);
  }
}

// Calcul du rowspan pour les medicaments
$types = array("med","inj");
foreach($types as $_type_med){
  $produits = ($_type_med == "med") ? $prescription->_ref_lines_med_for_plan : $prescription->_ref_injections_for_plan;
	if($produits){
	  foreach($produits as $_code_ATC => $_cat_ATC){
		  if(!isset($prescription->_nb_produit_by_cat[$_code_ATC])){
		    $prescription->_nb_produit_by_cat[$_type_med][$_code_ATC] = 0;
		  }
		  foreach($_cat_ATC as $line_id => $_line) {
		    foreach($_line as $unite_prise => $line_med){
		      $prescription->_nb_produit_by_cat[$_type_med][$_code_ATC]++;
		    }
		  }
		}
	}
}

// Calcul du rowspan pour les elements
if($prescription->_ref_lines_elt_for_plan){
	foreach($prescription->_ref_lines_elt_for_plan as $elements_chap){
	  foreach($elements_chap as $name_cat => $elements_cat){
	    if(!isset($prescription->_nb_produit_by_cat[$name_cat])){
	      $prescription->_nb_produit_by_cat[$name_cat] = 0;
	    }
	    foreach($elements_cat as $_element){
	      foreach($_element as $element){
	        $prescription->_nb_produit_by_cat[$name_cat]++;
	      }
	    }
	  }
	}     
}

// Chargement des transmissions de la prescriptions
$transmission = new CTransmissionMedicale();
$where = array();
$where[] = "(object_class = 'CCategoryPrescription') OR 
            (object_class = 'CPrescriptionLineElement') OR 
            (object_class = 'CPrescriptionLineMedicament') OR 
            (object_class = 'CPerfusion')";

$where["sejour_id"] = " = '$sejour->_id'";
$transmissions_by_class = $transmission->loadList($where);

foreach($transmissions_by_class as $_transmission){
  $_transmission->loadRefsFwd();
	$prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
}


// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);
$smarty->assign("categories", $categories);
$smarty->assign("dates", $dates);
$smarty->assign("prescription", $prescription);
$smarty->assign("now", $date);
$smarty->assign("categorie", new CCategoryPrescription());
$smarty->display("../../dPprescription/templates/inc_vw_dossier_soin_semaine.tpl");

?>