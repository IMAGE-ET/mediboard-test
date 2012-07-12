<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$anesth_id = CValue::get("anesth_id");

$mediuser = new CMediusers;
$mediuser->load($anesth_id);

if (!$mediuser->isAnesth()) {
  CAppUI::stepAjax("L'utilisateur n'est pas anesthesiste", UI_MSG_ERROR);
}

$consult = new CConsultation;
$where = array(
  "plageconsult.chir_id"    => "= '$mediuser->_id'",
  "consultation.patient_id" => "IS NOT NULL"
);

$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);

$ids_consults = $consult->loadIds($where, null, null, null, $ljoin);

$where["consultation_anesth.consultation_id"] = "IS NOT NULL";
$ljoin["consultation_anesth"]                 = "consultation_anesth.consultation_id = consultation.consultation_id";

$ids_consults = array_diff($ids_consults, $consult->loadIds($where, null, null, null, $ljoin));

if (count($ids_consults) == 0) {
  CAppUI::stepAjax("Aucune consultation sans consultation d'anesth", UI_MSG_WARNING);
  
  CAppUI::js('$V($("check_repeat_actions"), false)');
  
  return;
}

$ids_consults = array_slice($ids_consults, 0, 400);

foreach ($ids_consults as $_consult_id) {
  $consult = new CConsultation;
  $consult->load($_consult_id);
  
  if ($msg = $consult->store()) {
    CAppUI::stepAjax($msg, UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("Mise à jour de la consultation : #$consult->_id");
}

