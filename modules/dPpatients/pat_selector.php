<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

$name = dPgetParam( $_GET, 'name', '' );
$firstName = dPgetParam( $_GET, 'firstName', '' );

$list = new CPatient;
$where = array();
if($name != '' || $firstName != '') {
  $where["nom"] = "LIKE '$name%'";
  $where["prenom"] = "LIKE '$firstName%'";
} else
  $where[] = "0";
$limit = "0, 100";
$order = "patients.nom, patients.prenom";
$list = $list->loadList($where, $order, $limit);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign("name", $name);
$smarty->assign("firstName", $firstName);
$smarty->assign("list", $list);

$smarty->display("pat_selector.tpl");

?>