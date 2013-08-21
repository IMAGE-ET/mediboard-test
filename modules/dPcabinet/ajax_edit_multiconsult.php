<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$consult_id = CValue::get("consult_id");

$first_consult = new CConsultation();
$first_consult->load($consult_id);
$plage_consult = $first_consult->loadRefPlageConsult();
$first_consult->loadRefPatient();
$first_consult->loadRefPraticien();
$date = $plage_consult->date;

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens(PERM_EDIT);

$next_consult = new CConsultation();
$ljoin = array();
$where = array();
$where["patient_id"] = " = '$first_consult->patient_id'";
$where["date"] = " >= '$date'";
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

/** @var CConsultation[] $next_consults */
$next_consults = $next_consult->loadList($where, "date ASC", null, null, $ljoin);
foreach ($next_consults as $_key => $_consult) {
  $_consult->loadRefPraticien();

  //no rights on prat, skip the consult
  if (!$_consult->_ref_praticien->canDo()->edit) {
    unset($next_consults[$_key]);
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("consults", $next_consults);
$smarty->assign("consult", $first_consult);
$smarty->assign("praticiens", $praticiens);
$smarty->display("inc_edit_multiconsult.tpl");
