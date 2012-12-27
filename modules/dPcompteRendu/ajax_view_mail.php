<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$object_guid = CValue::get("object_guid");

$smarty = new CSmartyDP();

$smarty->assign("object_guid", $object_guid);

$smarty->display("inc_view_mail.tpl");
