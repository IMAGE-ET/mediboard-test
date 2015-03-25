<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $m;

// Redirection pour gérer le cas ou le volet par defaut est l'autre affichage des sejours
if (CAppUI::pref("vue_sejours") == "global" && $m == "soins") {
  CAppUI::redirect("m=soins&tab=vw_sejours");
}

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

CCanDo::checkRead();
$group = CGroups::loadCurrent();

// Filtres
$date           = CValue::getOrSession("date");
$datetime       = CMbDT::dateTime();
$mode           = CValue::getOrSession("mode", 0);
$service_id     = CValue::getOrSession("service_id");
$praticien_id   = CValue::getOrSession("praticien_id");
$_active_tab    = CValue::get("_active_tab");
$type_admission = CValue::getOrSession("type");
$my_patient     = CValue::getOrSession("my_patient");

// récuperation du service par défaut dans les préférences utilisateur
$default_services_id = CAppUI::pref("default_services_id");

if (!$default_services_id) {
  $default_services_id = "{}";
}

$group_id = CGroups::loadCurrent()->_id;

// Récuperation du service à afficher par défaut (on prend le premier s'il y en a plusieurs)
$default_service_id = "";

$default_services_id = json_decode($default_services_id);
if (isset($default_services_id->{"g$group_id"})) {
  $default_service_id = reset(explode("|", $default_services_id->{"g$group_id"}));
}

if (!$service_id && $default_service_id && !$praticien_id) {
  $service_id = $default_service_id;
}

if (!$date) {
  $date = CMbDT::date();
}

$tab_sejour = array();

// Chargement de l'utilisateur courant
$userCourant = CMediusers::get();

$_is_praticien = $userCourant->isPraticien();
$_is_anesth    = $userCourant->isAnesth();

// Preselection du praticien_id
if ($_is_praticien && !$service_id && !$praticien_id) {
  $praticien_id = $userCourant->user_id;
}

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();

if ($_is_praticien) {
  $services = $service->loadGroupList($where);
}
else {
  $services = $service->loadListWithPerms(PERM_READ, $where);
}
  
$changeSejour = CValue::get("service_id") || CValue::get("praticien_id");
$changeSejour = $changeSejour || (!$service_id && !$praticien_id);

if ($changeSejour) {
  $sejour_id = null;
  CValue::setSession("sejour_id");
}
else {
  $sejour_id = CValue::getOrSession("sejour_id");
}  

// Récupération du service à ajouter/éditer
$totalLits = 0;

// A passer en variable de configuration
$heureLimit = "16:00:00";

// Initialisation
$service = new CService();
$groupSejourNonAffectes = array();
$sejoursParService = array();

// Chargement de la liste de praticiens
$prat = new CMediusers();
$praticiens = $prat->loadPraticiens(PERM_READ);

// Restructuration minimal des services
global $sejoursParService;
$sejoursParService = array();
$count_my_patient = 0;

// Chargement du praticien
if ($praticien_id) {
  $praticien = new CMediusers();
  $praticien->load($praticien_id);
}

$anesth = new CMediusers();
$anesthesistes = array_keys($anesth->loadAnesthesistes());

