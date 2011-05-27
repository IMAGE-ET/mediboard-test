<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkEdit();

$selConsult  = CValue::getOrSession("selConsult", 0);

$consult = new CConsultation();
$consult->load($selConsult);
$consult->loadRefConsultAnesth();
$consult->_ref_consult_anesth->loadRefsBack();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult_anesth", $consult->_ref_consult_anesth);

$smarty->display("inc_consult_anesth/techniques_comp.tpl");
?>