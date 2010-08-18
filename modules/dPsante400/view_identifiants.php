<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$canSante400 = CModule::getCanDo("dPsante400");

// Récupération de la liste des classes disponibles
$listClasses = CApp::getInstalledClasses();

// Chargement de l'IdSante400 courant
$idSante400 = new CIdSante400;
$idSante400->load(CValue::get("id_sante400_id"));
$idSante400->loadRefs();

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id"   );
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag"         );
$filter->id400        = CValue::get("id400");
$filter->nullifyEmptyFields();

// Rester sur le même filtre en mode dialogue
$dialog = CValue::get("dialog");
if ($dialog && $idSante400->_id) {
  $filter->object_class = $idSante400->object_class;
  $filter->object_id    = $idSante400->object_id   ;
}

// Chargement de la cible si oBjet unique
$target = null;
if ($filter->object_id && $filter->object_class) {
  $target = new $filter->object_class;
  $target->load($filter->object_id);
}

// Requête du filtre
$order = "last_update DESC";
$max = CValue::get("max", 30);
$limit = "0, $max";

$list_idSante400 = $filter->loadMatchingList($order, $limit);
$count_idSante400 = $filter->countMatchingList();
foreach ($list_idSante400 as &$_idSante400) {
  $_idSante400->loadRefs();
}

$filter->last_update = CValue::first($idSante400->last_update, mbDateTime());

// Prendre exemple sur le fitre pour la création
if (!$idSante400->_id) {
  $idSante400 = $filter;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listClasses", $listClasses);
$smarty->assign("target", $target);
$smarty->assign("filter", $filter);
$smarty->assign("idSante400", $idSante400);
$smarty->assign("list_idSante400", $list_idSante400);
$smarty->assign("count_idSante400", $count_idSante400);
$smarty->assign("canSante400", $canSante400);
$smarty->display("view_identifiants.tpl");

?>