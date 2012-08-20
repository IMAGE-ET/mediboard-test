<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$patient_id   = CValue::getOrSession("patient_id");
$object_guid  = CValue::getOrSession("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

$grossesse = new CGrossesse;
$grossesse->parturiente_id = $patient_id;
$grossesses = $grossesse->loadMatchingList("terme_prevu DESC, active DESC");

foreach ($grossesses as $_grossesse) {
  $_grossesse->countBackRefs("sejours");
  $_grossesse->countBackRefs("consultations");
  $_grossesse->countBackRefs("naissances");
}

$smarty = new CSmartyDP;

$smarty->assign("grossesses"  , $grossesses);
$smarty->assign("patient_id"  , $patient_id);
$smarty->assign("object"      , $object);

$smarty->display("inc_list_grossesses.tpl");

?>