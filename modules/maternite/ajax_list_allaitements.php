<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id  = CValue::getOrSession("patient_id");
$object_guid = CValue::get("object_guid");

$allaitement = new CAllaitement();
$allaitement->patient_id = $patient_id;

$allaitements = $allaitement->loadMatchingList("date_debut DESC, date_fin DESC");

$smarty = new CSmartyDP();

$smarty->assign("allaitements", $allaitements);
$smarty->assign("patient_id"  , $patient_id);
$smarty->assign("object_guid" , $object_guid);

$smarty->display("inc_list_allaitements.tpl");