<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$offline = CValue::get("offline");

if(!$sejour_id){
	CAppUI::stepMessage(UI_MSG_WARNING, "Veuillez sélectionner un sejour pour visualiser le dossier complet");
	return;
}

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadComplete();
$sejour->loadListConstantesMedicales();
$sejour->canRead();

// Chargement du patient
$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadComplete();
$patient->loadIPP();

// Chargement des constantes medicales
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->countAntecedents();
$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

$csteByTime = array();
foreach ($sejour->_list_constantes_medicales as $_constante_medicale) {
  $csteByTime[$_constante_medicale->datetime] = array();
  foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
    $csteByTime[$_constante_medicale->datetime][$_constante] = $_constante_medicale->$_constante;
  }
}

// Chargement du dossier de soins cloturé
$prescription = new CPrescription();
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->object_id = $sejour->_id;
$prescription->loadMatchingObject();

$dossier = array();
$list_lines = array();

// Chargement des lignes
$prescription->loadRefsLinesMedComments();
$prescription->loadRefsLinesElementsComments();
$prescription->loadRefsPrescriptionLineMixes();

if($prescription->_ref_prescription_line_mixes|@count){
	foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
	  $_prescription_line_mix->loadRefsLines();
	  $_prescription_line_mix->calculQuantiteTotal();
		$_prescription_line_mix->loadRefPraticien();
	  foreach($_prescription_line_mix->_ref_lines as $_perf_line){
	    $list_lines["prescription_line_mix"][$_perf_line->_id] = $_perf_line;
	    $_perf_line->loadRefsAdministrations();
	    foreach($_perf_line->_ref_administrations as $_administration_perf){
	    	$_administration_perf->loadRefAdministrateur();
	      if(!$_administration_perf->planification){
	        $dossier[mbDate($_administration_perf->dateTime)]["prescription_line_mix"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
	      }
	    }
	  }
	}
}

// Parcours des lignes de medicament et stockage du dossier cloturé
if($prescription->_ref_prescription_lines|@count){
	foreach($prescription->_ref_prescription_lines as $_line_med){
		$_line_med->loadRefsFwd();
		$_line_med->loadRefsPrises();
	  $_line_med->loadRefProduitPrescription();
	  $_line_med->_ref_produit->loadConditionnement();
	  $list_lines["medicament"][$_line_med->_id] = $_line_med;
	  $_line_med->loadRefsAdministrations();
	  foreach($_line_med->_ref_administrations as $_administration_med){
	  	$_administration_med->loadRefAdministrateur();
	    if(!$_administration_med->planification){
	      $dossier[mbDate($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
	    }
	  }
	}
}

// Parcours des lignes d'elements
if($prescription->_ref_lines_elements_comments|@count){
	foreach($prescription->_ref_lines_elements_comments as $chap => $_lines_by_chap){
	  foreach($_lines_by_chap as $_lines_by_cat){
	    foreach($_lines_by_cat["comment"] as $_line_elt_comment){
	      $_line_elt_comment->loadRefPraticien();
			}
			foreach($_lines_by_cat["element"] as $_line_elt){
	    	$list_lines[$chap][$_line_elt->_id] = $_line_elt;
	      $_line_elt->loadRefsAdministrations();
	      foreach($_line_elt->_ref_administrations as $_administration_elt){
	      	$_administration_elt->loadRefAdministrateur();
	        if(!$_administration_elt->planification){
	          $dossier[mbDate($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
	        }
	      }
	    }
	  }
	}
}

ksort($dossier);

$praticien = new CMediusers();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour"    , $sejour);
$smarty->assign("dossier"   , $dossier);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("csteByTime", $csteByTime);
$smarty->assign("prescription", $prescription);
$smarty->assign("praticien", $praticien);
$smarty->assign("offline", $offline);
$smarty->display("print_dossier_soins.tpl");

?>