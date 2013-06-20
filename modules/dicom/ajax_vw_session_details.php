<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$session_guid = CValue::get("session_guid");

$session = CMbObject::loadFromGuid($session_guid);

$session->loadRefActor();
$session->loadRefGroups();
$session->loadRefDicomExchange();
$session->updateFormFields();
$session->loadMessages();

$smarty = new CSmartyDP();
$smarty->assign("session", $session);
$smarty->display("inc_session_details.tpl");