// Si seulement le praticien est indiqué
if ($praticien_id && !$service_id) {
  $sejours = array();
  $sejour = new CSejour();
  $where = array();
  $where["group_id"] = "= '$group->_id'";

  if ($praticien->isAnesth()) {
    $ljoin = array();
    $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
    $ljoin["plagesop"]   = "operations.plageop_id = plagesop.plageop_id";
    $where[] = "operations.anesth_id = '$praticien_id' OR (operations.anesth_id IS NULL AND plagesop.anesth_id = '$praticien_id')
                OR praticien_id = '$praticien_id'";
  }
  else {
    $where["praticien_id"] = " = '$praticien_id'";
  }
  
  $where["entree"] = " <= '$date 23:59:59'";
  $where["sortie"] = " >= '$date 00:00:00'";
  $where["annule"] = " = '0'";
  $where[] = $type_admission ? "type = '$type_admission'" : "type != 'urg' AND type != 'exte'";
  
  if ($praticien->isAnesth()) {
    $sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);
  }
  else {
    $sejours = $sejour->loadList($where);
  }

  foreach ($sejours as $_sejour) {
    $count_my_patient += count($_sejour->loadRefsUserSejour($userCourant));
    /* @var CSejour $_sejour*/
    if ($_is_anesth || ($_is_praticien && $_sejour->praticien_id == $userCourant->user_id)) {
      $tab_sejour[$_sejour->_id]= $_sejour;
    }
    $affectations = array();
    $affectation = new CAffectation();
    $where = array();
    $where["affectation.sejour_id"] = " = '$_sejour->_id'";
    $where["affectation.entree"] = "<= '$date 23:59:59'";
    $where["affectation.sortie"] = ">= '$date 00:00:00'";
    $ljoin = array();
    $complement = "";
    if ($date == CMbDT::date()) {
      $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
      $complement = "OR (sejour.sortie_reelle >= '".CMbDT::dateTime()."' AND affectation.sortie >= '".CMbDT::dateTime()."')";
    }
    $where[] = "affectation.effectue = '0' $complement";
    $affectations = $affectation->loadList($where, null, null, null, $ljoin);

    if (count($affectations) >= 1) {
      foreach ($affectations as $_affectation) {
        /* @var CAffectation $_affectation*/
        $_affectation->loadRefsAffectations();
        cacheLit($_affectation);
      }
    }
    else {
      $_sejour->loadRefsPrescriptions();
      $_sejour->loadRefPatient();
      $_sejour->loadRefPraticien();
      $_sejour->_ref_praticien->loadRefFunction();
      $_sejour->loadNDA();
      $sejoursParService["NP"][$_sejour->_id] = $_sejour;
    }
  }
}

foreach ($sejoursParService as $key => $_service) {
  if ($key != "NP") {
    $sorter = CMbArray::pluck($_service->_ref_chambres, "nom");
    array_multisort($sorter, SORT_ASC, $_service->_ref_chambres);
  
    foreach ($_service->_ref_chambres as $_chambre) {
      foreach ($_chambre->_ref_lits as $_lit) {
        foreach ($_lit->_ref_affectations as $_affectation) {
          $_affectation->loadRefsAffectations();
          $_affectation->loadRefSejour();
          $_sejour = $_affectation->_ref_sejour;
          if ($_is_anesth || ($_is_praticien && $_sejour->praticien_id == $userCourant->user_id)) {
            $tab_sejour[$_sejour->_id]= $_sejour;
          }
          $_sejour->loadRefsPrescriptions();
          $_sejour->loadRefPatient();
          $_sejour->loadRefPraticien();
          $_sejour->_ref_praticien->loadRefFunction();
          $_sejour->loadNDA();

          if ($_sejour->_ref_prescriptions) {
            if (array_key_exists('sejour', $_sejour->_ref_prescriptions)) {
               $prescription_sejour = $_sejour->_ref_prescriptions["sejour"];
               $prescription_sejour->countNoValideLines();
              if (@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
                $prescription_sejour->_count_alertes  = $prescription_sejour->countAlertsNotHandled("medium");
                $prescription_sejour->_count_urgences = $prescription_sejour->countAlertsNotHandled("high");
              }
              else {
                $prescription_sejour->countFastRecentModif();
              }
            }
          }
        }
      }
    }
  }
}

// Tri des sejours par services
ksort($sejoursParService);

// Récuperation du sejour sélectionné
$sejour = new CSejour;
$sejour->load($sejour_id);

