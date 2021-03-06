<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$user = CMediusers::get();
$user->loadRefFunction();

$selCabinet = CValue::getOrSession("selCabinet", $user->function_id);
$droit = true;

// si on affecte a selCabinet le function_id du user, on verifie si le user a le droit de creer des categories
if ($selCabinet == $user->function_id) {
  // Chargement de la liste de tous la cabinets
  $cabinet = new CFunctions();
  $listCabinets = $cabinet->loadSpecialites();
  if (!array_key_exists($selCabinet, $listCabinets)) {
    $droit = false;
  }
}

// Chargement de la liste des cabinets auquel le user a droit
$function = new CFunctions();
$listFunctions = $function->loadSpecialites(PERM_EDIT);

// Creation d'une categorie
$categorie = new CConsultationCategorie();
$categorie_id = CValue::getOrSession("categorie_id");

// Chargement des categories pour le cabinet selectionn� ou pour le cabinet auquel appartient le user
if ($selCabinet) {
  $whereCategorie["function_id"] = " = '$selCabinet'";
}
else {
  $whereCategorie["function_id"] = " = '$user->function_id'";
}

$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie, $orderCategorie);

// Chargement de la categorie selectionnee
if ($categorie_id) {
  $categorie = new CConsultationCategorie();
  $categorie->load($categorie_id);
}
else {
  $categorie->valueDefaults();
}
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("droit"        , $droit);
$smarty->assign("listFunctions", $listFunctions);
$smarty->assign("selCabinet", $selCabinet);
$smarty->assign("categories", $categories);
$smarty->assign("categorie", $categorie);
$smarty->display("vw_categories.tpl");
