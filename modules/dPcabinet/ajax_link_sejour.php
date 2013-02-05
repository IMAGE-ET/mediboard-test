<?php 

/**
 * Relier une consutation à un séjour
 *  
 * @category dPcabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);

$group_id = CGroups::loadCurrent()->_id;

$sejour = new CSejour();

$where = array();
$where[] = "'$consult->_date' BETWEEN DATE(entree) AND DATE(sortie)";
$where["sejour.type"] = "!= 'consult'";
$where["sejour.group_id"] = "= '$group_id'";
$where["sejour.patient_id"] = "= '$consult->patient_id'";

$sejours = $sejour->loadList($where);

CMbObject::massLoadFwdRef($sejours, "praticien_id");

foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien()->loadRefFunction();
}

$smarty = new CSmartyDP();

$smarty->assign("sejours", $sejours);

$smarty->display("inc_link_sejour.tpl");