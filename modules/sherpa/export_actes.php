<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can;

$can->needsRead();

// Filter sur les dossiers
$filter = new CSejour();
$filter->_num_dossier = mbGetValueFromGet("_num_dossier");
$filter->_date_sortie = !$filter->_num_dossier ? mbGetValueFromGet("_date_sortie", mbDate()) : null;

// Chargement des séjours concernés
$sejour = new CSejour();
$sejours = array();
if ($do = mbGetValueFromGet("do")) {
	if ($filter->_num_dossier) {
	  $sejour->loadFromNumDossier($filter->_num_dossier);
	  if ($sejour->_id) {
	    $sejours[$sejour->_id] = $sejour;
	  }
	}
	else {
		$where = array();
		$where["type"] = "NOT IN ('exte')";
		$where["sortie_reelle"] = "LIKE '$filter->_date_sortie%'";
	  $order = "entree_reelle, sortie_reelle";
	  $sejours = $sejour->loadList($where, $order);
	}
}

foreach ($sejours as &$sejour) {
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  
  // Suppression des actes
  $sejour->loadNumDossier();
  if ($sejour->_num_dossier == "-") {
    break;
  }
  
  // Suppression des anciens détails CCAM
  CSpActesExporter::deleteForDossier($sejour);
      
  // Actes du séjour
  $sejour->loadRefsActes();
  CSpActesExporter::exportEntCCAM($sejour);
  CSpActesExporter::exportDetsCIM($sejour, "0");
  
  // Opérations
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->_ref_sejour =& $sejour;
    $operation->loadRefChir();
    $operation->loadRefsActes();
    CSpActesExporter::exportEntCCAM($operation);
    CSpActesExporter::exportInfoCIM($operation, "anapath");
    CSpActesExporter::exportInfoCIM($operation, "labo");

    // Association d'un id400
    $idOperation = CSpObjectHandler::getId400For($operation);
    if (!$idOperation->_id) {
      $idOperation->id400 = $operation->_idinterv;
      $idOperation->last_update = mbDateTime();
      if ($msg = $idOperation->store()) {
        trigger_error("Impossible de créer un idenfiant externe pour l'opération: $msg", E_USER_WARNING);
        break;
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("do", $do);
$smarty->assign("filter", $filter);
$smarty->assign("acte_ccam", new CActeCCAM());
$smarty->assign("sejours", $sejours);
$smarty->assign("delDetCIM" , CSpActesExporter::$delDetCIM );
$smarty->assign("delActNGAP", CSpActesExporter::$delActNGAP);
$smarty->assign("delDetCCAM", CSpActesExporter::$delDetCCAM);
$smarty->assign("delEntCCAM", CSpActesExporter::$delEntCCAM);
$smarty->assign("detCIM" , CSpActesExporter::$detCIM);
$smarty->assign("detCCAM", CSpActesExporter::$detCCAM);
$smarty->assign("actNGAP", CSpActesExporter::$actNGAP);
$smarty->assign("entCCAM", CSpActesExporter::$entCCAM);

$smarty->display("export_actes.tpl");
?>