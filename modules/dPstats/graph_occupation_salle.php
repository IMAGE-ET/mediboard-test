<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphOccupationSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $codeCCAM = '') {
  
  $ds = CSQLDataSource::get("std");
  
  if (!$debut) $debut = mbDate("-1 YEAR");
  if (!$fin) $fin = mbDate();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
	
	$salle = new CSalle();
	$salle->load($salle_id);
	
	$bloc = new CBlocOperatoire();
	$bloc->load($bloc_id);
  
  $ticks = array();
  for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
    $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", $i, "%m/%Y"));
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
  DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%m/%Y') AS mois,
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%Y%m') AS orderitem
    FROM operations
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    WHERE operations.annulee = '0'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  $query .=  "\nAND (operations.date BETWEEN '$debut' AND '$fin'
      OR (operations.date IS NULL AND plagesop.date BETWEEN '$debut' AND '$fin'))
    GROUP BY mois ORDER BY orderitem";
  $nbInterventions = $ds->loadList($query);

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
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%m/%Y') AS mois,
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%Y%m') AS orderitem
    FROM operations
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    WHERE operations.annulee = '0'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  $query .=  "\nAND (operations.date BETWEEN '$debut' AND '$fin'
      OR (operations.date IS NULL AND plagesop.date BETWEEN '$debut' AND '$fin'))
    AND operations.debut_op IS NOT NULL
    AND operations.fin_op IS NOT NULL
    AND operations.debut_op < operations.fin_op
    GROUP BY mois ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["mois"]) {
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"];
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
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%m/%Y') AS mois,
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%Y%m') AS orderitem
    FROM operations
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    WHERE operations.annulee = '0'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  $query .=  "\nAND (operations.date BETWEEN '$debut' AND '$fin'
      OR (operations.date IS NULL AND plagesop.date BETWEEN '$debut' AND '$fin'))
    AND operations.entree_salle IS NOT NULL
    AND operations.sortie_salle IS NOT NULL
    AND operations.entree_salle < operations.sortie_salle
    GROUP BY mois ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["mois"]) {
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"];
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
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%m/%Y') AS mois,
    DATE_FORMAT(IF(operations.date, operations.date, plagesop.date), '%Y%m') AS orderitem
    FROM operations
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    WHERE operations.annulee = '0'
    AND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id) $query .= "\nAND operations.chir_id = '$prat_id'";
  $query .=  "\nAND (operations.date BETWEEN '$debut' AND '$fin'
      OR (operations.date IS NULL AND plagesop.date BETWEEN '$debut' AND '$fin'))
    AND operations.entree_reveil IS NOT NULL
    AND operations.sortie_reveil IS NOT NULL
    AND operations.entree_reveil < operations.sortie_reveil
    GROUP BY mois ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["mois"]) {
        $serieMoy['data'][] = array($i, $r["moyenne"]/(60));
        $totalMoy += $r["moyenne"]/(60);
        $serieTot['data'][] = array($i, $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"]);
        $totalTot += $r["moyenne"]/(60*60)*$nbInterventions[$i]["total"];
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
	  DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	  DATE_FORMAT(plagesop.date, '%Y%m') AS orderitem
	  FROM plagesop
    WHERE plagesop.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
  if($prat_id) $query .= "\nAND plagesop.chir_id = '$prat_id'";
  $query .=  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'
    GROUP BY mois ORDER BY orderitem";
  $result = $ds->loadList($query);

  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($tick[1] == $r["mois"]) {
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

  // Set up the title for the graph
  $subtitle = "";
	if($prat_id)  $subtitle .= "Dr $prat->_view - ";
	if($salle_id) $subtitle .= "$salle->nom - ";
	if($bloc_id) $subtitle  .= "$bloc->nom - ";
  if($codeCCAM) $subtitle .= "CCAM : $codeCCAM - ";

  $optionsMoy = array(
    'title' => utf8_encode("Durées moyennes d'occupation du bloc (en minutes)"),
    'subtitle' => utf8_encode("par intervention - ".$subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
    'lines' => array('show' => true),
    'points' => array('show' => true),
    'markers' => array('show' => true),
    'HtmlText' => false,
    'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
    'legend' => array('show' => true, 'position' => 'nw'),
    'grid' => array('verticalLines' => true),
    'spreadsheet' => array(
      'show' => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ',',
      'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Données'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('Sélectionner tout le tableau')
    )
  );
  if ($totalMoy == 0) $optionsMoy['yaxis']['max'] = 1;

  $optionsTot = array(
    'title' => utf8_encode("Durées totales d'occupation du bloc (en heures)"),
    'subtitle' => utf8_encode("total estimé - ".$subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
    'lines' => array('show' => true),
    'points' => array('show' => true),
    'markers' => array('show' => true),
    'HtmlText' => false,
    'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
    'legend' => array('show' => true, 'position' => 'nw'),
    'grid' => array('verticalLines' => true),
    'spreadsheet' => array(
      'show' => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ',',
      'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Données'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('Sélectionner tout le tableau')
    )
  );
  if ($totalTot == 0) $optionsTot['yaxis']['max'] = 1;
  
  return array(
    "moyenne" => array('series' => $seriesMoy, 'options' => $optionsMoy),
    "total"   => array('series' => $seriesTot, 'options' => $optionsTot));
}
