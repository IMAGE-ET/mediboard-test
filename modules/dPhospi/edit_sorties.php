<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$vue  = CValue::getOrSession("vue", 0);
$date = CValue::getOrSession("date", mbDate());
$sorties = array("comp" => 0, "ambu" => 0, "ssr" => 0, "psy" => 0);

$group = CGroups::loadCurrent();

// Récupération de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

$service  = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where);

$date  = CValue::getOrSession("date" , mbDate());

$affectation  = new CAffectation();

$where = array();
$ljoin = array();
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]            = "service.service_id = chambre.service_id";
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services));
$where["sejour.type"]        = "NOT IN ('exte', 'seances')";

if ($vue) {
  $where["confirme"] = " = '0'";
}

// Comptage des patients présents
$wherePresents   = $where;
$wherePresents[] = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
$presents = $affectation->countList($wherePresents, null, $ljoin);

$where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";

// Comptage des déplacements
if ($vue) {
  unset($where["confirme"]);
  $where["effectue"] = "= '0'";
}
$where["sejour.sortie"]      = "!= affectation.sortie";
$deplacements = $affectation->countList($where, null, $ljoin);

// Comptage des sorties
$where["sejour.sortie"]      = "= affectation.sortie";
foreach($sorties as $type => &$_sortie) {
  $where["sejour.type"] = " = '$type'";
  $_sortie = $affectation->countList($where, null, $ljoin);
}

$smarty = new CSmartyDP;
$smarty->assign("presents"    , $presents);
$smarty->assign("sorties"     , $sorties);
$smarty->assign("deplacements", $deplacements);
$smarty->assign("vue"         , $vue);
$smarty->assign("date"        , $date);

$smarty->display("edit_sorties.tpl");

?>