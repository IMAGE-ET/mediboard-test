<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

$selConsult = mbGetValueFromGetOrSession("selConsult", 0);

if ($selConsult) {
  $consult = new CConsultation();
  $consult->load($selConsult);
  $consult->loadRefConsultAnesth();
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefOperation();
    $consult->_ref_consult_anesth->_ref_operation->loadRefSejour();
    $consult->_ref_consult_anesth->_ref_operation->_ref_sejour->loadRefDossierMedical();
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult);

$smarty->display("inc_consult_anesth/inc_vw_sejour_anesth.tpl");

?>