<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("mediusers"));

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefsDocs();
}  

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("documents", $consult->_ref_documents);

$smarty->display("print_docs.tpl");
?>
