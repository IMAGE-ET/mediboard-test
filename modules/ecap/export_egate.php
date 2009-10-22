<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $dPconfig;

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

$can->needsRead();

$mb_sejour_id = mbGetValueFromPost("mb_sejour_id", mbGetValueFromGetOrSession("sejour_id"));

$msgError = null;
$mbSejour = new CSejour();
$doc      = new CEGateXMLPatientStayInformation();


if ($mbSejour->load($mb_sejour_id)) {
  $mbSejour->loadRefs();
  $mbSejour->loadNumDossier();
  $mbSejour->_ref_patient->loadIPP();
  foreach($mbSejour->_ref_operations as $key => $value) {
    $mbSejour->_ref_operations[$key]->loadRefsActesCCAM();
    $mbSejour->_ref_operations[$key]->loadRefPlageOp();
    $mbSejour->_ref_operations[$key]->loadRefsConsultAnesth();
    $mbSejour->_ref_operations[$key]->_ref_consult_anesth->loadRefConsultation();
  }
  
  if (!$doc->checkSchema()) {
    return;
  }
  
  $doc->generateFromSejour($mbSejour);
  $doc_valid = $doc->schemaValidate();
}

$doc->addNameSpaces();
$doc->saveTempFile();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("doc"       , $doc);
$smarty->assign("doc_valid" , @$doc_valid);
$smarty->assign("mbSejour"  , $mbSejour);

$smarty->display("export_egate.tpl");
?>