<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

// !! Attention, régression importante si ajout de type de paiement

$today = mbDate();

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", mbDate());
$filter->_date_max = CValue::getOrSession("_date_max", mbDate());

$filter->_mode_reglement = CValue::getOrSession("mode", 0);

$filter->_type_affichage  = CValue::getOrSession("_type_affichage" , 1);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if ($filter->_type_affichage == "complete") {
  $filter->_type_affichage = 1;
}
elseif ($filter->_type_affichage == "totaux") {
  $filter->_type_affichage = 0;
}

// On recherche tous les règlements effectués selon les critères
$ljoin = array();
$ljoin["consultation"] = "reglement.object_id = consultation.consultation_id";
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where = array();
$where[] = "DATE(reglement.date) >= '$filter->_date_min' AND DATE(reglement.date) <= '$filter->_date_max'";

// Tri sur les modes de paiement
if ($filter->_mode_reglement) {
  $where["reglement.mode"] = "= '$filter->_mode_reglement'";
}

// Tri sur les praticiens
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();

$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
$prat->loadRefFunction();
if ($prat->_id) {
  $listPrat = array($prat->_id => $prat);
}
else {
  $listPrat = $mediuser->loadPraticiensCompta();
}

$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));

// Chargement
$reglement = new CReglement();
$reglements = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);

//Si la config facturation est acitvée:
// Chargement des règlements de facture cloturées
//if (CAppUI::conf("dPcabinet CConsultation consult_facture")) {
  $where = array();
  $ljoin = array();
  
  $ljoin["factureconsult"] = "reglement.object_id = factureconsult.factureconsult_id";
  
   // Celles ayant des consultations qui ont des plages de consult du praticien concerné
  if ($prat->_id) {
    $ljoin["consultation"] = "consultation.factureconsult_id = factureconsult.factureconsult_id";
    $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
    $where["plageconsult.chir_id"] = "= '$prat->_id'";
  }
  $where[] = "DATE(reglement.date) >= '$filter->_date_min' AND DATE(reglement.date) <= '$filter->_date_max'";
  $where["factureconsult.cloture"] = "IS NOT NULL";
  // Tri sur les modes de paiement
  if ($filter->_mode_reglement) {
    $where["reglement.mode"] = "= '$filter->_mode_reglement'";
  }
  // Chargement
  $reglement = new CReglement();
  $reglementss = $reglement->loadList($where, "reglement.date", null, null, $ljoin);
  
  foreach ($reglementss as $key => $value) {
    $reglements[$key] = $value;
  }
//}

// Calcul du récapitulatif
// Initialisation du tableau de reglements
$recapReglement["total"] = array(
  "du_patient"           => "0",
  "du_tiers"             => "0",
  "nb_reglement_patient" => "0",
  "nb_reglement_tiers"   => "0",
  "secteur1"             => "0",
  "secteur2"             => "0",
  "montant_avec_remise"  => "0",
);
foreach (array_merge($reglement->_specs["mode"]->_list, array("")) as $_mode) {
  $recapReglement[$_mode] = array(
   "du_patient"           => "0",
   "du_tiers"             => "0",
   "nb_reglement_patient" => "0",
   "nb_reglement_tiers"   => "0",
  );
}

