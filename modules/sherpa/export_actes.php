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

$exports = array();

// Associations entre actes Mediboard et actes Sherpa
function exportActe(&$acte_ccam) {
  global $exports;
  
  $acte_ccam->loadRefExecutant();
  $spDetCCAM = new CSpDetCCAM();
  $exports[$acte_ccam->_id] = null;
}

foreach ($sejours as &$sejour) {
  $sejour->loadNumDossier();
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
	    exportActe($acte_ccam);
	  }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("acte_ccam", new CActeCCAM());
$smarty->assign("sejours", $sejours);
$smarty->assign("exports", $exports);

$smarty->display("export_actes.tpl");
?>