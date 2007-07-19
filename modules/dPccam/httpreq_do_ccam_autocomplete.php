<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");
do_connect($AppUI->cfg["baseCCAM"]);

if($codeacte = @$_POST["codeacte"]){
  $sql = "SELECT CODE, LIBELLELONG FROM actes WHERE CODE LIKE '" . addslashes($codeacte) . "%'";
}

$result = $ds->loadList($sql, null, $AppUI->cfg["baseCCAM"]);

if ($can->read) {
  // Création du template
  $smarty = new CSmartyDP();
  $smarty->debugging = false;

  $smarty->assign("codeacte"  , $codeacte);
  $smarty->assign("result"    , $result);

  $smarty->display("httpreq_do_ccam_autocomplete.tpl");
}

?>