<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("INSEE");
$sql = null;

if($cp = @$_GET[$_GET["fieldcp"]]) {
  $sql = "SELECT commune, code_postal FROM communes_france" .
      "\nWHERE code_postal LIKE '$cp%'" .
      "\nORDER BY code_postal, commune";
}
if($ville = @$_GET[$_GET["fieldcity"]]) {
  $sql = "SELECT commune, code_postal FROM communes_france" .
      "\nWHERE commune LIKE '%$ville%'" .
      "\nORDER BY code_postal, commune";
}

if ($can->read && $sql) {
	 $result = $ds->loadList($sql, 30, $AppUI->cfg["baseINSEE"]);
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("cp"    , $cp);
  $smarty->assign("ville" , $ville);
  $smarty->assign("result", $result);

  $smarty->display("httpreq_do_insee_autocomplete.tpl");
}