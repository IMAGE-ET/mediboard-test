<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$idSante400_id = CValue::get("idSante400_id");
$dialog        = CValue::get("dialog");

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id"   );
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag"         );
$filter->id400        = CValue::get("id400");
$filter->nullifyEmptyFields();

// Chargement de la cible si objet unique
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

$smarty = new CSmartyDP;
$smarty->assign("list_idSante400" , $list_idSante400);
$smarty->assign("count_idSante400", $count_idSante400);
$smarty->assign("filter"          , $filter);
$smarty->assign("idSante400_id"   , $idSante400_id);
$smarty->assign("dialog"          , $dialog);
$smarty->assign("target"          , $target);
$smarty->display("inc_list_identifiants.tpl");
?>