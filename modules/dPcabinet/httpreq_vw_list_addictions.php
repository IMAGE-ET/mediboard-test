<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$consultation_anesth_id = mbGetValueFromGetOrSession("consultation_anesth_id", 0);

$consult_anesth = new CConsultAnesth;
$consult_anesth->load($consultation_anesth_id);
$consult_anesth->loadRefsAddictions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult_anesth", $consult_anesth);

$smarty->display("inc_consult_anesth/inc_list_addiction.tpl");

?>