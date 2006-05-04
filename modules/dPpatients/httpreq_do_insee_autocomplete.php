<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

do_connect($AppUI->cfg["baseINSEE"]);

if($cp = @$_POST["cp"]) {
  $sql = "SELECT commune, code_postal FROM communes_france" .
      "\nWHERE code_postal LIKE '$cp%'" .
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
  require_once( $AppUI->getSystemClass ('smartydp' ) );
  $smarty = new CSmartyDP;
  $smarty->debugging = false;

  $smarty->assign('cp', $cp);
  $smarty->assign('ville', $ville);
  $smarty->assign('result', $result);

  $smarty->display('httpreq_do_insee_autocomplete.tpl');
}