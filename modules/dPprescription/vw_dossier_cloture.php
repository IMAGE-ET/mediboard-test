<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$sejour_id = CValue::getOrSession("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->_ref_patient->loadRefConstantesMedicales();
$sejour->loadSuiviMedical();

// Chargement de la prescription de sejour
$prescription = new CPrescription();
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->object_id = $sejour->_id;
$prescription->loadMatchingObject();

$dossier = array();
$list_lines = array();

// Chargement des lignes
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElementByCat();
$prescription->loadRefsPerfusions();

foreach($prescription->_ref_perfusions as $_perfusion){
  $_perfusion->loadRefsLines();
  $_perfusion->calculQuantiteTotal();
  foreach($_perfusion->_ref_lines as $_perf_line){
    $list_lines["perfusion"][$_perf_line->_id] = $_perf_line;
    $_perf_line->loadRefsAdministrations();
    foreach($_perf_line->_ref_administrations as $_administration_perf){
      if(!$_administration_perf->planification){
        $dossier[mbDate($_administration_perf->dateTime)]["perfusion"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
      }
    }
  }
}

// Parcours des lignes de medicament et stockage du dossier cloturé
foreach($prescription->_ref_prescription_lines as $_line_med){
	$_line_med->loadRefProduitPrescription();
	$_line_med->_ref_produit->loadConditionnement();
	$list_lines["medicament"][$_line_med->_id] = $_line_med;
	$_line_med->loadRefsAdministrations();
	foreach($_line_med->_ref_administrations as $_administration_med){
	  if(!$_administration_med->planification){
		  $dossier[mbDate($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
	  }
	}
}

// Parcours des lignes d'elements
foreach($prescription->_ref_prescription_lines_element_by_cat as $chap => $_lines_by_chap){
	foreach($_lines_by_chap as $_lines_by_cat){
		foreach($_lines_by_cat["element"] as $_line_elt){
			$list_lines[$chap][$_line_elt->_id] = $_line_elt;
		  $_line_elt->loadRefsAdministrations();
		  foreach($_line_elt->_ref_administrations as $_administration_elt){
		    if(!$_administration_elt->planification){
		      $dossier[mbDate($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
		    }
		  }
		}
	}
}

ksort($dossier);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dateTime", mbDateTime());
$smarty->assign("sejour", $sejour);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("dossier", $dossier);
$smarty->display("vw_dossier_cloture.tpl");

?>