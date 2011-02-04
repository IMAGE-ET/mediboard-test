<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPatRepartJour($debut = null, $fin = null, $prat_id = 0, $bloc_id = 0, $discipline_id = null, $codeCCAM = '') {
  if (!$debut) $debut = mbDate("-1 YEAR");
  if (!$fin) $fin = mbDate();
  
  $prat = new CMediusers();
  $prat->load($prat_id);
  
  $discipline = new CDiscipline;
  $discipline->load($discipline_id);

  $ticks = array(array("0", "Dimanche"),
                 array("1", "Lundi"),
                 array("2", "Mardi"),
                 array("3", "Mercredi"),
                 array("4", "Jeudi"),
                 array("5", "Vendredi"),
                 array("6", "Samedi"));
  
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

  // Nombre de patients par jour de la semaine
  $query = "SELECT COUNT(operations.operation_id) AS total,
    COUNT(DISTINCT(plagesop.date)) AS nb_days,
    DATE_FORMAT(plagesop.date, '%W') AS jour,
	  DATE_FORMAT(plagesop.date, '%w') AS orderitem
    FROM operations
    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
    LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
    LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
    WHERE 
      sallesbloc.stats = '1' AND 
      plagesop.date BETWEEN '$debut' AND '$fin' AND
      operations.annulee = '0'";
    
  if($prat_id)       $query .= "\nAND operations.chir_id = '$prat_id'";
  if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";

  if($bloc_id) {
    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
  
  $query .= "\nGROUP BY jour ORDER BY orderitem";
  $result = $prat->_spec->ds->loadlist($query);
  
  foreach($ticks as $i => $tick) {
    $f = true;
    foreach($result as $r) {
      if($i == $r["orderitem"]) {
        $serie["data"][] = array($tick[0], $r["total"]/$r["nb_days"]);
        $f = false;
      }
    }
    if($f) $serie["data"][] = array(count($serie["data"]), 0);
  }
  
  $serie["label"] = "moyenne";

  $series[] = $serie;
  
  // Set up the title for the graph
  $title = "Patients moyens / jour de la semaine";
  $subtitle = "Uniquement les jours d'activité";
  if($prat_id)       $subtitle .= " - Dr $prat->_view";
  if($discipline_id) $subtitle .= " - $discipline->_view";
  if($bloc_id)       $subtitle .= " - $bloc->_view";
  if($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";

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