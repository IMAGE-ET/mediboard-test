<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;
$can->needsRead();

$ds = CSQLDataSource::get("INSEE");
$query = null;

if ($cp = @$_GET[$_GET["fieldcp"]]) {
  $query = "SELECT commune, code_postal FROM communes_france" .
    "\nWHERE code_postal LIKE '$cp%'" .
    "\nORDER BY code_postal, commune";
}

if ($ville = @$_GET[$_GET["fieldcity"]]) {
  $query = "SELECT commune, code_postal FROM communes_france" .
    "\nWHERE commune LIKE '%$ville%'" .
    "\nORDER BY code_postal, commune";
}

if (!$query) {
  return;
}

$result = $ds->loadList($query, 30);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("cp"    , $cp);
$smarty->assign("ville" , $ville);
$smarty->assign("result", $result);

$smarty->display("httpreq_do_insee_autocomplete.tpl");