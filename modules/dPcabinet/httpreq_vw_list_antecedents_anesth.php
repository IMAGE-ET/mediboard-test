<?php /* $Id: httpreq_vw_list_antecedents.php 1476 2007-01-19 16:40:49Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1476 $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$consultation_anesth_id = mbGetValueFromGetOrSession("consultation_anesth_id", 0);

$consult_anesth = new CConsultAnesth;
$consult_anesth->load($consultation_anesth_id);
$consult_anesth->loadRefsAntecedents();
$consult_anesth->loadRefsTraitements();
$consult_anesth->loadRefsAddictions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult_anesth", $consult_anesth);

$smarty->display("inc_list_ant_anesth.tpl");

?>