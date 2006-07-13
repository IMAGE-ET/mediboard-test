<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients", "patients"));

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$name      = mbGetValueFromGet("name"     , "");
$firstName = mbGetValueFromGet("firstName", "");

$list = new CPatient;
$where = array();
if($name != "" || $firstName != "") {
  $where["nom"] = "LIKE '$name%'";
  $where["prenom"] = "LIKE '$firstName%'";
} else
  $where[] = "0";
$limit = "0, 100";
$order = "patients.nom, patients.prenom";
$list = $list->loadList($where, $order, $limit);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("name"     , $name     );
$smarty->assign("firstName", $firstName);
$smarty->assign("list"     , $list     );

$smarty->display("pat_selector.tpl");

?>