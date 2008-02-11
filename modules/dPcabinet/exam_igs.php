<?php 

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsEdit();

$consultation_id = mbGetValueFromGetOrSession("consultation_id");

// Chargement de la consultation
$consultation = new CConsultation();
$consultation->load($consultation_id);

// Chargement du patient
$consultation->loadRefPatient();

$where = array("consultation_id" => "= '$consultation_id'");
$exam_igs = new CExamIgs;
$exam_igs->loadObject($where);

if (!$exam_igs->_id) {
  $exam_igs->consultation_id = $consultation_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consultation", $consultation);
$smarty->assign("exam_igs", $exam_igs);

$smarty->display('exam_igs.tpl');

?>