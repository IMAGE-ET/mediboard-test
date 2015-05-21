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

CCanDo::checkEdit();
$_date_min  = CValue::getOrSession("_date_min");
$_date_max  = CValue::getOrSession("_date_max");
$_prat_id   = CValue::getOrSession("chir");
$typeVue    = CValue::getOrSession("typeVue");
$bloc_id    = CValue::get("bloc_id");

$nbActes = array();
$montantSejour = array();
$tabSejours = array();
$totalActes = 0;
$montantTotalActes = array(
  'total' => 0,
  'dh' => 0,
  'base' => 0,
);

$praticien = new CMediusers();
$praticien->load($_prat_id);

$date_min = "$_date_min 00:00:00";
$date_max = "$_date_max 23:59:59";

$ljoin = array();
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["acte_ccam"] = "consultation.consultation_id = acte_ccam.object_id AND acte_ccam.object_class = 'CConsultation'";
$where = array();
$where[] = "acte_ccam.facturable = '1'";
$where[] = "acte_ccam.execution BETWEEN '$date_min' AND '$date_max'";
$where[] = "acte_ccam.executant_id = '$_prat_id'";
$sejour = new CSejour();
$sejours = $bloc_id ? array() : $sejour->loadList($where, null, null, "sejour_id", $ljoin);

$ljoin = array();
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["acte_ngap"] = "consultation.consultation_id = acte_ngap.object_id AND acte_ngap.object_class = 'CConsultation'";
$where2 = array();
$where2[] = "acte_ngap.facturable = '1'";
$where2[] = "acte_ngap.execution BETWEEN '$date_min' AND '$date_max'";
$where2[] = "acte_ngap.executant_id = '$_prat_id'";
$sejour = new CSejour();
$sejours_ngap = $bloc_id ? array() : $sejour->loadList($where2, null, null, "sejour_id", $ljoin);
foreach ($sejours_ngap as $_sejour_ngap) {
  if (!isset($sejours[$_sejour_ngap->_id])) {
    $sejours[$_sejour_ngap->_id] = $_sejour_ngap;
  }
}

$ljoin = array();
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
$ljoin["acte_ccam"] = "operations.operation_id = acte_ccam.object_id AND acte_ccam.object_class = 'COperation'";
$where["operations.annulee"] = " = '0'";
if ($bloc_id) {
  $ljoin["sallesbloc"]      = "sallesbloc.salle_id = operations.salle_id";
  $ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";
  $where["operations.salle_id"] = " IS NOT NULL";
  $where["bloc_operatoire.bloc_operatoire_id"] = " = '$bloc_id'";
}
$sejours_consult = $sejour->loadList($where, null, null, "sejour_id", $ljoin);
foreach ($sejours_consult as $_sejour_consult) {
  if (!isset($sejours[$_sejour_consult->_id])) {
    $sejours[$_sejour_consult->_id] = $_sejour_consult;
  }
}

$ljoin = array();
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
$ljoin["acte_ngap"] = "operations.operation_id = acte_ngap.object_id AND acte_ngap.object_class = 'COperation'";
$where2["operations.annulee"] = " = '0'";
if ($bloc_id) {
  $ljoin["sallesbloc"]      = "sallesbloc.salle_id = operations.salle_id";
  $ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";
  $where2["operations.salle_id"] = " IS NOT NULL";
  $where2["bloc_operatoire.bloc_operatoire_id"] = " = '$bloc_id'";
}
$sejours_consult_ngap = $sejour->loadList($where2, null, null, "sejour_id", $ljoin);
foreach ($sejours_consult_ngap as $_sejour_consult_ngap) {
  if (!isset($sejours[$_sejour_consult_ngap->_id])) {
    $sejours[$_sejour_consult_ngap->_id] = $_sejour_consult_ngap;
  }
}

