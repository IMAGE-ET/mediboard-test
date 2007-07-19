<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision: $
* @author Sébastien Fillonneau
*/


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("INSEE");
$sql = null;

if($pays = @$_GET[$_GET["fieldpays"]]) {
  $sql = "SELECT nom_fr FROM pays" .
      "\nWHERE nom_fr LIKE '$pays%'" .
      "\nORDER BY nom_fr";
} 

if ($can->read && $sql) {
  $result = $ds->loadList($sql, 30, $AppUI->cfg["baseINSEE"]);
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("pays"  , $pays);
  $smarty->assign("result", $result);

  $smarty->display("httpreq_do_pays_autocomplete.tpl");
}
?>
