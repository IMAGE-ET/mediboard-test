<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage oscour
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license OXPL
 */

global $can;

$can->needsAdmin();

$debut_selection = CValue::get("debut_selection");
$fin_selection   = CValue::get("fin_selection");

if (!$debut_selection || !$fin_selection) {
  $fin_selection   = mbDateTime();
  $debut_selection = mbDateTime("-7 DAY", $fin_selection);
}

$extractPassages = new CExtractPassages();
$extractPassages->date_extract    = mbDateTime();
$extractPassages->debut_selection = $debut_selection;
$extractPassages->fin_selection   = $fin_selection;
$extractPassages->store();

$doc_valid = null;

$where = array();
$where['sejour.type'] = " = 'urg'";
$where['sejour.annule'] = " = '0'";
$where['sejour.entree_reelle'] = " BETWEEN '".$debut_selection."' AND '".$fin_selection."' "; 

$leftjoin = array();
$leftjoin['sejour'] = 'sejour.sejour_id = rpu.sejour_id';

$order = "entree_reelle ASC";

$rpu = new CRPU();
$rpus = $rpu->loadList($where, $order, null, null, $leftjoin);

if (count($rpus) == 0) {
  CAppUI::stepAjax("Aucun RPU à extraire.", UI_MSG_ERROR);
}

foreach ($rpus as $_rpu) {
  $sejour = $_rpu->_ref_sejour;
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadDiagnosticsAssocies(false);
  $sejour->loadRefsConsultations();
}

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
$extractPassages = $rpuSender->extract($extractPassages, $rpus);

CAppUI::stepAjax("Extraction de ".count($rpus)." RPUs du ".mbDateToLocale($debut_selection)." au ".mbDateToLocale($fin_selection)." terminée.", UI_MSG_OK);
if (!$extractPassages->message_valide)
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
else 
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);

foreach ($rpus as $_rpu) {
  $rpu_passage = new CRPUPassage();
  $rpu_passage->rpu_id = $_rpu->_id;
  $rpu_passage->extract_passages_id = $extractPassages->_id;
  $rpu_passage->store();
}

echo "<script type='text/javascript'>extract_passages_id = $extractPassages->_id;</script>"
 
?>