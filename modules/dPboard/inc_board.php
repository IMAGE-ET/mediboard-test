<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Chargement de l'utilisateur courant
$user = CMediusers::get();

if (!$user->isPraticien() && !$user->isSecretaire()) {
  CAppUI::redirect("m=system&a=access_denied");
}

$praticiens = null;
$prat = new CMediusers();

// Si le user est secretaire
if ($user->_is_secretaire) {
  // Chargement de la liste de praticien
  $praticiens = $user->loadPraticiens(PERM_EDIT);
  $prat->load(CValue::postOrSession("praticien_id"));
}

// Si le user est un praticien
if ($user->_is_praticien){
  $prat = $user;
}

global $smarty;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("prat"      , $prat);
$smarty->assign("praticiens", $praticiens);

$smarty->display("inc_board.tpl");

?>