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

$guid = CValue::get("guid");
$page = CValue::get("page", 0);

//smarty
$smarty = new CSmartyDP();
$smarty->assign("guid", $guid);
$smarty->assign("page", $page);
$smarty->display("vw_list_medical_access.tpl");