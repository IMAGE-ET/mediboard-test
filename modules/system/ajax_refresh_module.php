<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$mod_id = CValue::get("mod_id");

$module = new CModule();
$module->load($mod_id);
$module->checkModuleFiles();

$setupclass = "CSetup$module->mod_name";
$setup = new $setupclass;
$module->compareToSetup($setup);

$smarty = new CSmartyDP();
$smarty->assign("_mb_module", $module);
$smarty->assign("installed" , true);
$smarty->assign("module_id" , $mod_id);
$smarty->display("inc_module.tpl");
