<?php 

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Alexis Granger
*/

$consultation_id = mbGetValueFromGetOrSession("consultation_id");

// Chargement de la consultation
$consult = new CConsultation();
$consult->load($consultation_id);
$consult->loadRefsActesNGAP();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte_ngap", new CActeNGAP);
$smarty->assign("consult"  , $consult  );

$smarty->display("inc_acte_ngap.tpl");
?>