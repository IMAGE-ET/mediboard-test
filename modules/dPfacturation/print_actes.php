 <?php 
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision:$
 */

CCanDo::checkEdit();
// Récupération des paramètres
$date   = CValue::get("date");
$filter = new CPlageconsult();
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_type_affichage = CValue::getOrSession("_type_affichage" , 1);

// Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if ($filter->_type_affichage == "complete") {
  $filter->_type_affichage = 1;
}
elseif ($filter->_type_affichage == "totaux") {
  $filter->_type_affichage = 0;
}
$ljoin = array();
$where = array();
$where["ouverture"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

// Consultations gratuites
if (!CValue::getOrSession("cs")) {
  $where[] = "du_patient + du_tiers > 0";
}

if ($date) {
//CSQLDataSource::$trace = true;
  $ljoin["facture_liaison"] = "facture_liaison.facture_id = facture_etablissement.facture_id";
  $ljoin["sejour"] = "facture_liaison.object_id = sejour.sejour_id";
  
  $where["facture_liaison.facture_class"] = " = 'CFactureEtablissement'";
  $where["facture_liaison.object_class"] = " = 'CSejour'";
  $where["sejour.sortie"] = " LIKE '%$date%'";
}

// Tri sur les praticiens
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();
$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
$prat->loadRefFunction();
$listPrat = ($prat->_id) ? array($prat->_id => $prat) : $listPrat = $mediuser->loadPraticiensCompta();
$where["facture_etablissement.praticien_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));

// Initialisation du tableau de reglements
$reglement = new CReglement();
$recapReglement["total"]      = array(
  "nb_sejours"           => "0",
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

// Reglements via les factures d'établissement
$where["cloture"]    = "IS NOT NULL";
$where["facture_etablissement.patient_id"] = "IS NOT NULL";
$order = "ouverture, praticien_id";

//mbTrace(count($where));
$facture = new CFactureEtablissement();
$listFactures = $facture->loadList($where, $order, null, null, $ljoin);
//mbTrace(count($listFactures));
$listPlages = array();
foreach ($listFactures as $_facture) {
  $_facture->loadRefs();
  $_facture->loadRefsNotes();
  
  // Ajout de reglements
  $_facture->_new_reglement_patient = new CReglement();
  $_facture->_new_reglement_patient->setObject($_facture);
  $_facture->_new_reglement_patient->emetteur = "patient";
  $_facture->_new_reglement_patient->montant = $_facture->_du_restant_patient;
  
  $_facture->_new_reglement_tiers = new CReglement();
  $_facture->_new_reglement_tiers->setObject($_facture);
  $_facture->_new_reglement_tiers->emetteur = "tiers";
  $_facture->_new_reglement_tiers->mode     = "virement";
  $_facture->_new_reglement_tiers->montant  = $_facture->_du_restant_tiers;
  
  $recapReglement["total"]["nb_sejours"] += count($_facture->_ref_sejours);
  
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
  if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
    $recapReglement["total"]["secteur1"]             += $_facture->_secteur1;
    $recapReglement["total"]["secteur2"]             += $_facture->_secteur2;
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
  $plage = $_facture->_ref_last_sejour;
  if ($plage->_id) {
    $debut_plage = CMbDT::date($plage->sortie);
    if (!isset($listPlages["$debut_plage"])) {
      $listPlages["$debut_plage"]["plage"] = $plage;
      $listPlages["$debut_plage"]["total"]["secteur1"] = 0;
      $listPlages["$debut_plage"]["total"]["secteur2"] = 0;
      $listPlages["$debut_plage"]["total"]["total"]    = 0;
      $listPlages["$debut_plage"]["total"]["patient"]  = 0;
      $listPlages["$debut_plage"]["total"]["tiers"]    = 0;
    }
    
    $listPlages["$debut_plage"]["factures"][$_facture->_guid] = $_facture;
    if (CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
      $listPlages["$debut_plage"]["total"]["secteur1"] += $_facture->_secteur1;
      $listPlages["$debut_plage"]["total"]["secteur2"] += $_facture->_secteur2;
    }
    else {
      $listPlages["$debut_plage"]["total"]["secteur1"] += $_facture->_montant_sans_remise;
      $listPlages["$debut_plage"]["total"]["secteur2"] += $_facture->remise;
    }
    $listPlages["$debut_plage"]["total"]["total"]    += $_facture->_montant_avec_remise;
    $listPlages["$debut_plage"]["total"]["patient"]  += $_facture->_reglements_total_patient;
    $listPlages["$debut_plage"]["total"]["tiers"]    += $_facture->_reglements_total_tiers;
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

$smarty->display("print_actes.tpl");

?>