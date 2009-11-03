<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$consultation_id = CValue::post("consultation_id");
$nbDoc           = CValue::post("nbDoc");
$documents       = array();

// Consultation courante
$consult = new CConsultation();
$consult->load($consultation_id);
$can->edit &= $consult->canEdit();

$can->needsEdit();
$can->needsObject($consult);

$consult->loadRefsDocs();
$aKeysDocs = array_keys($consult->_ref_documents);
foreach($nbDoc as $compte_rendu_id => $nb_print){
  if($nb_print>0 && in_array($compte_rendu_id,$aKeysDocs)){
    for($i=1; $i<=$nb_print; $i++){
      $documents[] = $consult->_ref_documents[$compte_rendu_id];
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("documents", $documents);

$smarty->display("print_docs.tpl");
?>
