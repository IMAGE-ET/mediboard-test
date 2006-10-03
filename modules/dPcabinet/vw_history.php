<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;


if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Définition des variables
$consultation_id = mbGetValueFromGet("consultation_id", 0);

$consult = new CConsultation;
$consult->load($consultation_id);
$consult->loadRefConsultAnesth();

$consult->loadLogs();

if($consult->_ref_consult_anesth->consultation_anesth_id){
  $consult->_ref_consult_anesth->loadLogs();
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("consult" , $consult );

$smarty->display("vw_history.tpl");
?>