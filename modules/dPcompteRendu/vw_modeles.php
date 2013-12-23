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

$order_col = CValue::getOrSession("order_col", "object_class");
$order_way = CValue::getOrSession("order_way", "ASC");
$order = "";

switch ($order_col) {
  case "object_class":
    $order = "object_class $order_way, type, nom";
    break;
  case "nom":
    $order = "nom $order_way, object_class, type";
    break;
  case "type":
    $order = "type $order_way, object_class, nom";
    break;
  case "file_category_id":
    $order = "file_category_id $order_way, object_class, nom";
}

// Filtres
$filtre = new CCompteRendu();
$filtre->user_id      = CValue::getOrSession("user_id");
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

// Praticien
// Liste des praticiens et cabinets accessibles
$user = CMediusers::get($filtre->user_id);
$filtre->user_id = $user->_id;
$praticiens = $user->loadUsers(PERM_EDIT);

$owners = $user->getOwners();
$modeles = CCompteRendu::loadAllModelesFor($filtre->user_id, 'prat', $filtre->object_class, $filtre->type, 1, $order);
foreach ($modeles as $_modeles) {
  /** @var $_modeles CStoredObject[] */
  CStoredObject::massCountBackRefs($_modeles, "documents_generated");
  /** @var $_modele CCompteRendu */
  foreach ($_modeles as $_modele) {
    $_modele->canDo();
    $_modele->countBackRefs("documents_generated");
    switch ($_modele->type) {
      case "body":
        $_modele->loadComponents();
        break;
      case "header":
        $_modele->countBackRefs("modeles_headed");
        break;
      case "footer":
        $_modele->countBackRefs("modeles_footed");
        break;
      case "preface":
        $_modele->countBackRefs("modeles_prefaced");
        break;
      case "ending":
        $_modele->countBackRefs("modeles_ended");
    }
  }
}

// On ne met que les classes qui ont une methode filTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::getTemplatedClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"         , $user);
$smarty->assign("filtre"       , $filtre);
$smarty->assign("praticiens"   , $praticiens);
$smarty->assign("modeles"      , $modeles);
$smarty->assign("owners"       , $owners);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("special_names", CCompteRendu::$special_names);

$smarty->display("vw_modeles.tpl");
