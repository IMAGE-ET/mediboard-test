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

/** @var CCompteRendu $compte_rendu */
$compte_rendu = CMbObject::loadFromGuid($object_guid);
$compte_rendu->isAutoLock();
$compte_rendu->loadRefLocker()->loadRefFunction();


$smarty = new CSmartyDP();

$smarty->assign("compte_rendu", $compte_rendu);

$smarty->display("inc_show_locker.tpl");