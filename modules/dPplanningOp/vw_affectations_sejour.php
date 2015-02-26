<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();
$sejour_id = CValue::getOrSession("sejour_id", 0);

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefsUserSejour();
$user_affecte = $sejour->_ref_users_by_type;

$user = CMediusers::get();
$users = array(
  "infirmiere" => $user->loadListFromType(array("Infirmière")),
  "AS"  => $user->loadListFromType(array("Aide soignant")),
  "SF"  => $user->loadListFromType(array("Sage Femme")),
);

foreach ($user_affecte as $type => $users_affected) {
  foreach ($users_affected as $_user_sejour) {
    $_user = $_user_sejour->loadRefUser();
    if (isset($users[$type][$_user->_id])) {
      unset($users[$type][$_user->_id]);
    }
  }
}

$user_sejour = new CUserSejour();
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("users" , $users);
$smarty->assign("user_sejour" , $user_sejour);
$smarty->assign("refresh" , CValue::get("refresh", 0));

$smarty->display("vw_affectations_sejour.tpl");
