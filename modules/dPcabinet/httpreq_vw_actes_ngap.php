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

// Chargement de la liste des actes NGAP
$acte_ngap = new CActeNGAP();
$where = array();
$where["consultation_id"] = " = '$consultation_id'";
$listActesNGAP = $acte_ngap->loadList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult       );
$smarty->assign("acte_ngap"     , $acte_ngap     );
$smarty->assign("listActesNGAP" , $listActesNGAP );

$smarty->display("inc_acte_ngap.tpl");



?>