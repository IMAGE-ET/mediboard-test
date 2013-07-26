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

// !! Attention, r�gression importante si ajout de type de paiement

CCanDo::checkEdit();
$today = CMbDT::date();

// R�cup�ration des param�tres
$filter = new CPlageconsult();
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_etat_reglement_patient = CValue::getOrSession("_etat_reglement_patient");
$filter->_etat_reglement_tiers   = CValue::getOrSession("_etat_reglement_tiers");
$filter->_mode_reglement         = CValue::getOrSession("mode");

if ($filter->_mode_reglement == null) {
  $filter->_mode_reglement = 0;
}

$filter->_type_affichage = CValue::getOrSession("_type_affichage" , 1);
// Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if ($filter->_type_affichage == "complete") {
  $filter->_type_affichage = 1;
}
elseif ($filter->_type_affichage == "totaux") {
  $filter->_type_affichage = 0;
}

// Requ�te sur les consultations selon les crit�res
$consultation = new CConsultation();
$where = array();
$ljoin = array();

// Contraintes sur les plages de consultation
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

// Plage cibl�e
if ($plage_id = CValue::get("plage_id")) {
  $where["plageconsult.plageconsult_id"] = " = '$plage_id'";
}
// Tri sur les dates
else {
  $where["plageconsult.date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
}

// Consultations gratuites
if (!CValue::getOrSession("cs")) {
  $where[] = "consultation.secteur1 + consultation.secteur2 > 0";
}

$where["consultation.patient_id"] = "IS NOT NULL";

// Filtre sur les praticiens
$chir_id = CValue::getOrSession("chir");
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

$where[] = "plageconsult.chir_id ".CSQLDataSource::prepareIn(array_keys($listPrat))
    ." OR plageconsult.pour_compte_id ".CSQLDataSource::prepareIn(array_keys($listPrat));

$order = "plageconsult.date, plageconsult.debut, plageconsult.chir_id";

// Initialisation du tableau de reglements
$reglement = new CReglement();
$recapReglement["total"]      = array(
  "nb_consultations"     => "0",
  "reste_patient"        => "0",
  "reste_tiers"          => "0",
  "du_patient"           => "0",
  "du_tiers"             => "0",
  "nb_reglement_patient" => "0",
  "nb_reglement_tiers"   => "0",
  "nb_impayes_tiers"     => "0",
  "nb_impayes_patient"   => "0",
  "secteur1"             => "0",
  "secteur2"             => "0"
);

foreach (array_merge($reglement->_specs["mode"]->_list, array("")) as $_mode) {
  $recapReglement[$_mode] = array(
    "du_patient"           => "0",
    "du_tiers"             => "0",
    "nb_reglement_patient" => "0",
    "nb_reglement_tiers"   => "0"
  );
}

// Etat des r�glements
if ($filter->_etat_reglement_patient == "reglee") {
  $where["consultation.patient_date_reglement"] = "IS NOT NULL";
}
  
if ($filter->_etat_reglement_patient == "non_reglee") {
  $where["consultation.patient_date_reglement"] = "IS NULL";
  $where["consultation.du_patient"] = "> 0";
}

if ($filter->_etat_reglement_tiers == "reglee") {
  $where["consultation.tiers_date_reglement"] = "IS NOT NULL";
}

if ($filter->_etat_reglement_tiers == "non_reglee") {
  $where["consultation.tiers_date_reglement"] = "IS NULL";
  $where["consultation.du_tiers"] = "> 0";
}

// Reglements via les ***factures de consultation***
$ljoin = array();
$ljoin["consultation"] = "consultation.facture_id = facture_cabinet.facture_id";
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where["facture_cabinet.cloture"] = "IS NOT NULL";
$where["consultation.facture_id"] = "IS NOT NULL";

$facture = new CFactureCabinet();
/** @var CFactureCabinet[] $listFactures */
$listFactures = $facture->loadList($where, $order, null, null, $ljoin);

$factures = array();
$listPlages = array();

foreach ($listFactures as $_facture) {
  $_facture->loadRefPatient();
  $_facture->loadRefsConsultation();
  $_facture->loadRefsReglements();
  $_facture->loadRefsNotes();
  
  // Ajout de reglements
  $_facture->_new_reglement_patient = new CReglement();
  $_facture->_new_reglement_patient->setObject($_facture);
  $_facture->_new_reglement_patient->emetteur = "patient";
  $_facture->_new_reglement_patient->montant = $_facture->_du_restant_patient;
  $_facture->_new_reglement_tiers = new CReglement();
  $_facture->_new_reglement_tiers->setObject($_facture);
  $_facture->_new_reglement_tiers->emetteur = "tiers";
  $_facture->_new_reglement_tiers->mode = "virement";
  $_facture->_new_reglement_tiers->montant = $_facture->_du_restant_tiers;
  
  // Utiliser le GUID comme pour les consultations
  $factures[$_facture->_guid] = $_facture; 
}

foreach ($factures as $_facture) {
  $recapReglement["total"]["nb_consultations"] += count($_facture->_ref_consults);
  
  $recapReglement["total"]["du_patient"]      += $_facture->_reglements_total_patient;
  $recapReglement["total"]["reste_patient"]   += $_facture->_du_restant_patient;
  if ($_facture->_du_restant_patient) {
    $recapReglement["total"]["nb_impayes_patient"]++;
  }

  $recapReglement["total"]["du_tiers"]        += $_facture->_reglements_total_tiers;
  $recapReglement["total"]["reste_tiers"]     += $_facture->_du_restant_tiers;
  if ($_facture->_du_restant_tiers) {
    $recapReglement["total"]["nb_impayes_tiers"]++;
  }
  
  $recapReglement["total"]["nb_reglement_patient"] += count($_facture->_ref_reglements_patient);
  $recapReglement["total"]["nb_reglement_tiers"]   += count($_facture->_ref_reglements_tiers  );
  $recapReglement["total"]["secteur1"]             += $_facture->_secteur1;
  $recapReglement["total"]["secteur2"]             += $_facture->_secteur2;
  
  foreach ($_facture->_ref_reglements_patient as $_reglement) {
    $recapReglement[$_reglement->mode]["du_patient"]          += $_reglement->montant;
    $recapReglement[$_reglement->mode]["nb_reglement_patient"]++;
  }
  
  foreach ($_facture->_ref_reglements_tiers as $_reglement) {
    $recapReglement[$_reglement->mode]["du_tiers"]          += $_reglement->montant;
    $recapReglement[$_reglement->mode]["nb_reglement_tiers"]++;
  }
  
  // Classement par plage
  $plage = $_facture->_ref_last_consult->_ref_plageconsult;
  if (!isset($listPlages["$plage->date $plage->debut"])) {
    $listPlages["$plage->date $plage->debut"]["plage"] = $plage;
    $listPlages["$plage->date $plage->debut"]["total"]["secteur1"] = 0;
    $listPlages["$plage->date $plage->debut"]["total"]["secteur2"] = 0;
    $listPlages["$plage->date $plage->debut"]["total"]["total"]    = 0;
    $listPlages["$plage->date $plage->debut"]["total"]["patient"]  = 0;
    $listPlages["$plage->date $plage->debut"]["total"]["tiers"]    = 0;
  }
  
  $listPlages["$plage->date $plage->debut"]["factures"][$_facture->_guid] = $_facture;
  $listPlages["$plage->date $plage->debut"]["total"]["secteur1"] += $_facture->_secteur1;
  $listPlages["$plage->date $plage->debut"]["total"]["secteur2"] += $_facture->_secteur2;
  $listPlages["$plage->date $plage->debut"]["total"]["total"]    += $_facture->_montant_avec_remise;
  $listPlages["$plage->date $plage->debut"]["total"]["patient"]  += $_facture->_reglements_total_patient;
  $listPlages["$plage->date $plage->debut"]["total"]["tiers"]    += $_facture->_reglements_total_tiers;
}

// Chargement des banques
$banque = new CBanque();
$banques = $banque->loadList(null, "nom ASC");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("today"         , $today);
$smarty->assign("filter"        , $filter);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("recapReglement", $recapReglement);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("banques"       , $banques);

$smarty->display("print_rapport.tpl");
