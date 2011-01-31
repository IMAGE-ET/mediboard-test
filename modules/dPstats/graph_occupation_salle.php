<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphOccupationSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = null, $codeCCAM = "", $type_hospi = "", $type_duree = "MONTH") {
  
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
	
  if (!$debut) $debut = mbDate("-1 YEAR");
  if (!$fin) $fin = mbDate();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $salle = new CSalle();
  $salle->load($salle_id);
  
  $bloc = new CBlocOperatoire();
  $bloc->load($bloc_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);
  
  $ticks = array();
  for($i = $debut; $i <= $fin; $i = mbDate("+1 $type_duree", $i)) {
    $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", $i, $date_format));
  }

  $where = array();
  $where['stats'] = "= '1'";
  if($salle_id) {
    $where["salle_id"] = "= '$salle_id'";
  }
  if($bloc_id) {
    $where["bloc_id"] = "= '$bloc_id'";
  }
  $salles = $salle->loadGroupList($where);
  
  // requete de récupération des interventions
  $query = "SELECT COUNT(*) AS total,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  $query_hors_plage = "SELECT COUNT(*) AS total,
    DATE_FORMAT(operations.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(operations.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND operations.date IS NOT NULL
    AND operations.plageop_id IS NULL
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  if($type_hospi) {
    $query .= "\nAND sejour.type = '$type_hospi'";
    $query_hors_plage .= "\nAND sejour.type = '$type_hospi'";
  }
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id' AND plagesop.chir_id = '$prat_id'"; 
    $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'";
  }
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  if($codeCCAM) {
    $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
    $query_hors_plage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'"; 
  }
  
  $query .=  "\nAND (operations.date BETWEEN '$debut' AND '$fin'
      OR (operations.date IS NULL AND plagesop.date BETWEEN '$debut' AND '$fin'))";
  $query .= "GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $query_hors_plage .=  "\nAND operations.date BETWEEN '$debut' AND '$fin'";
  $query_hors_plage .= "GROUP BY $type_duree_fr ORDER BY orderitem";
  

  $nbInterventions = $ds->loadList($query);
  $nbInterventions_hors_plage = $ds->loadList($query_hors_plage);

  foreach($nbInterventions as $_nb_interv) {
    foreach($nbInterventions_hors_plage as $_nb_interv_hp) {
      if ($_nb_interv["orderitem"] == $_nb_interv_hp["orderitem"]) {
        $_nb_interv["total"] += $_nb_interv_hp["total"];
      }
    }
  }
  $seriesMoy = array();
  $seriesTot = array();
  $totalMoy = 0;
  $totalTot = 0;
  
  // First serie : Interv
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Intervention")
  );
  $query = "SELECT COUNT(*) AS nbInterv,
    AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op)) AS moyenne,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  $query_hors_plage = "SELECT COUNT(*) AS nbInterv,
    AVG(IF(operations.fin_op > operations.debut_op, TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op),
    TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op)+86400)) AS moyenne,
    DATE_FORMAT(operations.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(operations.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND operations.date IS NOT NULL
    AND operations.plageop_id IS NULL
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  if($type_hospi) {
    $query .= "\nAND sejour.type = '$type_hospi'";
    $query_hors_plage .= "\nAND sejour.type = '$type_hospi'";
  }
  
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id' AND plagesop.chir_id = '$prat_id'"; 
    $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'";
  }
  
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'"; 
  }
  
  if($codeCCAM) {
    $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
    $query_hors_plage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'"; 
  }
  
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    AND operations.debut_op IS NOT NULL
    AND operations.fin_op IS NOT NULL
    AND operations.debut_op < operations.fin_op
    GROUP BY $type_duree_fr ORDER BY orderitem";

  $query_hors_plage .=  "\nAND operations.date BETWEEN '$debut' AND '$fin'
    GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $result = $ds->loadList($query);
  $result_hors_plage = $ds->loadList($query_hors_plage);

  foreach($ticks as $i => $tick) {
    $f = true;
    if(!isset($nbInterventions[$i])) {
      $nbInterventions[$i] = array("total" => 0);
    }
    foreach($result as $j => $r) {
      if($tick[1] == $r[$type_duree_fr]) {
        foreach($result_hors_plage as $key => $rb) {
          if ($tick[1] == $rb[$type_duree_fr]) {
            $r["moyenne"] = ($r["moyenne"] + $rb["moyenne"]) / 2;
            unset($result_hors_plage[$key]);
            break;
          }
        }
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"];
        $f = false;
      }
    }
    if($f) {
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
    AVG(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle)) AS moyenne,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  $query_hors_plage = "SELECT COUNT(*) AS nbInterv,
    AVG(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle)) AS moyenne,
    DATE_FORMAT(operations.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(operations.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND operations.date IS NOT NULL
    AND operations.plageop_id IS NULL
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  if($type_hospi) {
    $query .= "\nAND sejour.type = '$type_hospi'";
    $query_hors_plage .= "\nAND sejour.type = '$type_hospi'";
  }
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id' AND plagesop.chir_id = '$prat_id'";
    $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'";
  }
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  if($codeCCAM) {
    $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
    $query_hors_plage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  }
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    AND operations.entree_salle IS NOT NULL
    AND operations.sortie_salle IS NOT NULL
    AND operations.entree_salle < operations.sortie_salle
    GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $query_hors_plage .= "\nAND operations.date BETWEEN '$debut' AND '$fin'
    AND operations.entree_salle IS NOT NULL
    AND operations.sortie_salle IS NOT NULL
    AND operations.entree_salle < operations.sortie_salle
    GROUP BY $type_duree_fr ORDER BY orderitem";

  $result = $ds->loadList($query);
  $result_hors_plage = $ds->loadList($query_hors_plage);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $j => $r) {
      if($tick[1] == $r["$type_duree_fr"]) {
        foreach($result_hors_plage as $key => $rb) {
          if ($tick[1] == $rb[$type_duree_fr]) {
            $r["moyenne"] = ($r["moyenne"] + $rb["moyenne"]) / 2;
            unset($result_hors_plage[$key]);
            break;
          }
        }
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"];
        $f = false;
      }
    }
    if($f) {
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
    AVG(TIME_TO_SEC(operations.sortie_reveil)-TIME_TO_SEC(operations.entree_reveil)) AS moyenne,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  $query_hors_plage = "SELECT COUNT(*) AS nbInterv,
    AVG(TIME_TO_SEC(operations.sortie_reveil)-TIME_TO_SEC(operations.entree_reveil)) AS moyenne,
    DATE_FORMAT(operations.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(operations.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE operations.annulee = '0'
    AND operations.date IS NOT NULL
    AND operations.plageop_id IS NULL
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  if($type_hospi) {
    $query .= "\nAND sejour.type = '$type_hospi'";
    $query_hors_plage .= "\nAND sejour.type = '$type_hospi'";
  }
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id' AND plagesop.chir_id = '$prat_id'";
    $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'"; 
  }
  
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'"; 
  }
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    AND operations.entree_reveil IS NOT NULL
    AND operations.sortie_reveil IS NOT NULL
    AND operations.entree_reveil < operations.sortie_reveil
    GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $query_hors_plage .=  "\nAND operations.date BETWEEN '$debut' AND '$fin'
    AND operations.entree_reveil IS NOT NULL
    AND operations.sortie_reveil IS NOT NULL
    AND operations.entree_reveil < operations.sortie_reveil
    GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $result = $ds->loadList($query);
  $result_hors_plage = $ds->loadList($query_hors_plage);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $j => $r) {
      if($tick[1] == $r[$type_duree_fr]) {
        foreach($result_hors_plage as $key => $rb) {
          if ($tick[1] == $rb[$type_duree_fr]) {
            $r["moyenne"] = ($r["moyenne"] + $rb["moyenne"]) / 2;
            unset($result_hors_plage[$key]);
            break;
          }
        }
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$j]["total"];
        $f = false;
      }
    }
    if($f) {
      $serieMoy["data"][] = array(count($serieMoy["data"]), 0);
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesMoy[] = $serieMoy;
  $seriesTot[] = $serieTot;
  
  // Fourth serie : reservé
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Vacations attribuées")
  );
  $query = "SELECT SUM(TIME_TO_SEC(plagesop.fin) - TIME_TO_SEC(plagesop.debut)) AS total,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM plagesop
    LEFT JOIN users_mediboard ON plagesop.chir_id = users_mediboard.user_id
    WHERE plagesop.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id)       $query .= "\nAND plagesop.chir_id = '$prat_id'";
  if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    GROUP BY $type_duree_fr ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["$type_duree_fr"]) {
        $serieTot['data'][] = array($i, $r["total"]/(60*60));
        $totalTot += $r["total"]/(60*60);
        $f = false;
      }
    }
    if($f) {
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesTot[] = $serieTot;

  // Fifth serie : Occupation du personnel
  $serieMoy = $serieTot = array(
    'data' => array(),
    'label' => utf8_encode("Occupation du personnel")
  );
  
  $query = "SELECT COUNT(*) AS nbInterv,
    AVG(TIME_TO_SEC(affectation_personnel.fin)-TIME_TO_SEC(affectation_personnel.debut)) AS moyenne,
    DATE_FORMAT(plagesop.date, '$date_format') AS $type_duree_fr,
    DATE_FORMAT(plagesop.date, '$order_key') AS orderitem
    FROM operations
    LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    LEFT JOIN affectation_personnel ON affectation_personnel.object_id = operations.operation_id
    LEFT JOIN personnel ON personnel.personnel_id = affectation_personnel.personnel_id
    WHERE operations.annulee = '0'
    AND affectation_personnel.object_class = 'COperation'
    AND personnel.emplacement IN ('op', 'op_panseuse')
    AND affectation_personnel.debut IS NOT NULL
    AND affectation_personnel.fin IS NOT NULL
    AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  
  if($type_hospi) {
    $query .= "\nAND sejour.type = '$type_hospi'";
  }
  if($prat_id) {
    $query .= "\nAND operations.chir_id = '$prat_id' AND plagesop.chir_id = '$prat_id'"; 
  }
  
  if($discipline_id) {
    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    AND affectation_personnel.debut < affectation_personnel.fin
    GROUP BY $type_duree_fr ORDER BY orderitem";
  
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    if(!isset($nbInterventions[$i])) {
      $nbInterventions[$i] = array("total" => 0);
    }
    foreach($result as $j => $r) {
      if($tick[1] == $r[$type_duree_fr]) {
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$r["nbInterv"]);
        $totalTot += $r["moyenne"]/(60*60)*$r["nbInterv"];
        $f = false;
      }
    }
    if($f) {
      $serieMoy["data"][] = array(count($serieMoy["data"]), 0);
      $serieTot["data"][] = array(count($serieTot["data"]), 0);
    }
  }
  
  $seriesMoy[] = $serieMoy;
  $seriesTot[] = $serieTot;
  
  
  // Set up the title for the graph
  $subtitle = "";
  if($prat_id)       $subtitle .= " - Dr $prat->_view";
  if($discipline_id) $subtitle .= " - $discipline->_view";
  if($salle_id)      $subtitle .= " - $salle->nom";
  if($bloc_id)       $subtitle .= " - $bloc->nom";
  if($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";
  if($type_hospi)    $subtitle .= " - ".CAppUI::tr("CSejour.type.$type_hospi");

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
  
  if ($type_duree == 'MONTH') {
    return array(
      "moyenne" => array('series' => $seriesMoy, 'options' => $optionsMoy),
      "total"   => array('series' => $seriesTot, 'options' => $optionsTot));
  }
  else {
    return array('series' => $seriesTot, 'options' => $optionsTot);
  }
}
