<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPatJourSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = null, $codeCCAM = '', $hors_plage) {
  if (!$debut) $debut = CMbDT::date("-1 YEAR");
  if (!$fin) $fin = CMbDT::date();
  
  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $salle = new CSalle;
  $salle->load($salle_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);

  $ticks = array();
  for ($i = $debut; $i <= $fin; $i = CMbDT::date("+1 MONTH", $i)) {
    $ticks[] = array(count($ticks), CMbDT::transform("+0 DAY", $i, "%m/%Y"));
  }

  //$salles = CSalle::getSallesStats($salle_id, $bloc_id);
  $series = array();
  $serie = array('data' => array());

  // @TODO : Ajouter les intervs hors plage
  $query = "SELECT COUNT(operations.operation_id) AS total,
    COUNT(DISTINCT(plagesop.date)) AS nb_days,
    COUNT(DISTINCT(sallesbloc.salle_id)) AS nb_salles,
    DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
    DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
    FROM operations
    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE 
      sallesbloc.stats = '1' AND 
      plagesop.date BETWEEN '$debut' AND '$fin' AND 
      operations.annulee = '0'";
  
  if ($prat_id)       $query .= "\nAND operations.chir_id = '$prat_id'";
  if ($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if ($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";

  if ($salle_id) {
    $query .= "\nAND sallesbloc.salle_id = '$salle_id'";
  }
  elseif ($bloc_id) {
    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
  
  $query .= "\nGROUP BY mois ORDER BY orderitem";
  
  $result = $prat->_spec->ds->loadlist($query);
  
  if ($hors_plage) {
    $query_hors_plage = "SELECT COUNT(operations.operation_id) AS total,
      COUNT(DISTINCT(operations.date)) AS nb_days,
      COUNT(DISTINCT(sallesbloc.salle_id)) AS nb_salles,
      DATE_FORMAT(operations.date, '%m/%Y') AS mois,
      DATE_FORMAT(operations.date, '%Y-%m-01') AS orderitem
      FROM operations
      INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
      LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
      WHERE
        operations.date IS NOT NULL AND
        operations.plageop_id IS NULL AND
        sallesbloc.stats = '1' AND 
        operations.date BETWEEN '$debut' AND '$fin' AND 
        operations.annulee = '0'";
    
    if ($prat_id)       $query_hors_plage .= "\nAND operations.chir_id = '$prat_id'";
    if ($discipline_id) $query_hors_plage .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    if ($codeCCAM)      $query_hors_plage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  
    if ($salle_id) {
      $query_hors_plage .= "\nAND sallesbloc.salle_id = '$salle_id'";
    }
    elseif($bloc_id) {
      $query_hors_plage .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
    }
    $query_hors_plage .= "\nGROUP BY mois ORDER BY orderitem";
    $result_hors_plage = $prat->_spec->ds->loadlist($query_hors_plage);

  }

  foreach ($ticks as $i => $tick) {
    $f = true;
    foreach ($result as $r) {
      if ($tick[1] == $r["mois"]) {
        $res = $r["total"]/($r["nb_days"]*$r["nb_salles"]);
        if ($hors_plage) {
          foreach ($result_hors_plage as &$_r_h) {
            if ($tick[1] == $_r_h["mois"]) {
              $res_hors_plage = $_r_h["total"]/($_r_h["nb_days"]*$_r_h["nb_salles"]);
              $res = ($res * $r["total"] + $res_hors_plage * $_r_h["total"]) / ($r["total"] + $_r_h["total"]);
              unset($r_h);
              break;
            }
          }
        }
        //$nbjours = mbWorkDaysInMonth($r["orderitem"]);
        //$serie['data'][] = array($i, $r["total"]/($nbjours*count($salles)));
        $serie['data'][] = array($i, $res);
        //$serie['data'][] = array($i, $r["total"]/($r["nb_days"]*count($salles)));
        $f = false;
      }
    }
    if($f) {
      $serie["data"][] = array(count($serie["data"]), 0);
    }
  }

  $series[] = $serie;
  
  // Set up the title for the graph
  $title = "Patients / jour / salle active dans le mois";
  $subtitle = "Uniquement les jours d'activité";
  if ($prat_id)       $subtitle .= " - Dr $prat->_view";
  if ($discipline_id) $subtitle .= " - $discipline->_view";
  if ($salle_id)      $subtitle .= " - $salle->nom";
  if ($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";

  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
    'lines' => array('show' => true, 'filled' => true, 'fillColor' => '#999'),
    'markers' => array('show' => true),
    'points' => array('show' => true),
    'HtmlText' => false,
    'legend' => array('show' => true, 'position' => 'nw'),
    'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
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
  
  return array('series' => $series, 'options' => $options);
}