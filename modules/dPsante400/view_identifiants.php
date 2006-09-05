<?php /* $Id: view_compta.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPsante400", "idsante400"));

$dialog = mbGetValueFromGet("dialog");

if (!$canRead && !$dialog) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$object_id    = mbGetValueFromGetOrSession("object_id"   );
$object_class = mbGetValueFromGetOrSession("object_class");
$tag          = mbGetValueFromGetOrSession("tag"         );

// Récupération de la liste des classes disponibles
$listClasses = getChildClasses();

// Chargement de la liste des praticiens pour l'historique
$idSante400 = new CIdSante400;

$where = array();
if ($object_id) {
  $where["object_id"] = "= '$object_id'";
}

if ($object_class) {
  $where["object_class"] = "= '$object_class'";
}

if ($tag) {
  $where["tag"] = "= '$tag'";
}

$order = "last_update DESC";
$limit = "0, 100";

$list_idSante400 = $idSante400->loadList($where, $order, $limit);
foreach($list_idSante400 as $idSante400) {
  $idSante400->loadRefs();
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listClasses", $listClasses);
$smarty->assign("object_id", $object_id);
$smarty->assign("object_class", $object_class);
$smarty->assign("tag", $tag);
$smarty->assign("list_idSante400", $list_idSante400);

$smarty->display("view_identifiants.tpl");

?>