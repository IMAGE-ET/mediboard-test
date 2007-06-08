<?php /* $Id: view_compta.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: 331 $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$dialog = mbGetValueFromGet("dialog");

$can->needsRead();

// Récupération de la liste des classes disponibles
$listClasses = getInstalledClasses();

// Chargement de l'IdSante400 courant
$idSante400 = new CIdSante400;
$idSante400->load(mbGetValueFromGet("id_sante400_id"));
$idSante400->loadRefs();

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = mbGetValueFromGet("object_id"   );
$filter->object_class = mbGetValueFromGet("object_class");
$filter->tag          = mbGetValueFromGet("tag"         );
$filter->id400        = mbGetValueFromGet("id400");
$filter->nullifyEmptyFields();

// Rester sur le même filtre en mode dialogue
if ($dialog && $idSante400->_id) {
  $filter->object_class = $idSante400->object_class;
  $filter->object_id    = $idSante400->object_id   ;
}

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

$filter->last_update = mbGetValue($idSante400->last_update, mbDateTime());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listClasses", $listClasses);
$smarty->assign("target", $target);
$smarty->assign("filter", $filter);
$smarty->assign("idSante400", $idSante400);
$smarty->assign("list_idSante400", $list_idSante400);

$smarty->display("view_identifiants.tpl");

?>