<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();

// Définition des variables
$consultation_id = CValue::get("consultation_id", 0);

$consult = new CConsultation();
$consult->load($consultation_id);
$consult->loadRefConsultAnesth();

$consult->loadLogs();

foreach ($consult->_refs_dossiers_anesth as $_dossier_anesth) {
  $_dossier_anesth->loadLogs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult", $consult);

$smarty->display("vw_history.tpl");
