<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$userSel = CMediusers::get();

$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadStaticCIM10($userSel->user_id);

// Chargement des aides à la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);


// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");
$smarty->assign("line", new CPrescriptionLineMedicament());
$smarty->assign("current_m", "dPcabinet");
$smarty->assign("sejour_id", $sejour->_id);
$smarty->assign("patient", $patient);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);
$smarty->assign("_is_anesth", "1");
$smarty->assign("userSel", $userSel);
$smarty->assign("today", mbDate());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));

$smarty->display("inc_ant_consult.tpl");

?>