foreach ($sejours as $sejour) {
  /* @var CSejour $sejour*/
  $sejour->loadRefPatient();
  $sejour->loadRefsOperations();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsActes(null, 1);
  $sejour->loadRefsFactureEtablissement();
  foreach ($sejour->_ref_operations as $op) {
    $op->loadRefsActes(null, 1);
    if (!count($op->_ref_actes)) {
      unset($sejour->_ref_operations[$op->_id]);
    }
  }
  foreach ($sejour->_ref_consultations as $consult) {
    $consult->loadRefsActes(null, 1);
    if (!count($consult->_ref_actes)) {
      unset($sejour->_ref_consultations[$consult->_id]);
    }
  }
  if (!count($sejour->_ref_actes) && !count($sejour->_ref_operations) && !count($sejour->_ref_consultations)) {
    unset($sejours[$sejour->_id]);
  }
  else {
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab") && CAppUI::conf("ref_pays") == 1) {
      if (!$sejour->_ref_last_facture->_id) {
        if ($msg = $sejour->store()) {mbLog($msg);}
        $sejour->loadRefsFactureEtablissement();
      }

      $facture = $sejour->_ref_last_facture;
      $facture->_ref_sejours = array($sejour->_id => $sejour);
      $facture->updateMontants();

      if ($msg = $facture->store()) {mbLog($msg);}
      // Ajout de reglements
      $facture->_new_reglement_patient = new CReglement();
      $facture->_new_reglement_patient->setObject($facture);
      $facture->_new_reglement_patient->montant = $facture->_du_restant;
      $use_mode_default = CAppUI::conf("dPfacturation CReglement use_mode_default");
      $facture->_new_reglement_patient->mode = $use_mode_default != "none"  ? $use_mode_default : "autre";
    }
    $sejour->loadRefPatient();
    $tabSejours[CMbDT::date($sejour->sortie)][$sejour->_id] = $sejour;
    $nbActes[$sejour->_id] = 0;
    $montantSejour[$sejour->_id] = 0;
    // Calcul du nombre d'actes par sejour
    if ($sejour->_ref_actes) {
      if (count($sejour->_ref_actes)) {
        foreach ($sejour->_ref_actes as $acte) {
          if ($acte->executant_id == $_prat_id) {
            $nbActes[$sejour->_id]++;
            $montantSejour[$sejour->_id] += $acte->_montant_facture;
            $montantTotalActes['base']  += $acte->montant_base;
            $montantTotalActes['dh']    += $acte->montant_depassement;
          }
        }
      }
    }
    if ($sejour->_ref_operations) {
      foreach ($sejour->_ref_operations as $operation) {
        if (count($operation->_ref_actes)) {
          $operation->loadRefPlageOp();
          foreach ($operation->_ref_actes as $acte) {
            if ($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
              $montantTotalActes['base']  += $acte->montant_base;
              $montantTotalActes['dh']    += $acte->montant_depassement;
            }
          }
        }
      }
    }
    if ($sejour->_ref_consultations) {
      foreach ($sejour->_ref_consultations as $consult) {
        if (count($consult->_ref_actes)) {
          foreach ($consult->_ref_actes as $acte) {
            if ($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
              $montantTotalActes['base']  += $acte->montant_base;
              $montantTotalActes['dh']    += $acte->montant_depassement;
            }
          }
        }
      }
    }
    $totalActes        += $nbActes[$sejour->_id];
    $montantTotalActes['total'] += $montantSejour[$sejour->_id];
  }
}

// Tri par date du tableau de sejours
ksort($tabSejours);

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("montantTotalActes", $montantTotalActes);
$smarty->assign("totalActes"       , $totalActes);
$smarty->assign("nbActes"          , $nbActes);
$smarty->assign("sejours"          , $tabSejours);
$smarty->assign("montantSejour"    , $montantSejour);
$smarty->assign("praticien"        , $praticien);
$smarty->assign("_date_min"        , $_date_min);
$smarty->assign("_date_max"        , $_date_max);
$smarty->assign("typeVue"          , $typeVue);
$smarty->assign("bloc"             , $bloc);

if (CAppUI::conf("ref_pays") == 1) {
  $smarty->display("vw_actes_realises.tpl");
}
else {
  $smarty->display("vw_actes_realises2.tpl");
}
