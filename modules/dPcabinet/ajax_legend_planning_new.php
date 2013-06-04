<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("view_operations", CAppUI::pref("showIntervPlanning"));
$smarty->display("vw_legend_planning_new.tpl");