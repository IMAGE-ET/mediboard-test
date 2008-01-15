<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());

// Chargement des séjours concernés
$where = array();
$where["type"] = "NOT IN ('exte', 'urg')";
$where["sortie_reelle"] = "LIKE '$date%'";
$order = "entree_reelle, sortie_reelle";
$sejour = new CSejour();
$sejours = $sejour->loadList($where, $order);

global $exports;
$exports = array();

// Associations entre actes Mediboard et actes Sherpa
function exportActe(&$acte_ccam) {
  global $exports, $g;
  
  $acte_ccam->loadRefExecutant();

  $spDetCCAM = new CSpDetCCAM();
  $spDetCCAM->_id = CSpObjectHandler::makeId($acte_ccam);
  $spDetCCAM->mapFrom($acte_ccam);
  $spDetCCAM->changeDSN($g);
  $exports[$acte_ccam->_id] = $spDetCCAM->store();
}

$deletions = array();

foreach ($sejours as &$sejour) {
  // Suppression des actes
  $sejour->loadNumDossier();
  $spDetCCAM = new CSpDetCCAM();
  $deletions[$sejour->_id] = $spDetCCAM->deleteForDossier($sejour->_num_dossier);
  
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  
  // Actes du séjour
  $sejour->loadRefsActes();
  foreach ($sejour->_ref_actes_ccam as &$acte_ccam) {
    exportActe($acte_ccam);
  }
  
  // Opérations
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->loadRefChir();
    $operation->loadRefsActes();
    foreach ($operation->_ref_actes_ccam as &$acte_ccam) {
      $operation->_ref_sejour =& $sejour;
	    exportActe($acte_ccam);
	  }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("acte_ccam", new CActeCCAM());
$smarty->assign("sejours", $sejours);
$smarty->assign("deletions", $deletions);
$smarty->assign("exports", $exports);

$smarty->display("export_actes.tpl");
?>