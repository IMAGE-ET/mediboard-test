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

// Associations entre actes CCAM Mediboard et les détails CCAM Sherpa
global $detCCAM; $detCCAM = array();
function exportDetCCAM(CActeCCAM &$acte_ccam, $idinterv) {
  global $detCCAM;
  
  $spDetCCAM = new CSpDetCCAM();
  $spDetCCAM->makeId();
  $spDetCCAM->idinterv = $idinterv;
  $spDetCCAM->mapFrom($acte_ccam);
  $spDetCCAM->getCurrentDataSource();
  $detCCAM[$acte_ccam->_id] = $spDetCCAM->store();
}

// Associations diagnostics CIM Mediboard et les détails CIM Sherpa
global $detCIM; $detCIM = array();
function exportDetsCIM(CCodable &$codable) {
  global $detCIM;
  
  $spDetCIM = new CSpDetCIM();
  $spDetCIM->makeId();
  $spDetCIM->mapFrom($codable);
  $spDetCIM->idinterv = $codable->_idinterv;
  $spDetCIM->getCurrentDataSource();

  $detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
  
  $sejour =& $codable->_ref_sejour;

  // Diagnostic relié
  if ($sejour->DR) {
    $spDetCIM->makeId();
    $spDetCIM->typdia = "R";
    $spDetCIM->coddia = CSpObject::makeString($sejour->DR);
    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
    $detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
  }
  
  // Diagnostics associés
  $sejour->loadRefDossierMedical();
  if ($sejour->_ref_dossier_medical->_codes_cim) {
	  foreach ($sejour->_ref_dossier_medical->_codes_cim as $code_cim) {
	    $spDetCIM->makeId();
	    $spDetCIM->typdia = "S";
	    $spDetCIM->coddia = CSpObject::makeString($code_cim);
	    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
	    $detCIM[$codable->_class_name][$codable->_id][] = $spDetCIM->store();
	  }
  }
}

function exportInfoCIM(COperation &$operation, $key) {
  if (!$operation->$key) {
    return;
  }
  
  global $detCIM;
  
  $spDetCIM = new CSpDetCIM();
  $spDetCIM->makeId();
  $spDetCIM->mapFrom($operation);
  $spDetCIM->idinterv = $operation->_idinterv;
  $spDetCIM->getCurrentDataSource();
  $spDetCIM->typdia = "S";
  $spDetCIM->coddia = CSpObject::makeString($key);

  $detCIM[$operation->_class_name][$operation->_id][] = $spDetCIM->store();
}


global $entCCAM; $entCCAM = array();

/**
 * Associations entre codable Mediboard et les entêtes CCAM Sherpa
 */
function exportEntCCAM(CCodable &$codable) {
  global $entCCAM;

  
  $spEntCCAM = new CSpEntCCAM();
  $spEntCCAM->makeId($codable);
  $spEntCCAM->mapFrom($codable);
  $spEntCCAM->getCurrentDataSource();
  $entCCAM[$codable->_class_name][$codable->_id] = $spEntCCAM->store();
  
  foreach ($codable->_ref_actes_ccam as &$acte_ccam) {
    exportDetCCAM($acte_ccam, $spEntCCAM->_id);
  }
  
  $codable->_idinterv = $spEntCCAM->_id;
}

$delDetCCAM = array();
$delDetCIM  = array();
$delEntCCAM = array();

foreach ($sejours as &$sejour) {
  // Suppression des actes
  $sejour->loadNumDossier();
  
  // Suppression des anciens détails CCAM
  $spDetCCAM = new CSpDetCCAM();
  $delDetCCAM[$sejour->_id] = $spDetCCAM->deleteForDossier($sejour->_num_dossier);
  
  // Suppression des anciens détails CIM
  $spDetCIM = new CSpDetCIM();
  $delDetCIM[$sejour->_id] = $spDetCIM->deleteForDossier($sejour->_num_dossier);
  
  // Suppression des anciens entêtes
  $spEntCCAM = new CSpEntCCAM();
  $delEntCCAM[$sejour->_id] = $spEntCCAM->deleteForDossier($sejour->_num_dossier);
    
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  
  // Actes du séjour
  $sejour->loadRefsActes();
  exportEntCCAM($sejour);
  exportDetsCIM($sejour, "0");
  
  // Opérations
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->_ref_sejour =& $sejour;
    $operation->loadRefChir();
    $operation->loadRefsActes();
    exportEntCCAM($operation);
    exportInfoCIM($operation, "anapath");
    exportInfoCIM($operation, "labo");

    // Association d'un id400
    $idOperation = CSpObjectHandler::getId400For($operation);
    if (!$idOperation->_id) {
      $idOperation->id400 = $$operation->_idinterv;
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
$smarty->assign("delDetCIM" , $delDetCIM );
$smarty->assign("delDetCCAM", $delDetCCAM);
$smarty->assign("delEntCCAM", $delEntCCAM);
$smarty->assign("detCIM" , $detCIM);
$smarty->assign("detCCAM", $detCCAM);
$smarty->assign("entCCAM", $entCCAM);

$smarty->display("export_actes.tpl");
?>