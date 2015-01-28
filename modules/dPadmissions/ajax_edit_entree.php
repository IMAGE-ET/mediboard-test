<?php 

/**
 * $Id$
 *  
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$sejour_id            = CValue::get("sejour_id");
$module               = CValue::get("module");
$callback             = CValue::get("callback");
$modify_entree_prevue = CValue::get("modify_entree_prevue", true);

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadNDA();

$sejour->loadRefServiceMutation();
$sejour->loadRefEtablissementTransfert();

//Cas des urgences
if (CModule::getActive("dPurgences")) {
  $sejour->loadRefRPU()->loadRefSejourMutation();
}

$patient = $sejour->loadRefPatient();
$patient->loadIPP();

// maternité
if (CModule::getActive("maternite") && $sejour->grossesse_id) {
  $sejour->loadRefsNaissances();
  foreach ($sejour->_ref_naissances as $_naissance) {
    /** @var CNaissance $_naissance */
    $_naissance->loadRefSejourEnfant()->loadRefPatient();
  }
  $sejour->_sejours_enfants_ids = CMbArray::pluck($sejour->_ref_naissances, "sejour_enfant_id");
}

// list mode entree
$mode_entree = new CModeEntreeSejour();
$mode_entree->actif = 1;
$mode_entree->group_id = CGroups::loadCurrent()->_id;
$modes_entree = $mode_entree->loadMatchingList("libelle");

$service = new CService();
$service->group_id = CGroups::loadCurrent()->_id;
$services = $service->loadMatchingList();



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date_actuelle"            , $date_actuelle);
$smarty->assign("date_demain"            , $date_demain);


$smarty->assign("callback"            , stripslashes($callback));
$smarty->assign("modify_sortie_prevue", $modify_entree_prevue);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("module"              , $module);
$smarty->assign("list_mode_entree"    , $modes_entree);
$smarty->assign("services"            , $services);
$smarty->display("inc_edit_entree.tpl");