<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision: $
* @author Sébastien Fillonneau
*/


global $AppUI, $canRead, $canEdit, $m;

do_connect($AppUI->cfg["baseINSEE"]);


if($pays = @$_GET[$_GET["fieldpays"]]) {
  $sql = "SELECT nom_fr FROM pays" .
      "\nWHERE nom_fr LIKE '$pays%'" .
      "\nORDER BY nom_fr";
}
$result = db_loadList($sql, 30, $AppUI->cfg["baseINSEE"]);

if ($canRead) {
  // Création du template
  $smarty = new CSmartyDP(1);

  $smarty->assign("pays"  , $pays);
  $smarty->assign("result", $result);

  $smarty->display("httpreq_do_pays_autocomplete.tpl");
}
?>
