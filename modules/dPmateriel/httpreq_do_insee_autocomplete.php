<?php /* $Id: httpreq_do_insee_autocomplete.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

do_connect($AppUI->cfg["baseINSEE"]);

if($codepostal = @$_POST["codepostal"]) {
  $sql = "SELECT commune, code_postal FROM communes_france" .
      "\nWHERE code_postal LIKE '$codepostal%'" .
      "\nORDER BY code_postal, commune";
}
if($ville = @$_POST["ville"]) {
  $sql = "SELECT commune, code_postal FROM communes_france" .
      "\nWHERE commune LIKE '%$ville%'" .
      "\nORDER BY code_postal, commune";
}
$result = db_loadList($sql, 30, $AppUI->cfg["baseINSEE"]);

if ($canRead) {
  // Création du template
  require_once($AppUI->getSystemClass ("smartydp"));
  $smarty = new CSmartyDP(1);
  $smarty->debugging = false;

  $smarty->assign("codepostal", $codepostal);
  $smarty->assign("ville"     , $ville);
  $smarty->assign("result"    , $result);

  $smarty->display("httpreq_do_insee_autocomplete.tpl");
}

?>