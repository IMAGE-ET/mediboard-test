<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;

$can->needsAdmin();

$show_inactive = CValue::getOrSession("inactive", 0);


// Liste des Type d'anesthésie
$type_anesth = new CTypeAnesth;
$where = array(
  "actif" =>  ($show_inactive) ? " IN ('0','1')" : " = '1' "
);
$types_anesth = $type_anesth->loadList($where, "name");
foreach ($types_anesth as &$_type_anesth) {
  $_type_anesth->countOperations();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("types_anesth", $types_anesth);
$smarty->assign("show_inactive", $show_inactive);
$smarty->display("vw_edit_typeanesth.tpl");