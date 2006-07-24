<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

do_connect($AppUI->cfg["baseCCAM"]);

if($codeacte = @$_POST["codeacte"]){
  $sql = "SELECT CODE, LIBELLELONG FROM actes WHERE CODE LIKE '" . addslashes($codeacte) . "%'";
}

$result = db_loadList($sql, null, $AppUI->cfg["baseCCAM"]);

if ($canRead) {
  // Création du template
  require_once($AppUI->getSystemClass ("smartydp"));
  $smarty = new CSmartyDP(1);
  $smarty->debugging = false;

  $smarty->assign("codeacte"  , $codeacte);
  $smarty->assign("result"    , $result);

  $smarty->display("httpreq_do_ccam_autocomplete.tpl");
}

?>