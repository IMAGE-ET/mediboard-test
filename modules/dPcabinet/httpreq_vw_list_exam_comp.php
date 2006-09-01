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
$consult->loadRefsBack();

// Création du template
require_once( $AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("consult", $consult);

$smarty->display("exam_comp.tpl");
?>