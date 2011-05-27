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
$consult->loadRefsBack();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult", $consult);

$smarty->display("exam_comp.tpl");
?>