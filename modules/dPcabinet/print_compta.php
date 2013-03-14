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

$today = CMbDT::date();

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_mode_reglement = CValue::getOrSession("mode", 0);
$filter->_type_affichage  = CValue::getOrSession("_type_affichage" , 1);

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
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();
$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
$prat->loadRefFunction();
$listPrat = ($prat->_id) ? 
  array($prat->_id => $prat) :
  $listPrat = $mediuser->loadPraticiensCompta();
$where[] = "plageconsult.chir_id ".CSQLDataSource::prepareIn(array_keys($listPrat)).
    " OR plageconsult.pour_compte_id ".CSQLDataSource::prepareIn(array_keys($listPrat));
  
CSQLDataSource::$trace = false;

// Chargement des règlements via les consultations
$ljoin["consultation"] = "reglement.object_id = consultation.consultation_id";
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where["object_class"] = " = 'CConsultation'";

$reglement = new CReglement();
$reglements_consult = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);

// Chargement des règlements via les factures
$ljoin["consultation"] = "reglement.object_id = consultation.facture_id";
$where["object_class"] = " = 'CFactureCabinet'";

$reglement = new CReglement();
$reglements_facture = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);

$reglements = array_merge($reglements_consult, $reglements_facture);

// Calcul du récapitulatif
// Initialisation du tableau de reglements
$recapReglement["total"] = array(
  "du_patient"           => "0",
  "du_tiers"             => "0",
  "nb_reglement_patient" => "0",
  "nb_reglement_tiers"   => "0",
  "secteur1"             => "0",
  "secteur2"             => "0",
  "avec_remise"          => "0",
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
foreach ($reglements as $_reglement) {
  $facture = $_reglement->loadRefFacture();
  $facture->loadRefsNotes();
  $facture->_ref_praticien->loadRefFunction();
  foreach ($facture->_ref_consults as $_consult) {
    // Chargement des consultations
    $_consult->loadRefPatient(1);
    $_consult->loadRefPlageConsult(1);
    $_consult->loadRefPraticien();
    $_consult->loadRefsReglements();

    // Bilan des secteur 1 et 2 des consultations
    if (!array_key_exists($_consult->_id, $listConsults)) {
      $listConsults[$_consult->_id] = $_consult;
      $recapReglement["total"]["secteur1"] += $_consult->secteur1;
      $recapReglement["total"]["secteur2"] += $_consult->secteur2;
    }
  }

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
  
  $facture = $_reglement->_ref_object;
  if ($facture instanceof CFactureCabinet) {
    $facture->loadRefsReglements();
    $recapReglement["total"]["avec_remise"] += $facture->_montant_avec_remise;
  }
  
  // Totaux par date
  $date = CMbDT::date($_reglement->date);
  if (!isset($listReglements[$date])) {
    $listReglements[$date]["total"]["patient"] = 0;
    $listReglements[$date]["total"]["tiers"] = 0;
    $listReglements[$date]["total"]["total"] = 0;
  }
  
  $listReglements[$date]["total"][$_reglement->emetteur] += $_reglement->montant;
  $listReglements[$date]["total"]["total"] += $_reglement->montant;
  $listReglements[$date]["reglements"][$_reglement->_id] = $_reglement;
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