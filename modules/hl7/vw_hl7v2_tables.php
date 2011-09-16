<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$page         = intval(CValue::get('page', 0));
$table_number = CValue::getOrSession("table_number", 1);

$step = 20;

$table_description = new CHL7v2TableDescription();
$total_tables      = $table_description->countList();
$tables            = $table_description->loadMatchingList("number", "$page, $step");
foreach ($tables as $_table) {
  $_table->countEntries();
}

$table_entry         = new CHL7v2TableEntry();
$table_entry->number = $table_number;
$table_entries       = $table_entry->loadMatchingList("user");
$table_entry->user   = 1;

$table_description         = new CHL7v2TableDescription();
$table_description->number = $table_number;
$table_description->loadMatchingObject();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("page"             , $page);
$smarty->assign("tables"           , $tables);
$smarty->assign("total_tables"     , $total_tables);
$smarty->assign("table_entry"      , $table_entry);
$smarty->assign("table_entries"    , $table_entries);
$smarty->assign("table_description", $table_description);
$smarty->display("vw_hl7v2_tables.tpl");

?>