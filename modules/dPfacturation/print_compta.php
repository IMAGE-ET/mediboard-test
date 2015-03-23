<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();
// Récupération des paramètres
$filter = new CPlageconsult();
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_mode_reglement = CValue::getOrSession("mode", 0);
$filter->_type_affichage = CValue::getOrSession("_type_affichage" , 1);
$all_group_compta  = CValue::getOrSession("_all_group_compta" , 1);

// Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if ($filter->_type_affichage == "complete") {
  $filter->_type_affichage = 1;
}
if ($filter->_type_affichage == "totaux") {
  $filter->_type_affichage = 0;
}

$ljoin = array();
$where = array();
// Filtre sur les dates
$where["reglement.date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max 23:59:59'";

// Filtre sur les modes de paiement
if ($filter->_mode_reglement) {
  $where["reglement.mode"] = "= '$filter->_mode_reglement'";
}

// Filtre sur les praticiens
$chir_id = CValue::getOrSession("chir");
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

// Chargement des règlements via les factures
$ljoin["facture_cabinet"] = "reglement.object_id = facture_cabinet.facture_id";
if (!$all_group_compta) {
  $where["facture_cabinet.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
}
$where["facture_cabinet.praticien_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$where["reglement.object_class"] = " = 'CFactureCabinet'";

$reglement = new CReglement();
/** @var CReglement[] $reglements */
$reglements = $reglement->loadList($where, " facture_cabinet.facture_id, reglement.date", null, null, $ljoin);

$reglement = new CReglement();
// Calcul du récapitulatif
// Initialisation du tableau de reglements
$recapReglement["total"] = array(
  "nb_consultations"     => "0",
  "du_patient"           => "0",
  "du_tiers"             => "0",
  "nb_reglement_patient" => "0",
  "nb_reglement_tiers"   => "0",
  "secteur1"             => "0",
  "secteur2"             => "0",
  "secteur3"             => "0",
  "du_tva"               => "0"
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
$listConsults   = array();
$factures = CStoredObject::massLoadFwdRef($reglements, "object_id");
$patients = CStoredObject::massLoadFwdRef($factures, "patient_id");
CStoredObject::massCountBackRefs($factures, "notes");

foreach ($reglements as $_reglement) {
  $facture = $_reglement->loadRefFacture();

  $facture->loadRefsNotes();
  $facture->loadRefsConsultation();
  $facture->loadRefsReglements();

  if (count($facture->_ref_consults)) {
    if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
      foreach ($facture->_ref_consults as $_consult) {
        if (!array_key_exists($_consult->_id, $listConsults)) {
          $listConsults[$_consult->_id] = $_consult;
          $recapReglement["total"]["secteur1"] += $_consult->secteur1;
          $recapReglement["total"]["secteur2"] += $_consult->secteur2;
          $recapReglement["total"]["secteur3"] += $_consult->secteur3;
          $recapReglement["total"]["du_tva"] += $_consult->du_tva;
        }
      }
    }
    else {
      foreach ($facture->_ref_consults as $_consult) {
        $listConsults[$_consult->_id] = $_consult;
      }
      $recapReglement["total"]["secteur1"] += $facture->_montant_avec_remise;
    }
    $recapReglement["total"]["nb_consultations"] += count($facture->_ref_consults);

    if ($_reglement->emetteur == "patient") {
      $recapReglement["total"]["du_patient"] += $_reglement->montant;
      $recapReglement["total"]["nb_reglement_patient"]++;
      $recapReglement[$_reglement->mode]["du_patient"] += $_reglement->montant;
      $recapReglement[$_reglement->mode]["nb_reglement_patient"]++;
    }

    if ($_reglement->emetteur == "tiers") {
      $recapReglement["total"]["du_tiers"] += $_reglement->montant;
      $recapReglement["total"]["nb_reglement_tiers"]++;
      $recapReglement[$_reglement->mode]["du_tiers"] += $_reglement->montant;
      $recapReglement[$_reglement->mode]["nb_reglement_tiers"]++;
    }

    // Totaux par date
    $date = CMbDT::date($_reglement->date);
    if (!isset($listReglements[$date])) {
      $listReglements[$date]["total"]["patient"]  = 0;
      $listReglements[$date]["total"]["tiers"]    = 0;
      $listReglements[$date]["total"]["total"]    = 0;
    }

    $listReglements[$date]["total"][$_reglement->emetteur] += $_reglement->montant;
    $listReglements[$date]["total"]["total"]               += $_reglement->montant;
    $listReglements[$date]["reglements"][$_reglement->_id]  = $_reglement;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"           , CMbDT::date());
$smarty->assign("filter"          , $filter);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("listReglements"  , $listReglements);
$smarty->assign("listConsults"    , $listConsults);
$smarty->assign("recapReglement"  , $recapReglement);
$smarty->assign("reglement"       , $reglement);

$smarty->display("print_compta.tpl");
?>