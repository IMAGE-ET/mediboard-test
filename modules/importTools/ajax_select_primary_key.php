<?php 

/**
 * $Id$
 *  
 * @category ImportTools
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$dsn    = CValue::get("dsn");
$table  = CValue::get("table");
$column = CValue::get("column");

$db_info = CImportTools::getDatabaseStructure($dsn);

$smarty = new CSmartyDP();
$smarty->assign("dsn", $dsn);
$smarty->assign("table", $table);
$smarty->assign("column", $column);
$smarty->assign("db_info", $db_info);
$smarty->display("inc_select_primary_key.tpl");