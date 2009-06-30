<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$user = new CUser;
$user->load($AppUI->user_id);
$user->updateSpecs();

$forceChange = dPgetParam($_REQUEST, "forceChange");

if ($forceChange) {
$message = '<strong>Votre mot de passe ne correspond pas aux critères de sécurité de Mediboard</strong>. Vous ne pourrez
            pas accéder à Mediboard tant que vous ne l\'aurez pas changé afin qu\'il respecte ces critères.
            La sécurité des informations de vos patients en dépend.<br />
            Pour plus de précisions, veuillez vous référer aux 
            <a href="http://mediboard.org/public/tiki-index.php?page=Recommandations+de+la+CNIL" target="_blank">
            recommandations de la CNIL</a>.<br />';
} else {
  $message = null;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",    $user);
$smarty->assign("message", $message);
$smarty->display("change_password.tpl");
?>