<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphOccupationSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = null, $codeCCAM = "", $type_hospi = "", $hors_plage, $type_duree) {

  $ds = CSQLDataSource::get("std");
  
  if ($type_duree == "MONTH") {
    $type_duree_fr = "mois";
    $date_format = "%m/%Y";
    $order_key = "%Y%m";
  }
  else {
    $type_duree_fr = "jour";
    $date_format = "%d/%m/%Y";
    $order_key = "%Y%m%d";
  }
  
  if (!$debut) $debut = CMbDT::date("-1 YEAR");
  if (!$fin) $fin = CMbDT::date();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $salle = new CSalle();
  $salle->load($salle_id);
  
  $bloc = new CBlocOperatoire();
  $bloc->load($bloc_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);
  
  $ticks = array();
  for ($i = $debut; $i <= $fin; $i = CMbDT::date("+1 $type_duree", $i)) {
    $ticks[] = array(count($ticks), CMbDT::transform("+0 DAY", $i, $date_format));
  }

  $salles = CSalle::getSallesStats($salle_id, $bloc_id);$user = new CMediusers();
  
  // Chargement des praticiens
  $where = array();
  $where["users_mediboard.actif"] = "= '1'";
  if($discipline->_id) {
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

  $seriesMoy = array();
  $seriesTot = array();
  $totalMoy = 0;
  $totalTot = 0;

  // Gestion du hors plage
  if($hors_plage) {
    $where_hors_plage = "AND (plagesop.date BETWEEN '$debut' AND '$fin'
                              OR operations.date BETWEEN '$debut' AND '$fin')";
  }
  else {
    $where_hors_plage = "AND plagesop.date BETWEEN '$debut' AND '$fin'
                         AND operations.date IS NULL
                         AND operations.plageop_id IS NOT NULL";
  }
  
  // First serie : Interv
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Intervention")
  );
  $query = "SELECT COUNT(*) AS nbInterv,
    SUM(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op)) AS duree_total,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$date_format') AS $type_duree_fr,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    LEFT JOIN users ON operations.chir_id = users.user_id
    WHERE operations.annulee = '0'
      $where_hors_plage
      AND operations.debut_op IS NOT NULL
      AND operations.fin_op IS NOT NULL
      AND operations.debut_op < operations.fin_op
      AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
      AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
      AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats), $prat_id);
    
  if ($type_hospi)    $query .= "\nAND sejour.type = '$type_hospi'";
  if ($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if ($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  
  $query .=  "\nGROUP BY $type_duree_fr ORDER BY orderitem";
  $result = $ds->loadList($query);
  
  foreach ($ticks as $i => $tick) {
    $f = true;
    foreach ($result as $j=>$r) {
      if ($tick[1] == $r["$type_duree_fr"]) {
        $nb_interv = $r["nbInterv"];
        $serieMoy['data'][] = array($i, $r["duree_total"]/(60*$nb_interv));
        $totalMoy += $r["duree_total"]/(60*$nb_interv);
        $serieTot['data'][] = array($i, $r["duree_total"]/(60*60));
        $totalTot += $r["duree_total"]/(60*60);
        $f = false;
      }
    }
    if ($f) {
      $serieMoy["data"][] = array(count($serieMoy["data"]), 0);
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesMoy[] = $serieMoy;
  $seriesTot[] = $serieTot;
  
  // Second serie : Occupation
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Occupation de salle")
  );
  $query = "SELECT COUNT(*) AS nbInterv,
    SUM(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle)) AS duree_total,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$date_format') AS $type_duree_fr,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    LEFT JOIN users ON operations.chir_id = users.user_id
    WHERE operations.annulee = '0'
      $where_hors_plage
      AND operations.entree_salle IS NOT NULL
      AND operations.sortie_salle IS NOT NULL
      AND operations.entree_salle < operations.sortie_salle
      AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
      AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
      AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats), $prat_id);
    
  if ($type_hospi)    $query .= "\nAND sejour.type = '$type_hospi'";
  if ($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if ($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  
  $query .=  "\nGROUP BY $type_duree_fr ORDER BY orderitem";
  $result = $ds->loadList($query);
  
  foreach ($ticks as $i => $tick) {
    $f = true;
    foreach ($result as $j=>$r) {
      if ($tick[1] == $r["$type_duree_fr"]) {
        $nb_interv = $r["nbInterv"];
        $serieMoy['data'][] = array($i, $r["duree_total"]/(60*$nb_interv));
        $totalMoy += $r["duree_total"]/(60*$nb_interv);
        $serieTot['data'][] = array($i, $r["duree_total"]/(60*60));
        $totalTot += $r["duree_total"]/(60*60);
        $f = false;
      }
    }
    if ($f) {
      $serieMoy["data"][] = array(count($serieMoy["data"]), 0);
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesMoy[] = $serieMoy;
  $seriesTot[] = $serieTot;
  
  // Third serie : SSPI
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Salle de reveil")
  );
  $query = "SELECT COUNT(*) AS nbInterv,
    SUM(TIME_TO_SEC(operations.sortie_reveil_possible)-TIME_TO_SEC(operations.entree_reveil)) AS duree_total,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$date_format') AS $type_duree_fr,
    DATE_FORMAT(COALESCE(operations.date, plagesop.date), '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    LEFT JOIN users ON operations.chir_id = users.user_id
    WHERE operations.annulee = '0'
      $where_hors_plage
      AND operations.entree_reveil IS NOT NULL
      AND operations.sortie_reveil_possible IS NOT NULL
      AND operations.entree_reveil < operations.sortie_reveil_possible
      AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
      AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles))."
      AND users.user_id ".CSQLDataSource::prepareIn(array_keys($listPrats), $prat_id);
    
  if ($type_hospi)    $query .= "\nAND sejour.type = '$type_hospi'";
  if ($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if ($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  
  $query .=  "\nGROUP BY $type_duree_fr ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach ($ticks as $i => $tick) {
    $f = true;
    foreach ($result as $j=>$r) {
      if ($tick[1] == $r[$type_duree_fr]) {
        $nb_interv = $r["nbInterv"];
        $serieMoy['data'][] = array($i, $r["duree_total"]/(60*$nb_interv));
        $totalMoy += $r["duree_total"]/(60*$nb_interv);
        $serieTot['data'][] = array($i, $r["duree_total"]/(60*60));
        $totalTot += $r["duree_total"]/(60*60);
        $f = false;
      }
    }
    if ($f) {
      $serieMoy["data"][] = array(count($serieMoy["data"]), 0);
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesMoy[] = $serieMoy;
  $seriesTot[] = $serieTot;
  
  // Set up the title for the graph
  $subtitle = "";
  if ($prat_id)       $subtitle .= " - Dr $prat->_view";
  if ($discipline_id) $subtitle .= " - $discipline->_view";
  if ($salle_id)      $subtitle .= " - $salle->nom";
  if ($bloc_id)       $subtitle .= " - $bloc->nom";
  if ($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";
  if ($type_hospi)    $subtitle .= " - ".CAppUI::tr("CSejour.type.$type_hospi");

  $optionsMoy = CFlotrGraph::merge("lines", array(
    'title'    => utf8_encode("Durées moyennes d'occupation du bloc (en minutes)"),
    'subtitle' => utf8_encode("par intervention $subtitle"),
    'xaxis'    => array('ticks' => $ticks),
    'grid'     => array('verticalLines' => true)
  ));
  if ($totalMoy == 0) $optionsMoy['yaxis']['max'] = 1;

  $optionsTot = CFlotrGraph::merge("lines", array(
    'title'    => utf8_encode("Durées totales d'occupation du bloc (en heures)"),
    'subtitle' => utf8_encode("total estimé $subtitle"),
    'xaxis'    => array('ticks' => $ticks),
    'grid'     => array('verticalLines' => true)
  ));
  if ($totalTot == 0) $optionsTot['yaxis']['max'] = 1;
  
  if ($type_duree == "MONTH") {
    return array(
      "moyenne" => array('series' => $seriesMoy, 'options' => $optionsMoy),
      "total"   => array('series' => $seriesTot, 'options' => $optionsTot));
  }
  else {
    return array('series' => $seriesTot, 'options' => $optionsTot);
  }
}
