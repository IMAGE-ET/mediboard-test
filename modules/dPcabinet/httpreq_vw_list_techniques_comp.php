<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet" , "consultation"));
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$selConsult  = mbGetValueFromGetOrSession("selConsult", 0);

$consult = new CConsultation();
$consult->load($selConsult);
$consult->loadRefConsultAnesth();
$consult->_ref_consult_anesth->loadRefsBack();

// Création du template
require_once( $AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("consult_anesth", $consult->_ref_consult_anesth);

$smarty->display("inc_consult_anesth/techniques_comp.tpl");
?>