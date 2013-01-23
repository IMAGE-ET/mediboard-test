<?php /* $Id $ */

/**
 * List idexs
 *
 * @category dPsante400
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$idSante400_id = CValue::get("idSante400_id");
$dialog        = CValue::get("dialog");

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id"   );
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag"         );
$filter->id400        = CValue::get("id400"       );
$filter->nullifyEmptyFields();

// Chargement de la cible si objet unique
$target = null;
if ($filter->object_id && $filter->object_class) {
  $target = new $filter->object_class;
  $target->load($filter->object_id);
}

// Requête du filtre
$order = "last_update DESC";
$max   = CValue::get("max", 30);
$limit = "0, $max";

$idexs = $filter->loadMatchingList($order, $limit);
foreach ($idexs as $_idex) {
  $_idex->loadRefs();
  $_idex->getSpecialType();
}

// Création du template
$smarty = new CSmartyDP;
$smarty->assign("idexs"        , $idexs);
$smarty->assign("filter"       , $filter);
$smarty->assign("idSante400_id", $idSante400_id);
$smarty->assign("dialog"       , $dialog);
$smarty->assign("target"       , $target);
$smarty->display("inc_list_identifiants.tpl");