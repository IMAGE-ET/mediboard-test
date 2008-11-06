<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can;

$ds = CSQLDataSource::get("INSEE");
$query = null;

if ($cp = @$_GET[$_GET["fieldcp"]]) {
  $query = "SELECT commune, code_postal FROM communes_france
				    WHERE code_postal LIKE '$cp%'
				    ORDER BY code_postal, commune";
}

if ($ville = @$_GET[$_GET["fieldcity"]]) {
  $query = "SELECT commune, code_postal FROM communes_france
				    WHERE commune LIKE '%$ville%'
				    ORDER BY code_postal, commune";
}

if ($can->read && $query) {
	$result = $ds->loadList($query, 30);
	
	// Création du template
	$smarty = new CSmartyDP();
	
	$smarty->assign("cp"    , $cp);
	$smarty->assign("ville" , $ville);
	$smarty->assign("result", $result);
	$smarty->assign("nodebug", true);
	
	$smarty->display("httpreq_do_insee_autocomplete.tpl");
}