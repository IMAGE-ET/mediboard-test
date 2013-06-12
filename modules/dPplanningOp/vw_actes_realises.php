<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

CCanDo::checkEdit();
$_date_min  = CValue::getOrSession("_date_min");
$_date_max  = CValue::getOrSession("_date_max");
$_prat_id   = CValue::getOrSession("chir");
$typeVue    = CValue::getOrSession("typeVue");

$nbActes = array();
$montantSejour = array();
$tabSejours = array();
$totalActes = 0;
$montantTotalActes = 0;

$praticien = new CMediusers();
$praticien->load($_prat_id);

$date_min = $_date_min . " 00:00:00";
$date_max = $_date_max . " 23:59:59";

$sejour = new CSejour();
$ljoin = array();
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";

$where = array();
$where["sejour.sortie"] = " BETWEEN '$date_min' AND '$date_max'";
$where[] = "sejour.praticien_id = '$_prat_id' OR operations.chir_id = '$_prat_id'";

/** @var  CSejour[] $sejours*/
$sejours = $sejour->loadList($where, null, null, "sejour_id", $ljoin);

foreach ($sejours as $key => $sejour) {
  $sejour->loadRefPatient();
  $sejour->loadRefsOperations();
  $sejour->loadRefsActes();
  $sejour->loadRefsFactureEtablissement();
  foreach ($sejour->_ref_operations as $keyop => $op) {
    $op->loadRefsActes();
    if (!count($op->_ref_actes)) {
      unset($sejour->_ref_operations[$keyop]);
    }
  }
  if (!count($sejour->_ref_actes) && !count($sejour->_ref_operations)) {
    unset($sejours[$key]);
  }
  else {
    $sejour->loadRefPatient();
    $tabSejours[CMbDT::date($sejour->sortie)][$sejour->_id] = $sejour;
    $nbActes[$sejour->_id] = 0;
    $montantSejour[$sejour->_id] = 0;
    // Calcul du nombre d'actes par sejour
    if ($sejour->_ref_actes) {
      if (count($sejour->_ref_actes)) {
        foreach ($sejour->_ref_actes as $acte) {
          if($acte->executant_id == $_prat_id) {
            $nbActes[$sejour->_id]++;
            $montantSejour[$sejour->_id] += $acte->_montant_facture;
          }
        }
      }
    }
    if ($sejour->_ref_operations) {
      foreach ($sejour->_ref_operations as $operation) {
        if (count($operation->_ref_actes)) {
          $operation->loadRefPlageOp();
          foreach ($operation->_ref_actes as $acte) {
            if($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
            }
          }
        }
      }
    }
    if ($sejour->_ref_consultations) {
      foreach ($sejour->_ref_consultations as $consult) {
        if (count($consult->_ref_actes)) {
          foreach ($consult->_ref_actes as $acte) {
            if($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
            }
          }
        }
      }
    }
    $totalActes        += $nbActes[$sejour->_id];
    $montantTotalActes += $montantSejour[$sejour->_id];
  }
}

// Tri par date du tableau de sejours
ksort($tabSejours);

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

if (CAppUI::conf("ref_pays") == 1) {
  $smarty->display("vw_actes_realises.tpl");
}
else {
  $smarty->display("vw_actes_realises2.tpl");
}
