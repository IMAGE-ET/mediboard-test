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

$order_col = CView::get("order_col", "enum list|nom|object_class|file_category_id|type|_count_utilisation default|object_class", true);
$order_way = CView::get("order_way", "enum list|ASC|DESC default|DESC", true);

CView::checkin();

// On ne met que les classes qui ont une methode fillTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::$templated_classes;

if (!$filtre->user_id && !$filtre->function_id) {
  $filtre->user_id = CMediusers::get()->_id;
}

$filtre->loadRefUser();
$filtre->loadRefFunction();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre"   , $filtre);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);

$smarty->display("vw_modeles.tpl");
