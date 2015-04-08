<?php

/**
 * Affichage d'une liste de listes de choix
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$liste_id    = CValue::getOrSession("liste_id");
$user_id     = CValue::getOrSession("user_id");
$function_id = CValue::getOrSession("function_id");

if ($user_id) {
  $user   = CMediusers::get($user_id);
  $owners = $user->getOwners();
}
else {
  $function = new CFunctions();
  $function->load($function_id);
  $owners = $function->getOwners();
  $user_id = "";
}

$listes = CListeChoix::loadAllFor($user_id, $function_id);

// Modèles associés
foreach ($listes as $_listes) {
  foreach ($_listes as $_liste) {
    /** @var $_liste CListeChoix */
    $_liste->loadRefModele();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("liste_id", $liste_id);
$smarty->assign("owners"  , $owners);
$smarty->assign("listes"  , $listes);

$smarty->display("inc_list_listes_choix.tpl");
