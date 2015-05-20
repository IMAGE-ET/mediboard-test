<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

CApp::setMemoryLimit("512M");

$debut_selection = CValue::get("debut_selection");
$fin_selection   = CValue::get("fin_selection");

if (!$debut_selection || !$fin_selection) {
  $fin_selection   = CMbDT::date()." ".CAppUI::conf("oscour date_fin_selection");
  $debut_selection = CMbDT::date("-7 DAY", $fin_selection)." 00:00:00";
}

$extractPassages = new CExtractPassages();
$extractPassages->date_extract    = CMbDT::dateTime();
$extractPassages->type            = "uhcd";
$extractPassages->debut_selection = $debut_selection;
$extractPassages->fin_selection   = $fin_selection;
$extractPassages->group_id        = CGroups::loadCurrent()->_id;
$extractPassages->store();

$doc_valid = null;

$where = array();
$ljoin["rpu"]      = "sejour.sejour_id = rpu.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where[] = "sejour.entree BETWEEN '$debut_selection' AND '$fin_selection'
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$debut_selection' AND '$fin_selection' AND sejour.annule = '0')";
$where[] = CAppUI::pref("showMissingRPU") ?
  "sejour.type = 'comp' OR rpu.rpu_id IS NOT NULL" :
  "rpu.rpu_id IS NOT NULL";

$where["sejour.group_id"]      = "= '".CGroups::loadCurrent()->_id."'";
$where["sejour.UHCD"]          = "= '1'";
$where["sejour.sortie_reelle"] = "IS NULL";
$where["sejour.annule"]        = " = '0'";

if (CAppUI::conf("dPurgences create_sejour_hospit")) {
  $where["rpu.mutation_sejour_id"] = "IS NULL";
}

$order = "entree ASC";

$sejour  = new CSejour;
/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$rpus = array();
foreach ($sejours as $_sejour) {
  $_sejour->loadExtDiagnostics();
  $_sejour->loadDiagnosticsAssocies(false);
  $_sejour->loadRefsConsultations();
  $rpu = $_sejour->loadRefRPU();

  $rpus[$rpu->_id] = $rpu;
}

if (count($rpus) == 0) {
  CAppUI::stepAjax("Aucun RPU à extraire.", UI_MSG_ERROR);
}

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
if (!$rpuSender) {
  CAppUI::stepAjax("Aucun sender définit dans le module dPurgences.", UI_MSG_ERROR);
}
$extractPassages = $rpuSender->extractUHCD($extractPassages, $rpus);

CAppUI::stepAjax(
  "Extraction de ".count($rpus)." RPUs du ".CMbDT::dateToLocale($debut_selection)." au "
  .CMbDT::dateToLocale($fin_selection)." terminée.", UI_MSG_OK
);

if (!$extractPassages->message_valide) {
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
}
else {
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);
}

foreach ($rpus as $_rpu) {
  $rpu_passage = new CRPUPassage();
  $rpu_passage->rpu_id = $_rpu->_id;
  $rpu_passage->extract_passages_id = $extractPassages->_id;
  $rpu_passage->store();
}

echo "<script>RPU_Sender.extract_passages_id = $extractPassages->_id;</script>";
