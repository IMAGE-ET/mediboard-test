<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$date       = CValue::getOrSession("date", mbDate());
$type_hospi = CValue::getOrSession("type_hospi", null);
$vue        = CValue::getOrSession("vue", 0);

$mouvements = array("comp" => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "ambu" => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "urg"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "ssr"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "psy"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)));
$group = CGroups::loadCurrent();

// R�cup�ration de la liste des services et du service selectionn�
$where = array(
           "externe"  => "= '0'",
           "group_id" => "= '$group->_id'"
         );
$order      = "nom";
$service    = new CService();
$services   = $service->loadListWithPerms(PERM_READ, $where, $order);
$service_id = CValue::getOrSession("service_id", null);

// R�cup�ration de la liste des praticiens et du praticien selectionn�
$praticien    = new CMediusers();
$praticiens   = $praticien->loadPraticiens(PERM_READ);
$praticien_id = CValue::getOrSession("praticien_id", null);

$date  = CValue::getOrSession("date" , mbDate());

$limit1  = $date." 00:00:00";
$limit2  = $date." 23:59:59";

// Patients plac�s
$affectation                 = new CAffectation();
$ljoin                       = array();
$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]            = "service.service_id = chambre.service_id";
$where                       = array();
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services), $service_id);
$where["sejour.type"]        = CSQLDataSource::prepareIn(array_keys($mouvements) , $type_hospi);
if ($vue) {
  $where["sejour.confirme"] = " = '0'";
}
if($praticien_id) {
  $where["sejour.praticien_id"] = "= '$praticien_id'";
}

// Patients non plac�s
$sejour                                = new CSejour();
$ljoinNP                               = array();
$ljoinNP["affectation"]                = "sejour.sejour_id = affectation.sejour_id";
$whereNP                               = array();
$whereNP["sejour.group_id"]            = "= '$group->_id'";
$whereNP["sejour.annule"]              = "= '0'";
$whereNP["sejour.type"]                = CSQLDataSource::prepareIn(array_keys($mouvements), $type_hospi);
$whereNP["affectation.affectation_id"] = "IS NULL";
if($service_id) {
  $whereNP["sejour.service_id"] = "= '$service_id'";
}
if($praticien_id) {
  $whereNP["sejour.praticien_id"] = "= '$praticien_id'";
}

// Comptage des patients pr�sents
$wherePresents     = $where;
$wherePresents[]   = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
$presents          = $affectation->countList($wherePresents, null, $ljoin);

$wherePresentsNP   = $whereNP;
$wherePresentsNP[] = "'$date' BETWEEN DATE(sejour.entree) AND DATE(sejour.sortie)";
$presentsNP        = $sejour->countList($wherePresentsNP, null, $ljoinNP);

// Comptage des d�placements
if ($vue) {
  unset($where["sejour.confirme"]);
  $where["effectue"] = "= '0'";
}
$where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
$whereEntrants = $whereSortants = $where;
$whereEntrants["sejour.entree"] = "!= affectation.entree";
$whereSortants["sejour.sortie"] = "!= affectation.sortie";
$dep_entrants = $affectation->countList($whereEntrants, null, $ljoin);
$dep_sortants = $affectation->countList($whereSortants, null, $ljoin);

// Comptage des entr�es/sorties
foreach($mouvements as $type => &$_mouvement) {
  if(($type_hospi && $type_hospi != $type) || ($type_hospi == "ambu")) {
    continue;
  }
  $where["sejour.type"] = $whereNP["sejour.type"] = " = '$type'";
  foreach($_mouvement as $type_mouvement => &$_liste) {
    if($type == "ambu" && $type_mouvement == "sorties") {
      $_liste["place"]     = 0;
      $_liste["non_place"] = 0;
      continue;
    }
    if($type_mouvement == "entrees") {
      unset($where["affectation.sortie"]);
      $where["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
      if(isset($where["sejour.sortie"])) {
        unset($where["sejour.sortie"]);
      }
      if(isset($whereNP["sejour.sortie"])) {
        unset($whereNP["sejour.sortie"]);
      }
      $where["sejour.entree"]      = "= affectation.entree";
      $whereNP["sejour.entree"]    = "BETWEEN '$limit1' AND '$limit2'";
    } else {
      unset($where["affectation.entree"]);
      $where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
      if(isset($where["sejour.entree"])) {
        unset($where["sejour.entree"]);
      }
      if(isset($whereNP["sejour.entree"])) {
        unset($whereNP["sejour.entree"]);
      }
      $where["sejour.sortie"]      = "= affectation.sortie";
      $whereNP["sejour.sortie"]    = "BETWEEN '$limit1' AND '$limit2'";
    }
    $_liste["place"]     = $affectation->countList($where, null, $ljoin);
    $_liste["non_place"] = $sejour->countList($whereNP, null, $ljoinNP);
  }
}

$smarty = new CSmartyDP;
$smarty->assign("presents"    , $presents);
$smarty->assign("presentsNP"  , $presentsNP);
$smarty->assign("mouvements"  , $mouvements);
$smarty->assign("dep_entrants", $dep_entrants);
$smarty->assign("dep_sortants", $dep_sortants);
$smarty->assign("services"    , $services);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("praticiens"  , $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("type_hospi"  , $type_hospi);
$smarty->assign("vue"         , $vue);
$smarty->assign("date"        , $date);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("edit_sorties.tpl");

?>