<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Column 
$columns = array("code_postal", "commune");
$column = CValue::get("column");
if (!in_array($column, $columns)) {
  trigger_error("Column '$column' is invalid");
  return;
}

// Parameters
$ds      = CSQLDataSource::get("INSEE");
$max     = CValue::get("max", 30);
$nbPays  = 0;
$matches = array();

// Needle
$name =  CValue::get("name_input");
$keyword = CValue::post($name);
$needle  = $column == "code_postal" ? "$keyword%" : "%$keyword%";

// Query
$where       = "WHERE $column LIKE '$needle'";

// France
if (CAppUI::conf("dPpatients INSEE france")) {
  $nbPays++;
  $queryFrance = "SELECT commune, code_postal, departement, 'France' AS pays FROM communes_france $where";
}

// Suisse
if (CAppUI::conf("dPpatients INSEE suisse")) {
  $nbPays++;
  $querySuisse = "SELECT commune, code_postal, '' AS departement, 'Suisse' AS pays FROM communes_suisse $where";
}

if (CAppUI::conf("dPpatients INSEE france")) {
  $france = $ds->loadList($queryFrance, intval($max/$nbPays));
  $matches = array_merge($matches, $france); 
}
if (CAppUI::conf("dPpatients INSEE suisse")) {
  $suisse =  $ds->loadList($querySuisse, intval($max/$nbPays));
  $matches = array_merge($matches, $suisse);
}

array_multisort(CMbArray::pluck($matches, "code_postal"), SORT_ASC, CMbArray::pluck($matches, "commune"), SORT_ASC, $matches);

foreach ($matches as $key => $_match) {
  $matches[$key]["commune"] = CMbString::capitalize(CMbString::lower($matches[$key]["commune"]));
  $matches[$key]["departement"] = CMbString::capitalize(CMbString::lower($matches[$key]["departement"]));
  $matches[$key]["pays"] = CMbString::capitalize(CMbString::lower($matches[$key]["pays"]));
}

// Template
$smarty = new CSmartyDP();

$smarty->assign("keyword", $keyword);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->display("autocomplete_cp_commune.tpl");