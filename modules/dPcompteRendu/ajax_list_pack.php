<?php

/**
 * Liste des packs de modèles pour un utilisateur
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id      = CValue::getOrSession("user_id");
$object_class = CValue::getOrSession("object_class");

$user = CMediusers::get($user_id);
$owners = $user->getOwners();
$packs = CPack::loadAllPacksFor($user->_id, "prat", $object_class);

foreach ($packs as $_packs_by_owner) {
  foreach ($_packs_by_owner as $_pack) {
    /** @var $_pack CPack */
    $_pack->loadRefOwner();
    $_pack->loadBackRefs("modele_links");
    $_pack->loadHeaderFooter();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"  , $user);
$smarty->assign("owners", $owners);
$smarty->assign("packs" , $packs);

$smarty->display("inc_list_pack.tpl");
