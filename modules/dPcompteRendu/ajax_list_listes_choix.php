<?php /* $Id: vw_idx_listes.php 12241 2011-05-20 10:29:53Z flaviencrochard $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 12241 $
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$liste_id = CValue::getOrSession("liste_id");
$user_id  = CValue::get("user_id");

$user = new CMediusers;
$user->load($user_id);
$owners  = $user->getOwners();
$listes  = CListeChoix::loadAllFor($user->_id);

// Modèles associés
foreach ($listes as $_listes) {
  foreach ($_listes as $_liste) {
    $_liste->loadRefModele();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("liste_id", $liste_id);
$smarty->assign("user"    , $user);
$smarty->assign("owners"  , $owners);
$smarty->assign("listes"  , $listes);

$smarty->display("inc_list_listes_choix.tpl");

?>