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
$triAdm    = CValue::getOrSession("triAdm", "praticien");
$list_services    = CValue::getOrSession("list_services");
$filterAdm = CValue::getOrSession("filterAdm", "tout");
$filterFunction = CValue::getOrSession("filterFunction");

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

if(!$list_services){
  foreach($services as $_service){
    $list_services[] = $_service->_id;
  }
}

$where_service = "service_id IN (".join($list_services, ',').") OR service_id IS NULL"; 

// Chargment des services
foreach ($services as &$service) {
  if(!in_array($service->_id, $list_services)){
	continue;
  }
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
$where["sejour.entree"] = "BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
$where[] = "affectation.affectation_id IS NULL";
$where[] = $where_service;
$leftjoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

// Filtre sur les fonctions
if($filterFunction){
	$leftjoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
  $where["users_mediboard.function_id"] = " = '$filterFunction'";
}

// Filtre sur les types d'admission
if($filterAdm == "comp" || $filterAdm == "ambu"){
  $where["type"] = " = '$filterAdm'";
}
if($filterAdm == "csejour"){
  $where[] = "HOUR(TIMEDIFF(sejour.sortie_prevue, sejour.entree_prevue)) <= 48";
}

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, null, null, $leftjoin);

// Liste des patients à placer
$groupSejourNonAffectes = array();

if ($can->edit) {
  switch ($triAdm) {
    case "date_entree" :
      $order = "entree_prevue ASC";
      break;
    case "praticien" :  
      $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
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
	$where["sejour.entree"] = "BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
	$where[] = $whereFilter;
	$where[] = $where_service;
  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
  
  // Admissions du matin
  $where = array(
    "type" => "NOT IN ('exte', 'urg', 'seances')",
    "annule" => "= '0'"
  );
  $where["sejour.entree"] = "BETWEEN '$date 02:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'";
  $where[] = $whereFilter;
  $where[] = $where_service;
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
  
  // Admissions du soir
  $where = array(
    "type" => "NOT IN ('exte', 'urg', 'seances')",
    "annule" => "= '0'"
  );  
  $where["sejour.entree"] = "BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
  $where[] = $whereFilter;
  $where[] = $where_service;
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where = array(
    "annule" => "= '0'",
    "type" => "NOT IN ('exte', 'urg', 'seances')",
  );
  $where["sejour.entree"] = "<= '$twoDaysBefore 23:59:59'";
  $where["sejour.sortie"] = ">= '$date 00:00:00'";
  $where[] = $whereFilter;
  $where[] = $where_service;

  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
}

$functions_filter = array();
foreach($groupSejourNonAffectes as $_keyGroup => $_group) {
  foreach($_group as $_key => $_sejour) {
    $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
    if ($filterFunction && $filterFunction != $_sejour->_ref_praticien->function_id) {
      unset($groupSejourNonAffectes[$_keyGroup][$_key]);
    }
  }
}

$affectation = new CAffectation();
$affectation->entree = mbAddDateTime("08:00:00",$date);
$affectation->sortie = mbAddDateTime("23:00:00",$date);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_services"         , $list_services);
$smarty->assign("affectation"           , $affectation);
$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date);
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("filterAdm"             , $filterAdm);
$smarty->assign("filterFunction"        , $filterFunction);
$smarty->assign("triAdm"                , $triAdm);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->assign("functions_filter"      , $functions_filter);

$smarty->display("vw_affectations.tpl");
?>