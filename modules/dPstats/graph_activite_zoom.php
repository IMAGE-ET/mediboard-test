<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphActiviteZoom($date, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = 0, $codes_ccam = '') {
  if (!$date) $date = mbTransformTime("+0 DAY", mbDate(), "%m/%Y");

  $prat = new CMediusers;
  $prat->load($prat_id);
  
  $salle = new CSalle;
  $salle->load($salle_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);
  
  // Gestion de la date
  $debut = substr($date,3,7)."-".substr($date,0,2)."-01";
  $fin = mbDate("+1 MONTH", $debut);
  $fin = mbDate("-1 DAY", $fin);
  $step = "+1 DAY";
  
  // Tableaux des jours
  $ticks = array();
  $ticks2 = array();
  $serie_total = array(
    'label' => 'Total',
    'data' => array(),
    'markers' => array('show' => true),
    'bars' => array('show' => false)
  );
  for($i = $debut; $i <= $fin; $i = mbDate($step, $i)) {
    $ticks[] = array(count($ticks), mbTransformTime(null, $i, "%a %d"));
    $ticks2[] = array(count($ticks), mbTransformTime(null, $i, "%d"));
    $serie_total['data'][] = array(count($serie_total['data']), 0);
  }
  
  // Chargement des salles
  $where = array();
  $where['stats'] = " = '1'";
  if($salle_id) {
    $where['salle_id'] = " = '$salle_id'";
  } elseif($bloc_id) {
    $where['bloc_id'] = "= '$bloc_id'";
  }
  $salles = $salle->loadList($where);
  
  $series = array();
  $total = 0;
  foreach($salles as $salle) {
    $serie = array(
      'data' => array(),
      'label' => utf8_encode($salle->nom)
    );
    
    $query = "SELECT COUNT(operations.operation_id) AS total,
      DATE_FORMAT(plagesop.date, '%d') AS jour,
      sallesbloc.nom AS nom
      FROM operations
      INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
      INNER JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
      INNER JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
      WHERE 
        plagesop.date BETWEEN '$debut' AND '$fin' AND 
        operations.annulee = '0' AND 
        sallesbloc.salle_id = '$salle->_id'";
        
    if($prat_id && !$prat->isFromType(array("Anesthésiste"))) {
      $query .= "\nAND operations.chir_id = '$prat_id'";
    }
    if($prat_id && $prat->isFromType(array("Anesthésiste"))) {
      $query .= "\nAND (operations.anesth_id = '$prat_id' OR plagesop.anesth_id = '$prat_id')";
    }
    if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    if($codes_ccam)    $query .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
    
    $query .= "\nGROUP BY jour ORDER BY jour";
    
    $result = $salle->_spec->ds->loadlist($query);
    
    foreach($ticks2 as $i => $tick) {
      $f = true;
      foreach($result as $r) {
        if($tick[1] == $r["jour"]) {
          $serie["data"][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];
          $total += $r["total"];
          $f = false;
        }
      }
      if($f) $serie["data"][] = array(count($serie["data"]), 0);
    }
    
    $series[] = $serie;
  }
  
  $series[] = $serie_total;
  
  // Set up the title for the graph
  if($prat_id && $prat->isFromType(array("Anesthésiste"))) {
    $title = "Nombre d'anesthésie par salle - ".mbTransformTime(null, $debut, "%m/%Y");
    $subtitle = "$total anesthésies";
  } else {
    $title = "Nombre d'interventions par salle - ".mbTransformTime(null, $debut, "%m/%Y");
    $subtitle = "$total interventions";
  }

  if($prat_id)       $subtitle .= " - Dr $prat->_view";
  if($discipline_id) $subtitle .= " - $discipline->_view";
  if($codes_ccam)    $subtitle .= " - CCAM : $codes_ccam";

  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1),
    'bars' => array('show' => true, 'stacked' => true, 'barWidth' => 0.8),
    'HtmlText' => false,
    'legend' => array('show' => true, 'position' => 'nw'),
    'grid' => array('verticalLines' => false),
    'spreadsheet' => array(
      'show' => true,
      'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Données'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('Seléctionner tout le tableau')
    )
  );
  
  return array('series' => $series, 'options' => $options);
}

?>