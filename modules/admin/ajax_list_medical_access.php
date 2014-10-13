<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$object = CMbObject::loadFromGuid(CValue::get("guid"));
$page = CValue::get("page", 0);
$step = 20;

$total = CLogAccessMedicalData::countListForSejour($object);
$list = CLogAccessMedicalData::loadListForSejour($object, $page, $step);
foreach ($list as $_list) {
  $_list->loadRefUser()->loadRefFunction();
}

$smarty = new CSmartyDP();
$smarty->assign("list", $list);
$smarty->assign("page", $page);
$smarty->assign("step", $step);
$smarty->assign("total", $total);
$smarty->display("inc_list_medical_access.tpl");