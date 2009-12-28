<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $g;

CAppUI::requireModuleFile($m, "inc_vw_affectations");

$pathos = new CDiscipline();

$can->needsRead();
$ds = CSQLDataSource::get("std");

// A passer en variable de configuration
$heureLimit = "16:00:00";

$date      = CValue::getOrSession("date", mbDate()); 
$mode      = CValue::getOrSession("mode", 0); 
$filterAdm = CValue::getOrSession("filterAdm", "tout");
$triAdm    = CValue::getOrSession("triAdm", "praticien");

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

// Chargment des services
foreach ($services as &$service) {
  loadServiceComplet($service, $date, $mode);
  $totalLits += $service->_nb_lits_dispo;
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);
$where = array(
  "type" => "NOT IN ('exte', 'urg', 'seances')",
  "annule" => "= '0'"
);
$where[] = "IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`) BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
$where[] = "affectation.affectation_id IS NULL";

$leftjoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, null, null, $leftjoin);

// Liste des patients à placer
$groupSejourNonAffectes = array();

if ($can->edit) {
  switch ($triAdm) {
    case "date_entree" :
      $orderTri = "entree_prevue ASC";
      break;
    case "praticien" :  
      $orderTri = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
      break;
  }
  
  switch ($filterAdm) {
    case "ambu" :
      $whereFilter = "type = 'ambu'";
      break;
    case "csejour" :
      $whereFilter = "HOUR(TIMEDIFF(sejour.sortie_prevue, sejour.entree_prevue)) <= 48";
      break;
    case "comp" :
      $whereFilter = "type = 'comp'";
      break;
    default :
      $whereFilter = "1 = 1";
  }
  
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
  $where = array(
    "type" => "NOT IN ('exte', 'urg', 'seances')",
    "annule" => "= '0'"
  );
  $where[] = "IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`) BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
  $where[] = $whereFilter;
  $order = $orderTri;
  
  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
  
  // Admissions du matin
  $where = array(
    "type" => "NOT IN ('exte', 'urg', 'seances')",
    "annule" => "= '0'"
  );
  $where[] = "IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`) BETWEEN '$date 02:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'";
  $where[] = $whereFilter;
  $order = $orderTri;
  
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
  
  // Admissions du soir
  $where = array(
    "type" => "NOT IN ('exte', 'urg', 'seances')",
    "annule" => "= '0'"
  );  
  $where[] = "IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`) BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
  $where[] = $whereFilter;
  $order = $orderTri;
  
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where = array(
    "annule" => "= '0'",
    "type" => "NOT IN ('exte', 'urg', 'seances')",
  );
  $where[] = "IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`) <= '$twoDaysBefore 23:59:59'";
  $where[] = "IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`) >= '$date 00:00:00'";
  $where[] = $whereFilter;
  $order = $orderTri;
  
  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
}

$affectation = new CAffectation();
$affectation->entree = mbAddDateTime("08:00:00",$date);
$affectation->sortie = mbAddDateTime("23:00:00",$date);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation"           , $affectation);
$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date );
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("filterAdm"             , $filterAdm);
$smarty->assign("triAdm"                , $triAdm);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);

$smarty->display("vw_affectations.tpl");
?>