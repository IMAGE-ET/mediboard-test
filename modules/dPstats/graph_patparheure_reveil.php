<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPatParHeureReveil($debut = null, $fin = null, $prat_id = 0, $bloc_id = 0, $codeCCAM = '') {
  $ds = CSQLDataSource::get("std");
  if (!$debut) $debut = mbDate("-1 YEAR");
  if (!$fin) $fin = mbDate();

  $totalWorkDays = 0;
  for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
    $totalWorkDays += mbWorkDaysInMonth(mbTransformTime("+0 DAY", $i, "%Y-%m-01"));
  }
  
  $prat = new CMediusers;
  $prat->load($prat_id);

  $ticks = array();
  for($i = "7"; $i <= "21"; $i = $i + 1) {
    $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", "$i:00:00", "%Hh%M"));
  }
  
  $where = array();
  $where["stats"] = " = '1'";
  $bloc = new CBlocOperatoire();
  if($bloc_id) {
    $bloc->load($bloc_id);
    $where["bloc_id"] = "= '$bloc_id'";
  }
  $salle = new CSalle();
  $salles = $salle->loadList($where);
  
  $series = array();
  $serie = array("data" => array());

  // Nombre de patients par heure
  foreach($ticks as $i => $tick) {
    $query = "DROP TEMPORARY TABLE IF EXISTS pat_par_heure";
    $ds->exec($query);
    $query = "CREATE TEMPORARY TABLE pat_par_heure
                SELECT COUNT(operations.operation_id) AS total_by_day,
                       '".$tick[1]."' AS heure,
                       plagesop.date AS date
                FROM operations
                INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
                LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
                WHERE 
                  sallesbloc.stats = '1' AND 
                  plagesop.date BETWEEN '$debut' AND '$fin' AND 
                  '".$tick[1].":00' BETWEEN operations.entree_reveil AND operations.sortie_reveil AND
                  operations.annulee = '0'";
      
    if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
    if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  
    if($bloc_id) {
      $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
    }
    
    $query .= "\nGROUP BY plagesop.date";
    $result = $ds->exec($query);
    
    
    $query = "SELECT SUM(total_by_day) AS total, MAX(total_by_day) AS max,heure
                FROM pat_par_heure
                GROUP BY heure";
    $result = $ds->loadlist($query);
    if(count($result)) {
      $serie_moyenne["data"][] = array($i, $result[0]["total"] / $totalWorkDays);
      $serie_max["data"][]     = array($i, $result[0]["max"]);
    } else {
      $serie_moyenne["data"][] = array($i, 0);
      $serie_max["data"][]     = array($i, 0);
    }
  }
  
  // Nombre de patients non renseignés
  
  $query = "SELECT COUNT(operations.operation_id) AS total,
    'err' AS heure
    FROM operations
    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    WHERE 
      sallesbloc.stats = '1' AND 
      plagesop.date BETWEEN '$debut' AND '$fin' AND 
      (operations.entree_reveil IS NULL OR operations.sortie_reveil IS NULL) AND
      operations.annulee = '0'";
    
  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";

  if($bloc_id) {
    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
  
  $query .= "\nGROUP BY heure";
  $result = $ds->loadlist($query);
  if(count($result)) {
    $serie_moyenne["data"][] = array(count($ticks), $result[0]["total"] / $totalWorkDays);
  } else {
    $serie_moyenne["data"][] = array(count($ticks), 0);
  }
  //$serie_max["data"][] = array(count($ticks), 0);
  $ticks[] = array(count($ticks), "Erreurs");
  
  $serie_moyenne["label"] = "moyenne";
  $serie_max["label"]     = "max";
  
  $series[] = $serie_moyenne;
  $series[] = $serie_max;
  
  // Set up the title for the graph
  $title = "Patients moyens et max / heure du jour";
  $subtitle = "Moyenne sur tous les jours ouvrables";
  if($prat_id)  $subtitle .= " - Dr $prat->_view";
  if($bloc_id) $subtitle .= " - $bloc->_view";
  if($codeCCAM) $subtitle .= " - CCAM : $codeCCAM";

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