<?php /* $Id: view_compta.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;


$dialog = mbGetValueFromGet("dialog");

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied&dialog=$dialog");
}

// Récupération de la liste des classes disponibles
$listClasses = getInstalledClasses();

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = mbGetValueFromGetOrSession("object_id"   );
$filter->object_class = mbGetValueFromGetOrSession("object_class");
$filter->tag          = mbGetValueFromGetOrSession("tag"         );
$filter->id400        = mbGetValueFromGetOrSession("id400");
$filter->nullifyEmptyFields();

// Chargment de la cible si ojet unique
$target = null;
if ($filter->object_id && $filter->object_class) {
  $target = new $filter->object_class;
  $target->load($filter->object_id);
}

$order = "last_update DESC";
$limit = "0, 100";

$list_idSante400 = $filter->loadMatchingList($order, $limit);
foreach ($list_idSante400 as $curr_idSante400) {
  $curr_idSante400->loadRefs();
}

// Chargment de l'IdSante400 courant
$idSante400 = new CIdSante400;
$idSante400->load(mbGetValueFromGetOrSession("id_sante400_id"));
$idSante400->loadRefs();

$last_update = mbGetValue($idSante400->last_update, mbDateTime());
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listClasses", $listClasses);
$smarty->assign("last_update", $last_update);
$smarty->assign("target", $target);
$smarty->assign("filter", $filter);
$smarty->assign("idSante400", $idSante400);
$smarty->assign("list_idSante400", $list_idSante400);

$smarty->display("view_identifiants.tpl");

?>