<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$consult_id = CValue::get("consult_id");
$group_id   = CGroups::loadCurrent()->_id;

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefPlageConsult();

// next consultations
$dateW = $consult->_ref_plageconsult->date;
$whereN = array();
$ljoin = array();
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
$whereN["patient_id"] = " = '$consult->patient_id'";
$whereN["plageconsult.date"] = " >= '$dateW'";
$whereN["heure"]  = " >= '$consult->heure'";
/** @var CConsultation[] $consults */
$consults = $consult->loadListWithPerms(PERM_READ, $whereN, null, null, null, $ljoin);
foreach ($consults as $_consult) {
  $_consult->loadRefPraticien()->loadRefFunction();
  $_consult->loadRefSejour();
}

// sejours
$where = array();
$where[] = "'$consult->_date' BETWEEN DATE(entree) AND DATE(sortie)";
$where["sejour.type"] = "!= 'consult'";
$where["sejour.group_id"] = "= '$group_id'";
$where["sejour.patient_id"] = "= '$consult->patient_id'";
/** @var CSejour[] $sejours */
$sejour = new CSejour();
$sejours = $sejour->loadListWithPerms(PERM_EDIT, $where);
CMbObject::massLoadFwdRef($sejours, "praticien_id");
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien()->loadRefFunction();
}


$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("consult", $consult);
$smarty->assign("next_consults", $consults);
$smarty->display("inc_link_sejour.tpl");