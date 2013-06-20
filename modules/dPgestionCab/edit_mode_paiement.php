<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$user = CMediusers::get();
$user->loadRefsFwd();
$user->_ref_function->loadRefsFwd();

$etablissement = $user->_ref_function->_ref_group->text;

$mode_paiement_id = CValue::get("mode_paiement_id");

$modePaiement = new CModePaiement();
$modePaiement->load($mode_paiement_id);

// Récupération de la liste des functions
$function = new CFunctions();
$listFunc = $function->loadListWithPerms(PERM_EDIT);

$where            = array();
$itemModePaiement = new CModePaiement;
$order            = "nom DESC";
 
// Récupération de la liste des mode de paiement hors fonction
$where["function_id"]  = "IS NULL";
$listModePaiementGroup = $itemModePaiement->loadList($where, $order);
 
$listModePaiementFonction = array();

// Récupération de la liste des mode de paiement liés aux fonctions
foreach ($listFunc as $function) {
  $where["function_id"] = "= '$function->function_id'";
  $listModePaiementFonction[$function->text] = $itemModePaiement->loadList($where, $order);
}

$smarty = new CSmartyDP();

$smarty->assign("etablissement",            $etablissement);
$smarty->assign("listFunc",                 $listFunc);
$smarty->assign("modePaiement",             $modePaiement);
$smarty->assign("listModePaiementGroup",    $listModePaiementGroup);
$smarty->assign("listModePaiementFonction", $listModePaiementFonction);

$smarty->display("edit_mode_paiement.tpl");
