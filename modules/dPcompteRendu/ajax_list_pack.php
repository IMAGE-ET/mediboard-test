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

$user_id      = CValue::getOrSession("user_id", CAppUI::$user->_id);
$object_class = CValue::getOrSession("object_class");

$user = CMediusers::get($user_id);
$user->loadRefFunction();

$packs = CPack::loadAllPacksFor($user_id, "user", $object_class);
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

$smarty->assign("user" , $user);
$smarty->assign("packs", $packs);

$smarty->display("inc_list_pack.tpl");
