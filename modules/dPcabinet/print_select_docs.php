<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
$consult->load($consultation_id);
$can->edit &= $consult->canEdit();

$can->needsEdit();
$can->needsObject($consult);

$consult->loadRefsDocs();  

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"   , $consult);
$smarty->assign("documents" , $consult->_ref_documents);

$smarty->display("print_select_docs.tpl");
?>
