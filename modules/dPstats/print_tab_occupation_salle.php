<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$debut         = CValue::get("date_debut");
$fin           = CValue::get("date_fin");
$codeCCAM      = CValue::get("CCAM");
$type_hospi    = CValue::get("type");
$discipline_id = CValue::get("discipline_id");
$bloc_id       = CValue::get("bloc_id");
$salle_id      = CValue::get("salle_id");
$hors_plage    = CValue::get("hors_plage");

CView::enforceSlave();

if (!$debut) {
  $debut = CMbDT::date("-1 YEAR");
}
if (!$fin) {
  $fin = CMbDT::date();
}

$salle = new CSalle();
$salle->load($salle_id);

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

$discipline = new CDiscipline;
$discipline->load($discipline_id);

$salles = CSalle::getSallesStats($salle_id, $bloc_id);

// Chargement des praticiens
$user = new CMediusers();
$where = array();
$where["users_mediboard.actif"] = "= '1'";
if ($discipline->_id) {
  $where["users_mediboard.discipline_id"] = "= '$discipline->_id'";
}
// Filter on user type
$utypes_flip = array_flip(CUser::$types);
$user_types = array("Chirurgien", "Anesthésiste", "Médecin", "Dentiste");
foreach ($user_types as &$_type) {
  $_type = $utypes_flip[$_type];
}
$where["users.user_type"] = CSQLDataSource::prepareIn($user_types);

$ljoin = array("users" => "users.user_id = users_mediboard.user_id");
$order = "users_mediboard.function_id, users_mediboard.discipline_id, users.user_last_name, users.user_first_name";
$listPrats = $user->loadList($where, $order, null, null, $ljoin);

// Gestion du hors plage
$where_hors_plage = !$hors_plage ? "AND operations.plageop_id IS NOT NULL" : "";

// Nombre totaux d'interventions
$tableau = array();

$query = "SELECT COUNT(*) AS nbInterv, users.user_id
  FROM operations
  LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
  LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
  LEFT JOIN users ON operations.chir_id = users.user_id
  WHERE operations.annulee = '0'
  AND operations.date BETWEEN '$debut' AND '$fin'
  $where_hors_plage
  AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
  AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
  AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats));
  
if ($type_hospi) {
  $query .= "\nAND sejour.type = '$type_hospi'";
}
if ($discipline_id) {
  $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
}
if ($codeCCAM) {
  $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
}

$query .=  "\nGROUP BY users.user_id ORDER BY users.user_last_name, users.user_first_name";

$ds = CSQLDataSource::get("std");
$result = $ds->loadList($query);

$total_interventions     = 0;
$duree_totale_intervs    = 0;
$nb_interv_intervs       = 0;
$duree_totale_occupation = 0;
$nb_interv_occupation    = 0;

foreach ($result as $praticien) {
  $prat = new CMediusers();
  $prat->load($praticien["user_id"]);
  $prat->loadRefFunction();
  $prat->loadRefDiscipline();
  $tableau[$praticien["user_id"]]["user"]                     = $prat;
  $total_interventions += $praticien["nbInterv"];
  $tableau[$praticien["user_id"]]["total_interventions"]      = $praticien["nbInterv"];
  $tableau[$praticien["user_id"]]["duree_totale_intervs"]     = 0;
  $tableau[$praticien["user_id"]]["duree_moyenne_intervs"]    = 0;
  $tableau[$praticien["user_id"]]["nb_interv_intervs"]        = 0;
  $tableau[$praticien["user_id"]]["duree_totale_occupation"]  = 0;
  $tableau[$praticien["user_id"]]["duree_moyenne_occupation"] = 0;
  $tableau[$praticien["user_id"]]["nb_interv_occupation"]     = 0;
}

// Durée d'intervention

$query = "SELECT COUNT(*) AS nbInterv,
    SUM(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op)) AS duree_totale,
    users.user_id
  FROM operations
  LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
  LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
  LEFT JOIN users ON operations.chir_id = users.user_id
  WHERE operations.annulee = '0'
  AND operations.date BETWEEN '$debut' AND '$fin'
  $where_hors_plage
  AND operations.debut_op IS NOT NULL
  AND operations.fin_op IS NOT NULL
  AND operations.debut_op < operations.fin_op
  AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
  AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
  AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats));
  
if ($type_hospi) {
  $query .= "\nAND sejour.type = '$type_hospi'";
}
if ($discipline_id) {
  $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
}
if ($codeCCAM) {
  $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
}

$query .=  "\nGROUP BY users.user_id";
$result = $ds->loadList($query);

foreach ($result as $item) {
  $duree_totale_intervs += $item["duree_totale"];
  $tableau[$item["user_id"]]["duree_totale_intervs"]  = $item["duree_totale"]/(60*60);
  $tableau[$item["user_id"]]["duree_moyenne_intervs"] = $item["duree_totale"]/(60*$item["nbInterv"]);
  $nb_interv_intervs += $item["nbInterv"];
  $tableau[$item["user_id"]]["nb_interv_intervs"]     = $item["nbInterv"];
}

// Occupation de salle

$query = "SELECT COUNT(*) AS nbInterv,
    SUM(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle)) AS duree_totale,
    users.user_id
  FROM operations
  LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
  LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
  LEFT JOIN users ON operations.chir_id = users.user_id
  WHERE operations.annulee = '0'
  AND operations.date BETWEEN '$debut' AND '$fin'
  $where_hors_plage
  AND operations.entree_salle IS NOT NULL
  AND operations.sortie_salle IS NOT NULL
  AND operations.entree_salle < operations.sortie_salle
  AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
  AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
  AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats));
  
if ($type_hospi) {
  $query .= "\nAND sejour.type = '$type_hospi'";
}
if ($discipline_id) {
  $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
}
if ($codeCCAM) {
  $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
}

$query .=  "\nGROUP BY users.user_id";
$result = $ds->loadList($query);

foreach ($result as $item) {
  $duree_totale_occupation += $item["duree_totale"];
  $tableau[$item["user_id"]]["duree_totale_occupation"]  = $item["duree_totale"]/(60*60);
  $tableau[$item["user_id"]]["duree_moyenne_occupation"] = $item["duree_totale"]/(60*$item["nbInterv"]);
  $nb_interv_occupation += $item["nbInterv"];
  $tableau[$item["user_id"]]["nb_interv_occupation"]     = $item["nbInterv"];
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debut"     , $debut);
$smarty->assign("fin"       , $fin);
$smarty->assign("codeCCAM"  , $codeCCAM);
$smarty->assign("type_hospi", $type_hospi);
$smarty->assign("discipline", $discipline);
$smarty->assign("bloc"      , $bloc);
$smarty->assign("salle"     , $salle);
$smarty->assign("hors_plage", $hors_plage);

$smarty->assign("total_interventions"    , $total_interventions);
$smarty->assign("duree_totale_intervs"   , $duree_totale_intervs/(60*60));
$smarty->assign("duree_moyenne_intervs"   , $duree_totale_intervs/(60*$nb_interv_intervs));
$smarty->assign("nb_interv_intervs"      , $nb_interv_intervs);
$smarty->assign("duree_totale_occupation", $duree_totale_occupation/(60*60));
$smarty->assign("duree_moyenne_occupation", $duree_totale_occupation/(60*$nb_interv_occupation));
$smarty->assign("nb_interv_occupation"   , $nb_interv_occupation);

$smarty->assign("tableau", $tableau);

$smarty->display("print_tab_occupation_salle.tpl");


