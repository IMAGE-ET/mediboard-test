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

$last_log = $compte_rendu->loadLastLogForField("valide");

// Le document peut être verrouillé à la création
if (!$last_log->_id) {
  $last_log = $compte_rendu->loadFirstLog();
}

$mediuser = $last_log->loadRefUser()->loadRefMediuser();
$mediuser->loadRefFunction();

$smarty = new CSmartyDP("modules/mediusers");

$smarty->assign("mediuser", $mediuser);

$smarty->display("inc_vw_mediuser.tpl");