if ($service_id) {
  // Chargement des séjours à afficher
  if ($service_id == "NP") {
    // Liste des patients à placer
    $order = "entree_prevue ASC";
      
    // Admissions de la veille
    $dayBefore = CMbDT::date("-1 days", $date);
    $where = array(
      "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );
      
    $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order, $praticien_id);
      
    // Admissions du matin
    $where = array(
      "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".CMbDT::time("-1 second", $heureLimit)."'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );
      
    $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order, $praticien_id);
      
    // Admissions du soir
    $where = array(
      "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );

    $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order, $praticien_id);

    // Admissions antérieures
    $twoDaysBefore = CMbDT::date("-2 days", $date);
    $where = array(
      "entree_prevue" => "<= '$twoDaysBefore 23:59:59'",
      "sortie_prevue" => ">= '$date 00:00:00'",
      //"'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue",
      "annule" => "= '0'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'"
    );

    $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order, $praticien_id);

    if ($_is_praticien || $_is_anesth) {
      foreach ($groupSejourNonAffectes as $sejours_by_moment) {
        foreach ($sejours_by_moment as $_sejour) {
          if (($_sejour->praticien_id == $userCourant->user_id) || $_is_anesth) {
            $tab_sejour[$_sejour->_id] = $_sejour;
          }
        }
      }
    }
  }
  else {
    $service->load($service_id);
    loadServiceComplet($service, $date, $mode, $praticien_id, $type_admission);
  }
  
  if ($service->_id) {
    foreach ($service->_ref_chambres as $_chambre) {
      foreach ($_chambre->_ref_lits as $_lits) {
        foreach ($_lits->_ref_affectations as $_affectation) {
          if ($_is_anesth || ($_is_praticien && $_affectation->_ref_sejour->praticien_id == $userCourant->user_id)) {
            $tab_sejour[$_affectation->_ref_sejour->_id]= $_affectation->_ref_sejour;
          }
          $sejour = $_affectation->_ref_sejour;
          $sejour->loadRefsPrescriptions();
          $sejour->_ref_praticien->loadRefFunction();
          $count_my_patient += count($sejour->loadRefsUserSejour($userCourant));

          $_affectation->loadRefsAffectations();
          if ($_affectation->_ref_sejour->_ref_prescriptions) {
            if (array_key_exists('sejour', $_affectation->_ref_sejour->_ref_prescriptions)) {
              $prescription_sejour = $_affectation->_ref_sejour->_ref_prescriptions["sejour"];
              $prescription_sejour->countNoValideLines();
              if (@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
                $prescription_sejour->_count_alertes  = $prescription_sejour->countAlertsNotHandled("medium");
                $prescription_sejour->_count_urgences = $prescription_sejour->countAlertsNotHandled("high");
              }
              else {
                $prescription_sejour->countFastRecentModif();
              }
            }
          }
        }
      }
    }

    $service->loadRefsAffectationsCouloir($date, $mode, true);

    foreach ($service->_ref_affectations_couloir as $_affectation) {
      $_affectation->loadRefSejour();

      if ($_is_anesth || ($_is_praticien && $_affectation->_ref_sejour->praticien_id == $userCourant->user_id)) {
        $tab_sejour[$_affectation->_ref_sejour->_id]= $_affectation->_ref_sejour;
      }
      $sejour = $_affectation->_ref_sejour;
      $sejour->loadRefPraticien();
      $sejour->loadRefPatient();
      $sejour->loadRefsPrescriptions();
      $sejour->_ref_praticien->loadRefFunction();
      $count_my_patient += count($sejour->loadRefsUserSejour($userCourant));

      $_affectation->loadRefsAffectations();
      if ($_affectation->_ref_sejour->_ref_prescriptions) {
        if (array_key_exists('sejour', $_affectation->_ref_sejour->_ref_prescriptions)) {
          $prescription_sejour = $_affectation->_ref_sejour->_ref_prescriptions["sejour"];
          $prescription_sejour->countNoValideLines();
          if (@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
            $prescription_sejour->_count_alertes  = $prescription_sejour->countAlertsNotHandled("medium");
            $prescription_sejour->_count_urgences = $prescription_sejour->countAlertsNotHandled("high");
          }
          else {
            $prescription_sejour->countFastRecentModif();
          }
        }
      }
    }
  }
  $sejoursParService[$service->_id] = $service;
}

