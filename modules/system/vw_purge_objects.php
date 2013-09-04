<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$smarty = new CSmartyDP();

$smarty->assign("user_log", new CUserLog());
$smarty->assign("classes" , CApp::getChildClasses());

$smarty->display("vw_purge_objects.tpl");