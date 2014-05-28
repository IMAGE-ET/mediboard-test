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

$where = array();
$where[] = "'$consult->_date' BETWEEN DATE(entree) AND DATE(sortie)";
$where["sejour.type"] = "!= 'consult'";
$where["sejour.group_id"] = "= '$group_id'";
$where["sejour.patient_id"] = "= '$consult->patient_id'";

/** @var CSejour[] $sejours */
$sejour = new CSejour();
$sejours = $sejour->loadList($where);
CMbObject::massLoadFwdRef($sejours, "praticien_id");

foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien()->loadRefFunction();
}

$smarty = new CSmartyDP();

$smarty->assign("sejours", $sejours);

$smarty->display("inc_link_sejour.tpl");