if ($count_my_patient && $my_patient && ($userCourant->isSageFemme() || $userCourant->isAideSoignant() || $userCourant->isInfirmiere())) {
  foreach ($sejoursParService as $key => $_service) {
    if ($key != "NP") {
      foreach ($_service->_ref_chambres as $key_chambre => $_chambre) {
        foreach ($_chambre->_ref_lits as $key_lit => $_lit) {
          foreach ($_lit->_ref_affectations as $key_affectation => $_affectation) {
            $_sejour = $_affectation->loadRefSejour();
            if (!count($_sejour->_ref_users_sejour)) {
              unset($_lit->_ref_affectations[$key_affectation]);
            }
          }
        }
      }
    }
    else {
      foreach ($_service as $_sejour) {
        if (!count($_sejour->_ref_users_sejour)) {
          unset($sejoursParService[$key][$_sejour->_id]);
        }
      }
    }
  }
}
if (!$count_my_patient && $my_patient) {
  $my_patient = 0;
}


// Chargement des visites pour les séjours courants
$visites = array(
  "effectuee" => array(), 
  "non_effectuee" => array()
);

if (count($tab_sejour)) {
  foreach ($tab_sejour as $_sejour) {
    if ($_sejour->countNotificationVisite($date, $userCourant)) {
      $visites["effectuee"][] = $_sejour->_id;
    }
    else {
      $visites["non_effectuee"][] = $_sejour->_id; 
    }
  }
}

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $userCourant->isFromType(array("Infirmière"));

$sejour->type = $type_admission;

/**
 * Mettre en cache les lits
 *
 * @param CAffectation $affectation Affectation
 *
 * @return void
 */
function cacheLit(CAffectation $affectation) {
  // Cache des lits
  $lit_id = $affectation->lit_id;
  static $lits = array();
  if (!array_key_exists($lit_id, $lits)) {
    $lit = new CLit();
    $lit->load($lit_id);
    $lits[$lit_id] = $lit;
  }

  $lit = $lits[$lit_id];
  $lit->_ref_affectations[$affectation->_id] = $affectation;

  // Cache des chambres
  $chambre_id = $lit->chambre_id;
  static $chambres = array();
  if (!array_key_exists($chambre_id, $chambres)) {
    $chambre = new CChambre();
    $chambre->load($chambre_id);
    $chambres[$chambre_id] = $chambre;
  }

  $chambre = $chambres[$chambre_id];
  $chambre->_ref_lits[$lit_id] = $lit;

  // Cache de services
  global $sejoursParService;
  $service_id = $chambre->service_id;
  if (!array_key_exists($service_id, $sejoursParService)) {
    $service = new CService();
    $service->load($service_id);
    $sejoursParService[$service_id] = $service;
  }

  $service = $sejoursParService[$service_id];
  $service->_ref_chambres[$chambre_id] = $chambre;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("default_service_id"      , $default_service_id);
$smarty->assign("_active_tab"             , $_active_tab);
$smarty->assign("_is_praticien"           , $_is_praticien);
$smarty->assign("anesthesistes"           , $anesthesistes);
$smarty->assign("praticiens"              , $praticiens);
$smarty->assign("praticien_id"            , $praticien_id);
$smarty->assign("object"                  , $sejour);
$smarty->assign("mode"                    , $mode);
$smarty->assign("totalLits"               , $totalLits);
$smarty->assign("date"                    , $date);
$smarty->assign("isImedsInstalled"        , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("demain"                  , CMbDT::date("+ 1 day", $date));
$smarty->assign("services"                , $services);
$smarty->assign("sejoursParService"       , $sejoursParService);
$smarty->assign("service_id"              , $service_id);
$smarty->assign("groupSejourNonAffectes"  , $groupSejourNonAffectes);
$smarty->assign("visites"                 , $visites);
$smarty->assign("my_patient"              , $my_patient);
$smarty->assign("count_my_patient"        , $count_my_patient);

$smarty->display("vw_idx_sejour.tpl");
