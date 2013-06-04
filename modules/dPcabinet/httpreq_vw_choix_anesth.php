<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

// Utilisateur sélectionné ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);

$userSel = CMediusers::get($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
if (CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChir = $userSel->loadPraticiens(PERM_EDIT);
}
else {
  $listChir = $userSel->loadProfessionnelDeSante(PERM_EDIT);
}

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult", 0);
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id", 0);

if (isset($_GET["date"])) {
  $selConsult = null;
  CValue::setSession("selConsult", 0);
}

$anesth = new CTypeAnesth();
$orderanesth = "name";
$anesth = $anesth->loadList(null, $orderanesth);


// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  CCanDo::checkObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefConsultAnesth();
  $consult->loadRefPlageConsult();

  if (isset($consult->_refs_dossiers_anesth[$dossier_anesth_id])) {
    $consult->_ref_consult_anesth = $consult->_refs_dossiers_anesth[$dossier_anesth_id];
    $consult->_ref_consult_anesth->loadRefs();
    $sejour =& $consult->_ref_consult_anesth->_ref_sejour;

    if ($consult->_ref_consult_anesth->_ref_operation->operation_id) {
      if ($consult->_ref_consult_anesth->_ref_operation->passage_uscpo === null) {
        $consult->_ref_consult_anesth->_ref_operation->passage_uscpo = "";
      }
      $consult->_ref_consult_anesth->_ref_operation->loadRefSejour();
      $sejour =& $consult->_ref_consult_anesth->_ref_operation->_ref_sejour;
    }
  }

  $consult_anesth =& $consult->_ref_consult_anesth;
  
}
else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("consult"       , $consult       );
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("anesth"        , $anesth        );
$smarty->assign("techniquesComp", new CTechniqueComp());
$smarty->assign("userSel"       , $userSel);

$smarty->display("inc_consult_anesth/acc_infos_anesth.tpl");
