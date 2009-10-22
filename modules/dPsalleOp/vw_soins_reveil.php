<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$date         = mbGetValueFromGetOrSession("date", mbDate());
$bloc_id      = mbGetValueFromGetOrSession("bloc_id");
$op_reveil_id = mbGetValueFromGetOrSession("op_reveil_id");

$date_now        = mbDate();
$modif_operation = (CAppUI::conf("dPsalleOp COperation modif_actes") == "never") ||
                   ((CAppUI::conf("dPsalleOp COperation modif_actes") == "oneday") && ($date >= $date_now));

// Récupération de l'utilisateur courant
$currUser = new CMediusers();
$currUser->load(CAppUI::$instance->user_id);
$currUser->isAnesth();

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();

$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if(!$bloc->load($bloc_id) && count($blocs_list)) {
	$bloc = reset($blocs_list);
}

$op_reveil = new COperation();
$op_reveil->load($op_reveil_id);
if($op_reveil->_id) {
  $op_reveil->loadRefs();
  $modif_operation = $modif_operation || (CAppUI::conf("dPsalleOp COperation modif_actes") == "button" && !$op_reveil->_ref_plageop->actes_locked);
  $sejour =& $op_reveil->_ref_sejour;
	
  $modif_operation = $modif_operation || (CAppUI::conf("dPsalleOp COperation modif_actes") == "facturation" && !$op_reveil->facture);
	
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->_ref_dossier_medical->loadRefsBack();
  $sejour->loadRefsConsultAnesth();
  $sejour->loadRefsPrescriptions();
  $sejour->_ref_consult_anesth->loadRefsFwd();

  // Chargement des consultation d'anesthésie pour les associations a posteriori
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsConsultations();
  $patient->loadRefPhotoIdentite();
  foreach ($patient->_ref_consultations as $consultation) {
    $consultation->loadRefConsultAnesth();
    $consult_anesth =& $consultation->_ref_consult_anesth;
    if ($consult_anesth->_id) {
      $consultation->loadRefPlageConsult();
      $consult_anesth->loadRefOperation();
    }
  }

  $op_reveil->getAssociationCodesActes();
  $op_reveil->loadExtCodesCCAM();
  $op_reveil->loadPossibleActes();
  
  $op_reveil->_ref_plageop->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , CModule::getActive("dPImeds"));

$smarty->assign("currUser"   , $currUser);
$smarty->assign("listAnesths", $listAnesths);
$smarty->assign("listChirs"  , $listChirs);
$smarty->assign("acte_ngap"  , $acte_ngap);

$smarty->assign("date"           , $date);
$smarty->assign("blocs_list"     , $blocs_list);
$smarty->assign("bloc"           , $bloc);
$smarty->assign("op_reveil"      , $op_reveil);
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("vw_soins_reveil.tpl");

?>