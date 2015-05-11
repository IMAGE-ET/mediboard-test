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
$filtre->function_id  = CValue::getOrSession("function_id");
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

$order_col = CView::get("order_col", "enum list|nom|object_class|file_category_id|type|_count_utilisation default|object_class", true);
$order_way = CView::get("order_way", "enum list|ASC|DESC default|DESC", true);

CView::checkin();

$order = "";

switch ($order_col) {
  default:
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

$owner = "prat";
$owner_id = $filtre->user_id;
$owners = $user->getOwners();

if ($filtre->function_id) {
  $owner = "func";
  $owner_id = $filtre->function_id;
  $func = new CFunctions();
  $func->load($owner_id);
  $owners = array(
    "func" => $func,
    "etab" => $func->loadRefGroup()
  );
} else {
  $sec_func = $user->loadRefsSecondaryFunctions();
  foreach($sec_func as $_func) {
    $owners["func" . $_func->_id] = $_func;
  }
}

$modeles = CCompteRendu::loadAllModelesFor($owner_id, $owner, $filtre->object_class, $filtre->type, 1, $order);
if ($filtre->function_id) {
  unset($modeles["prat"]);
}

foreach ($modeles as $key => &$_modeles) {
  /** @var $_modeles CStoredObject[] */
  CStoredObject::massCountBackRefs($_modeles, "documents_generated");
  /** @var $_modele CCompteRendu */
  foreach ($_modeles as $_modele) {
    $_modele->canDo();
    switch ($_modele->type) {
      case "body":
        $_modele->loadComponents();
        break;
      case "header":
        $_modele->countBackRefs("modeles_headed", array("object_id" => "IS NULL"));
        break;
      case "footer":
        $_modele->countBackRefs("modeles_footed", array("object_id" => "IS NULL"));
        break;
      case "preface":
        $_modele->countBackRefs("modeles_prefaced");
        break;
      case "ending":
        $_modele->countBackRefs("modeles_ended");
    }
  }

  if ($order_col == "_count_utilisation") {
    array_multisort(CMbArray::pluck($_modeles, "_count", "documents_generated"), $order_way == "ASC" ? SORT_ASC : SORT_DESC, $_modeles);
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