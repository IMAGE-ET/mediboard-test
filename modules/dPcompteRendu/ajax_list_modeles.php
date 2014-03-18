<?php 

/**
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Filtres
$filtre = new CCompteRendu();
$filtre->_id          = CValue::getOrSession("compte_rendu_id");
$filtre->user_id      = CValue::getOrSession("user_id");
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

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

// Praticien
$user = CMediusers::get($filtre->user_id);
$filtre->user_id = $user->_id;

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre"   , $filtre);
$smarty->assign("modeles"  , $modeles);
$smarty->assign("owners"   , $owners);
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);

$smarty->display("inc_list_modeles.tpl");