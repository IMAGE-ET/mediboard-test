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
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

$order_col = CValue::getOrSession("order_col", "object_class");
$order_way = CValue::getOrSession("order_way", "ASC");

// Praticien
// Liste des praticiens et cabinets accessibles
$user = CMediusers::get($filtre->user_id);
$filtre->user_id = $user->_id;
$praticiens = $user->loadUsers(PERM_EDIT);

// On ne met que les classes qui ont une methode filTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::getTemplatedClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre"       , $filtre);
$smarty->assign("praticiens"   , $praticiens);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("order_way"    , $order_way);

$smarty->display("vw_modeles.tpl");
