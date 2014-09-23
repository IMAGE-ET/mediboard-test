<?php

/**
 * Interface des modèles
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Filtres
$filtre = new CCompteRendu();
$filtre->user_id      = CValue::getOrSession("user_id");
$filtre->function_id  = CValue::getOrSession("function_id");
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

$order_col = CValue::getOrSession("order_col", "object_class");
$order_way = CValue::getOrSession("order_way", "ASC");

if (!in_array($order_col, array("nom", "object_class", "file_category_id", "type", "_count_utilisation"))) {
  $order_col = "object_class";
}

$curr_user = CMediusers::get();

// Praticiens
// Liste des praticiens accessibles
$praticiens = $curr_user->loadUsers(PERM_EDIT);

// Fonctions
// Liste des fonctions accessibles
$functions = array_unique(CMbArray::pluck($praticiens, "_ref_function"));
array_multisort(CMbArray::pluck($functions, "text"), SORT_ASC, $functions);

// On ne met que les classes qui ont une methode fillTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::getTemplatedClasses();

if (!$filtre->user_id && !$filtre->function_id) {
  $filtre->user_id = $curr_user->_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre"    , $filtre);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("functions" , $functions);
$smarty->assign("order_col" , $order_col);
$smarty->assign("order_way" , $order_way);

$smarty->display("vw_modeles.tpl");