$listReglements = array();
$listConsults = array();
foreach ($reglements as  $key => $_reglement) {
  $del = 0;
  $_reglement->loadTargetObject();
  $_reglement->_ref_object->loadRefPatient(1);
  $_reglement->_ref_object->loadRefPlageConsult(1);
  $_reglement->_ref_object->loadRefPraticien();
  $_reglement->_ref_object->loadRefsReglements();
  if ($_reglement->object_class == "CConsultation") {
    $_reglement->_ref_object->loadRefsActes();
  }
  else {
    foreach ($_reglement->_ref_object->_ref_consults as $consult) {
      $consult->loadRefsActes();
    }
  }
  if (CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
    if (isset($_reglement->_ref_object->_ref_actes_tarmed) && !$_reglement->_ref_object->_ref_actes_tarmed) {
      $del = 1;
    }
    elseif (!isset($_reglement->_ref_object->_ref_actes_tarmed) && isset($_reglement->_ref_object->_ref_consults)) {
     if (!count($_reglement->_ref_object->_ref_consults)) {
      $del = 1;
     }
      foreach ($_reglement->_ref_object->_ref_consults as $consult) {
        $consult->loadRefsActes();
        if (!count($consult->_ref_actes_tarmed)) {
          $del = 1;
        }
      }
    }
  }
  if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
    if ($_reglement->object_class == "CConsultation" && !count($_reglement->_ref_object->_ref_actes_ngap) && !count($_reglement->_ref_object->_ref_actes_ccam)) {
      $del = 1;
    }
    elseif ($_reglement->object_class == "CFactureConsult") {
      if (count($_reglement->_ref_object->_ref_consults)) {
        foreach ($_reglement->_ref_object->_ref_consults as $consult) {
          $consult->loadRefsActes();
          if (count($consult->_ref_actes_ngap) == 0 && count($consult->_ref_actes_ccam) == 0) {
            $del = 1;
          }
        }
      }
      else {
        $del =1;
      }
    }
  }
  if ($del) {
    unset($reglements[$key]);
  }
  else {
    if ($_reglement->emetteur == "patient") {
      $recapReglement["total"]["du_patient"] += $_reglement->montant;
      $recapReglement["total"]["nb_reglement_patient"]++;
      $recapReglement[$_reglement->mode]["du_patient"] += $_reglement->montant;
      $recapReglement[$_reglement->mode]["nb_reglement_patient"]++;
    }
    else {
      $recapReglement["total"]["du_tiers"] += $_reglement->montant;
      $recapReglement["total"]["nb_reglement_tiers"]++;
      $recapReglement[$_reglement->mode]["du_tiers"] += $_reglement->montant;
      $recapReglement[$_reglement->mode]["nb_reglement_tiers"]++;
    }
    
    if (!array_key_exists($_reglement->_ref_object->_id, $listConsults)) {
      if ($_reglement->object_class=="CConsultation") {
        $recapReglement["total"]["secteur1"] += $_reglement->_ref_object->secteur1;
        $recapReglement["total"]["secteur2"] += $_reglement->_ref_object->secteur2;
      }
      else {
        $_reglement->_ref_object->loadRefsReglements();
        $recapReglement["total"]["secteur1"] += $_reglement->_ref_object->_montant_avec_remise;
      }
      
      $listConsults[$_reglement->_ref_object->_id] = $_reglement->_ref_object;
    }
    if (!isset($listReglements[mbDate(null, $_reglement->date)])) {
      $listReglements[mbDate(null, $_reglement->date)]["total"]["patient"] = 0;
      $listReglements[mbDate(null, $_reglement->date)]["total"]["tiers"] = 0;
      $listReglements[mbDate(null, $_reglement->date)]["total"]["total"] = 0;
    }
    
    $listReglements[mbDate(null, $_reglement->date)]["total"][$_reglement->emetteur] += $_reglement->montant;
    $listReglements[mbDate(null, $_reglement->date)]["total"]["total"] += $_reglement->montant;
    
    if ($prat->_id && $_reglement->_ref_object->_ref_chir->_id != $prat->_id) {
      $_reglement->_ref_object->_ref_chir->_id = $prat->_id;
    }
    $listReglements[mbDate(null, $_reglement->date)]["reglements"][$_reglement->_id] = $_reglement;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , $today);
$smarty->assign("filter"             , $filter);
$smarty->assign("listPrat"           , $listPrat);
$smarty->assign("listReglements"     , $listReglements);
$smarty->assign("listConsults"       , $listConsults);
$smarty->assign("recapReglement"     , $recapReglement);

$smarty->display("print_compta.tpl");
?>