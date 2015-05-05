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
$filter->_etat_reglement_patient = CValue::getOrSession("_etat_reglement_patient");
$filter->_etat_reglement_tiers   = CValue::getOrSession("_etat_reglement_tiers");
$filter->_mode_reglement = CValue::getOrSession("mode", 0);
$filter->_type_affichage = CValue::getOrSession("_type_affichage" , 1);
$all_group_money  = CValue::getOrSession("_all_group_money" , 1);
$all_impayes      = CValue::get("all_impayes" , 0);

// Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if ($filter->_type_affichage == "complete") {
  $filter->_type_affichage = 1;
}
elseif ($filter->_type_affichage == "totaux") {
  $filter->_type_affichage = 0;
}

$where = array();
$ljoin = array();

// Plage ciblée
if ($plage_id = CValue::get("plage_id")) {
  // Contraintes sur les plages de consultation
  $ljoin["facture_liaison"] = "facture_liaison.facture_id = facture_cabinet.facture_id";
  $ljoin["consultation"] = "facture_liaison.object_id = consultation.consultation_id";
  $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
  
  $where["facture_liaison.facture_class"] = " = 'CFactureCabinet'";
  $where["facture_liaison.object_class"] = " = 'CConsultation'";
  $where["plageconsult.plageconsult_id"] = " = '$plage_id'";
}
// Tri sur les dates
else {
  $where["ouverture"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
}

// Consultations gratuites
if (!CValue::getOrSession("cs")) {
  $where[] = "du_patient + du_tiers > 0";
}

// Filtre sur les praticiens
$chir_id = CValue::getOrSession("chir");
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

$where["praticien_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));

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
  "secteur2"             => "0",
  "secteur3"             => "0",
  "du_tva"               => "0"
);

foreach (array_merge($reglement->_specs["mode"]->_list, array("")) as $_mode) {
  $recapReglement[$_mode] = array(
    "du_patient"           => "0",
    "du_tiers"             => "0",
    "nb_reglement_patient" => "0",
    "nb_reglement_tiers"   => "0"
  );
}

// Etat des règlements
if ($all_impayes) {
  $where[] = "(patient_date_reglement IS NULL AND du_patient > 0)
    || (tiers_date_reglement IS NULL AND du_tiers > 0)";
}
else {
  if ($filter->_etat_reglement_patient == "reglee") {
    $where["patient_date_reglement"] = "IS NOT NULL";
  }
  if ($filter->_etat_reglement_patient == "non_reglee") {
    $where["patient_date_reglement"] = "IS NULL";
    $where["du_patient"] = "> 0";
  }

  if ($filter->_etat_reglement_tiers == "reglee") {
    $where["tiers_date_reglement"] = "IS NOT NULL";
  }
  if ($filter->_etat_reglement_tiers == "non_reglee") {
    $where["tiers_date_reglement"] = "IS NULL";
    $where["du_tiers"] = "> 0";
  }
}

// Reglements via les factures de consultation
$where["cloture"]    = "IS NOT NULL";
$where["patient_id"] = "IS NOT NULL";
$order = "ouverture, praticien_id";

$facture = new CFactureCabinet();
if ($all_group_money) {
  $listFactures = $facture->loadList($where, $order, null, null, $ljoin);
}
else {
 $listFactures = $facture->loadGroupList($where, $order, null, null, $ljoin);
}

$listPlages = array();
CMbObject::massLoadFwdRef($listFactures, "praticien_id");
CMbObject::massLoadFwdRef($listFactures, "patient_id");
CMbObject::massCountBackRefs($listFactures, "reglements");
CMbObject::massCountBackRefs($listFactures, "notes");

foreach ($listFactures as $_facture) {
  /* @var CFactureCabinet $_facture */
  $_facture->loadRefGroup();
  $_facture->loadRefPatient();
  $_facture->loadRefPraticien();
  $_facture->loadRefsConsultation();

  if (count($_facture->_ref_consults)) {

    $_facture->loadRefCoeffFacture();
    $_facture->updateMontants();
    $_facture->loadRefsReglements();
    $_facture->loadRefsNotes();
    if ($all_impayes) {
      $_facture->loadRefsRelances();
    }
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

    $recapReglement["total"]["nb_consultations"] += count($_facture->_ref_consults);

    $recapReglement["total"]["du_patient"]      += $_facture->_reglements_total_patient;
    $recapReglement["total"]["du_tva"]          += $_facture->du_tva;
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
    if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
      $recapReglement["total"]["secteur1"]             += $_facture->_secteur1;
      $recapReglement["total"]["secteur2"]             += $_facture->_secteur2;
      $recapReglement["total"]["secteur3"]             += $_facture->_secteur3;
    }
    else {
      $recapReglement["total"]["secteur1"]             += $_facture->_montant_avec_remise;
    }

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
    $plage->loadRefsFwd();
    if ($_facture->_ref_last_consult->_id) {
      $debut_plage = "$plage->date $plage->debut";
      if (!isset($listPlages["$debut_plage"])) {
        $listPlages["$debut_plage"]["plage"] = $plage;
        $listPlages["$debut_plage"]["total"]["secteur1"] = 0;
        $listPlages["$debut_plage"]["total"]["secteur2"] = 0;
        $listPlages["$debut_plage"]["total"]["secteur3"] = 0;
        $listPlages["$debut_plage"]["total"]["du_tva"]   = 0;
        $listPlages["$debut_plage"]["total"]["total"]    = 0;
        $listPlages["$debut_plage"]["total"]["patient"]  = 0;
        $listPlages["$debut_plage"]["total"]["tiers"]    = 0;
      }

      $listPlages["$debut_plage"]["factures"][$_facture->_guid] = $_facture;
      $listPlages["$debut_plage"]["total"]["secteur1"] += $_facture->_secteur1;

      if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
        $listPlages["$debut_plage"]["total"]["secteur2"] += $_facture->_secteur2;
      }
      else {
        $listPlages["$debut_plage"]["total"]["secteur2"] += $_facture->remise;
      }
      $listPlages["$debut_plage"]["total"]["secteur3"] += $_facture->_secteur3;
      $listPlages["$debut_plage"]["total"]["du_tva"]   += $_facture->du_tva;
      $listPlages["$debut_plage"]["total"]["total"]    += $_facture->_montant_avec_remise;
      $listPlages["$debut_plage"]["total"]["patient"]  += $_facture->_reglements_total_patient;
      $listPlages["$debut_plage"]["total"]["tiers"]    += $_facture->_reglements_total_tiers;
    }
  }
}

// Chargement des banques
$banque = new CBanque();
$banques = $banque->loadList(null, "nom ASC");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"         , CMbDT::date());
$smarty->assign("filter"        , $filter);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("recapReglement", $recapReglement);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("banques"       , $banques);
$smarty->assign("all_impayes"   , $all_impayes);

$smarty->display("print_rapport.tpl");