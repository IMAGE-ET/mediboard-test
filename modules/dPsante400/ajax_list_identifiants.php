<?php

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

$idex_id = CValue::get("idex_id");
$dialog  = CValue::get("dialog");
$page    = intval(CValue::get('page', 0));

// Chargement de la liste des id4Sante400 pour le filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id");
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag");
$filter->id400        = CValue::get("id400");
$filter->nullifyEmptyFields();

// Chargement de la cible si objet unique
$target = null;
if ($filter->object_id && $filter->object_class) {
  $target = new $filter->object_class;
  $target->load($filter->object_id);
}

// Requête du filtre
$step  = 25;
$idexs = $filter->loadMatchingList(null, "$page, $step");
foreach ($idexs as $_idex) {
  $_idex->loadRefs();
  $_idex->getSpecialType();
}

$total_idexs = $filter->countMatchingList();

// Création du template
$smarty = new CSmartyDP;

$smarty->assign("idexs"      , $idexs);
$smarty->assign("total_idexs", $total_idexs);
$smarty->assign("filter"     , $filter);
$smarty->assign("idex_id"    , $idex_id);
$smarty->assign("dialog"     , $dialog);
$smarty->assign("page"       , $page);
$smarty->assign("target"     , $target);

$smarty->display("inc_list_identifiants.tpl");