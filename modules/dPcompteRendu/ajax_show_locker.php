<?php 

/**
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_guid = CValue::get("object_guid");

$compte_rendu = CMbObject::loadFromGuid($object_guid);

$days = CAppUI::conf("dPcompteRendu CCompteRendu days_to_lock");
$days = isset($days[$compte_rendu->object_class]) ?
  $days[$compte_rendu->object_class] : $days["base"];

$last_log = $compte_rendu->loadLastLogForField("valide");

// Le document peut être verrouillé à la création
if (!$last_log->_id) {
  $last_log = $compte_rendu->loadFirstLog();
}

$mediuser = $last_log->loadRefUser()->loadRefMediuser();
$mediuser->loadRefFunction();

$smarty = new CSmartyDP();

$smarty->assign("compte_rendu", $compte_rendu);
$smarty->assign("last_log"    , $last_log);
$smarty->assign("mediuser"    , $mediuser);
$smarty->assign("days"        , $days);

$smarty->display("inc_show_locker.tpl");