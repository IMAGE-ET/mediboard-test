<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$correspondant_id = CValue::get("correspondant_id");

$correspondant = new CCorrespondantPatient();
$correspondant->load($correspondant_id);

if (CAppUI::conf('dPpatients CPatient function_distinct') && $correspondant->_id) {
  $current_user = CMediusers::get();
  $is_admin = $current_user->isAdmin();
  $same_function = $current_user->function_id == $correspondant->function_id;
  if (!$is_admin && !$same_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
}

//smarty
$smarty = new CSmartyDP();

$smarty->assign("correspondant", $correspondant);
$smarty->assign("mode_modele"  , 1);

$smarty->display("inc_form_correspondant.tpl");
