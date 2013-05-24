<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$table_number = CValue::getOrSession("table_number");

$table_entry         = new CHL7v2TableEntry();
$table_entry->number = $table_number;
$table_entries       = $table_entry->loadMatchingList("user");
$table_entry->user   = 1;

$table_description         = new CHL7v2TableDescription();
$table_description->number = $table_number;
$table_description->loadMatchingObject();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("table_entry"      , $table_entry);
$smarty->assign("table_description", $table_description);
$smarty->assign("table_entries"    , $table_entries);
$smarty->display("inc_hl7v2_table_entries.tpl");

