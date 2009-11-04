<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 7138 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Column 
$columns = array("code_postal", "commune");
$column = CValue::get("column");
if (!in_array($column, $columns)) {
  trigger_error("Column '$column' is invalid");
  return;
}

// Needle
$keyword = reset($_POST);
$needle = $column == "code_postal" ? "$keyword%" : "%$keyword%";

// Query
$select = "SELECT commune, code_postal, departement FROM communes_france";
$where = "WHERE $column LIKE '$needle'";
$order = "ORDER BY code_postal, commune";
$query = "$select $where $order";

$ds = CSQLDataSource::get("INSEE");
$max = CValue::get("max", 30);
$matches = $ds->loadList($query, $max);

// Template
$smarty = new CSmartyDP();

$smarty->assign("keyword", $keyword);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);
	
$smarty->display("autocomplete_cp_commune.tpl");