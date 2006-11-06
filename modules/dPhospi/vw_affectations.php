<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

require_once($AppUI->getModuleFile($m, "inc_vw_affectations"));

global $pathos;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// A passer en variable de configuration
$heureLimit = "16:00:00";

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$mode = mbGetValueFromGetOrSession("mode", 0);

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ,$where);

// Chargment des services
$fullService = mbGetValueFromCookie("fullService");
foreach ($services as &$service) {
  $service->_vwService = !preg_match("/service$service->_id-trigger:triggerShow/i", $fullService);
  if ($service->_vwService) {
    loadServiceComplet($service, $date, $mode);
    $totalLits += $service->_nb_lits_dispo;
  } 
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);
$where = array(
  "entree_prevue" => "BETWEEN '$today' AND '$endWeek'",
  "type" => "!= 'exte'",
  "annule" => "= '0'"
);
$where["sejour.group_id"] = "= '$g'";
$where[] = "affectation.affectation_id IS NULL";

$leftjoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$select = "count(sejour.sejour_id) AS total";  
$table = "sejour";

$sql = new CRequest();
$sql->addTable($table);
$sql->addSelect($select);
$sql->addWhere($where);
$sql->addLJoin($leftjoin);

$alerte = db_loadResult($sql->getRequest());

$groupSejourNonAffectes = array();

if ($canEdit) {
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
  $where = array(
    "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );

  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where);
  
  // Admissions du matin
  $where = array(
    "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );
  
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where);
  
  // Admissions du soir
  $where = array(
    "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
    "type" => "!= 'exte'",
    "annule" => "= '0'"
  );
  
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where);
  
  // Admissions antérieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where = array(
    "annule" => "= '0'",
    "'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue"
  );
  
  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where);
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date );
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->display("vw_affectations.tpl");
?>