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

$date            = CValue::getOrSession("date", mbDate()); 
$mode            = CValue::getOrSession("mode", 0); 
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$list_services   = CValue::getOrSession("list_services");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filterFunction  = CValue::getOrSession("filterFunction");

$emptySejour = new CSejour;
$emptySejour->_type_admission = $_type_admission;

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

global $phpChrono;

// Chargment des services
foreach ($services as &$service) {
  if (!in_array($service->_id, $list_services)){
    continue;
  }
	
  loadServiceComplet($service, $date, $mode);
	$phpChrono->stop("Load Service Complet : '$service->_view'");
  $phpChrono->start();
  $totalLits += $service->_nb_lits_dispo;
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);

$where["annule"]          = "= '0'";
$where["sejour.entree"]   = "BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
if($_type_admission == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($_type_admission) {
  $where["sejour.type"] = " = '$_type_admission'";
} else {
  $where["sejour.type"] = "!= 'urg'";
}
$where[]                  = "affectation.affectation_id IS NULL";
$where[]                  = $where_service;
$leftjoin["affectation"]  = "sejour.sejour_id = affectation.sejour_id";

// Filtre sur les fonctions
if($filterFunction){
	$leftjoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
  $where["users_mediboard.function_id"] = " = '$filterFunction'";
}

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, null, null, $leftjoin);
$phpChrono->stop("Patient à placer dans la semaine");
$phpChrono->start();

// Liste des patients à placer
$groupSejourNonAffectes = array();

if ($can->edit) {
	$where = array();
	$where["sejour.annule"] = "= '0'";
  $where[] = $where_service;
	
  switch ($triAdm) {
    case "date_entree" :
      $order = "entree_prevue ASC";
      break;
    case "praticien" :  
      $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
      break;
  }
  
  switch ($_type_admission) {
    case "ambucomp" :
      $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp'";
      break;
    case "0" :
    	break;
    default :
    	$where["sejour.type"] = "= '$_type_admission'"; 
  }
  
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
	$where["sejour.entree"] = "BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
	$phpChrono->stop("Non affectés: veille");
	$phpChrono->start();
  
  // Admissions du matin
  $where["sejour.entree"] = "BETWEEN '$date 02:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'";
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affectés: matin");
  $phpChrono->start();
  
  // Admissions du soir
  $where["sejour.entree"] = "BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affectés: soir");
  $phpChrono->start();
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where["sejour.entree"] = "<= '$twoDaysBefore 23:59:59'";
  $where["sejour.sortie"] = ">= '$date 00:00:00'";

  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affectés: avant");
  $phpChrono->start();
	
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
$smarty->assign("emptySejour"           , $emptySejour);
$smarty->assign("filterFunction"        , $filterFunction);
$smarty->assign("triAdm"                , $triAdm);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->assign("functions_filter"      , $functions_filter);

$smarty->display("vw_affectations.tpl");

if (CAppUI::pref("INFOSYSTEM")) {
  mbTrace(CMbArray::pluck($phpChrono->report, "total"), "Rapport uniquement visible avec les informations système");
}


?>