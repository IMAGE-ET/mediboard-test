<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

$sejour_id   = CValue::getOrSession("sejour_id");
$show_header = CValue::getOrSession("show_header", 0);

$sejour = new CSejour();
$sejour->load($sejour_id);

$userSel = CMediusers::get();

$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadStaticCIM10($userSel->user_id);

$patient->loadRefPhotoIdentite();

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");
$smarty->assign("line", new CPrescriptionLineMedicament());
$smarty->assign("current_m", "dPcabinet");
$smarty->assign("sejour_id", $sejour->_id);
$smarty->assign("patient", $patient);
$smarty->assign("antecedent", new CAntecedent());
$smarty->assign("traitement", new CTraitement());
$smarty->assign("_is_anesth", "1");
$smarty->assign("userSel", $userSel);
$smarty->assign("today", mbDate());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("sejour", $sejour);
$smarty->assign("show_header", $show_header);
$smarty->display("inc_ant_consult.tpl");

?>