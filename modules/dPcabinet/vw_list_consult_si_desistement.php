<?php /* $Id: vw_planning.php 9714 2010-08-02 09:55:34Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 9714 $
* @author Romain Ollivier
*/

CCanDo::checkRead();

$chir_id = CValue::get("chir_id");

$user = new CMediusers;
$user->load($chir_id);

// Liste des consultations a avancer si desistement
$now = mbDate();
$where = array(
  "plageconsult.date" => " > '$now'",
  "plageconsult.chir_id" => "= '$chir_id'",
  "consultation.si_desistement" => "= '1'",
);
$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);
$consultation_desist = new CConsultation;
$consultations = $consultation_desist->loadList($where, "date", null, null, $ljoin);

foreach($consultations as $_consult) {
  $_consult->loadRefPatient();
  $_consult->loadRefPlageConsult();
  $_consult->loadRefCategorie();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consultations", $consultations);
$smarty->assign("user", $user);
$smarty->display("inc_list_consult_si_desistement.tpl");
