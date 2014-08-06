<?php 

/**
 * $Id$
 *  
 * @category dPadmissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$sejour_id            = CValue::get("sejour_id");
$module               = CValue::get("module");
$callback             = CValue::get("callback");
$modify_sortie_prevue = CValue::get("modify_sortie_prevue", true);

$sejour = new CSejour();
$sejour->load($sejour_id);

$can_admission = CModule::getCanDo("dPadmissions");

if (!$sejour->canDo()->edit && !$can_admission->edit &&
  !CModule::getCanDo("dPhospi")->edit && !CModule::getCanDo("dPurgences")->edit && !CModule::getCanDo("soins")->edit
) {
  $can_admission->redirect();
}

$sejour->loadRefServiceMutation();
$sejour->loadRefEtablissementTransfert();

//Cas des urgences
if (CModule::getActive("dPurgences")) {
  $sejour->loadRefRPU()->loadRefSejourMutation();
}

$patient = $sejour->loadRefPatient();

if (!$modify_sortie_prevue && !$sejour->sortie_reelle) {
  $sejour->sortie_reelle = CMbDT::dateTime();
}

if (CModule::getActive("maternite") && $sejour->grossesse_id) {
  $sejour->loadRefsNaissances();
  foreach ($sejour->_ref_naissances as $_naissance) {
    /** @var CNaissance $_naissance */
    $_naissance->loadRefSejourEnfant()->loadRefPatient();
  }
  $sejour->_sejours_enfants_ids = CMbArray::pluck($sejour->_ref_naissances, "sejour_enfant_id");

}

//Cas du mode sortie personnalisé
$list_mode_sortie = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
  $mode_sortie = new CModeSortieSejour();
  $where       = array(
    "actif" => "= '1'",
  );
  $list_mode_sortie = $mode_sortie->loadGroupList($where);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("callback"            , stripslashes($callback));

$smarty->assign("modify_sortie_prevue", $modify_sortie_prevue);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("module"              , $module);
$smarty->assign("list_mode_sortie"    , $list_mode_sortie);
$smarty->assign("list_mode_sortie"    , $list_mode_sortie);

$smarty->display("inc_edit_sortie.tpl");