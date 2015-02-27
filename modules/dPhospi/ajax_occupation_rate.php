<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$services_ids = CValue::getOrSession("services_ids");
$date = CValue::getOrSession('date', CMbDT::date());

/** @var CMySQLDataSource $ds */
$ds = CSQLDataSource::get('std');

/* Récupération du nombre de lits */
$query = "SELECT COUNT(*) FROM `lit`
    LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
    WHERE `chambre`.`service_id` " . $ds->prepareIn($services_ids) . ";";
$result = $ds->fetchRow($ds->exec($query));
$total_lits = $result[0];

/* Récupération du nombre de lits disponibles */
$query = "SELECT COUNT(*) FROM `lit`
    LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
    WHERE `chambre`.`service_id` " . $ds->prepareIn($services_ids) . " AND `lit`.`annule` = '0'
    AND `chambre`.`annule` = '0';";
$result = $ds->fetchRow($ds->exec($query));
$total_lits_dispo = $result[0];

$ticks = array();
$series = array(
  'total_lits_dispo' => array(
    'name' => 'total_lits_dispo',
    'label' => utf8_encode('Capacité ouverte'),
    'yaxis' => 1,
    'data' => array(),
    'lines' => array('show' => true,),
    'color' => '#FFA700'
  ),
  'total_lits' => array(
    'name' => 'total_lits',
    'label' => utf8_encode('Capacité installée'),
    'yaxis' => 1,
    'data' => array(),
    'lines' => array('show' => true,),
    'color' => '#8803BB'
  ),
  'patients' => array(
    'name' => 'patients',
    'label' => utf8_encode('Lits occupés'),
    'data' => array(),
    'bars' => array(
      'show' => true,
      'barWidth' => 0.9,
      'fill' => true,
    ),
    'color' => '#00A8F0'
  ),
  'entrants' => array(
    'name' => 'entrants',
    'label' => utf8_encode('Patients entrants'),
    'data' => array(),
    'bars' => array(
      'show' => true,
      'barWidth' => 0.45,
      'fill' => true,
      'fillOpacity' => 0.6,
      'lineWidth' => 1.5,
      'centered' => false,
    ),
    'color' => '#066C16'
  ),
  'sortants' => array(
    'name' => 'sortants',
    'label' => utf8_encode('Patients sortants'),
    'data' => array(),
    'bars' => array(
      'show' => true,
      'barWidth' => 0.45,
      'fill' => true,
      'fillOpacity' => 0.6,
      'lineWidth' => 2,
      'centered' => false,
    ),
    'color' => '#F00000'
  )
);

for($h = 0; $h < 24; $h++) {
  $_hour = str_pad($h, 2, "0", STR_PAD_LEFT);
  $ticks[] = array($h, $_hour . 'h');
  $series['total_lits']['data'][] = array($h, $total_lits);
  $series['total_lits_dispo']['data'][] = array($h, $total_lits_dispo);

  /* Récupération du nombre lits occupés */
  $query = "SELECT COUNT(DISTINCT `lit_id`) FROM `affectation`
      WHERE `service_id` " . $ds->prepareIn($services_ids) . " AND `entree` <= '$date $_hour:59:59'
      AND `sortie` >= '$date $_hour:00:00' AND `sejour_id` IS NOT NULL";
  $result = $ds->fetchRow($ds->exec($query));
  $series['patients']['data'][] = array($h, $result[0]);

  /* Récupération du nombre de patients entrants */
  $query = "SELECT COUNT(DISTINCT `sejour_id`) FROM `affectation`
      WHERE `service_id` " . $ds->prepareIn($services_ids) . " AND `entree` <= '$date $_hour:59:59'
      AND `entree` >= '$date $_hour:00:00' AND `sejour_id` IS NOT NULL";
  $result = $ds->fetchRow($ds->exec($query));
  $series['entrants']['data'][] = array($h - 0.45, $result[0]);

  /* Récupération du nombre de patients sortants */
  $query = "SELECT COUNT(DISTINCT `sejour_id`) FROM `affectation`
      WHERE `service_id` " . $ds->prepareIn($services_ids) . " AND `sortie` <= '$date $_hour:59:59'
      AND `sortie` >= '$date $_hour:00:00' AND `sejour_id` IS NOT NULL";
  $result = $ds->fetchRow($ds->exec($query));
  $series['sortants']['data'][] = array($h, $result[0]);
}

$services_names = array();
foreach ($services_ids as $_service_id) {
  $service = new CService();
  $service->load($_service_id);
  $services_names[] = $service->nom;
}


$options = array(
  'title' => utf8_encode('Répartition du nombre de patients par heure, le ' . CMbDT::format($date, CAppUI::conf('date'))),
  'subtitle' => utf8_encode('Services : ' . implode(', ', $services_names)),
  'shadowSize' => 0,
  'xaxis' => array(
    'ticks' => $ticks,
    'title' => utf8_encode('Heure'),
  ),
  'yaxis' => array(
    'title' => utf8_encode('Nombre de patients'),
    'tickDecimals' => 0,
    'min' => 0,
    'max' => $total_lits + 5,
  ),
  'legend' => array(
    'show' => true,
    'position' => 'nw'
  ),
  'grid' => array('verticalLines' => false),
  'mouse' => array(
    'track' => true,
    'position' => 'ne',
    'relative' => true,
    'sensibility' => 2,
    'trackFormatter' => utf8_encode("(
        function(obj) {
          var label = obj.series.label;

          if (obj.series.lines.show) {
            return printf('%s : %d', label, obj.y);
          }
          else {
            if (obj.series.name == 'patients') {
              var format = 'Taux d\'occupation : %s<br/>Taux de performances : %s<br/>Nombre de patients présents de %d' + 'h à %d' + 'h : %d';
              var occupation = Math.round(100 * obj.y / graph.options.total_lits) + '%';
              var performance = Math.round(100 * obj.y / graph.options.total_lits_dispo) + '%';
              var h = parseInt(obj.x);
              return printf(format, occupation, performance, h, h + 1, obj.y);
            }
            else {
              var format = '%s de %d' + 'h à %d' + 'h : %d';
              var h = parseInt(obj.x);
              return printf(format, label, h, h + 1, obj.y);
            }
          }
        }
      )")
  ),
  'total_lits' => $total_lits,
  'total_lits_dispo' => $total_lits_dispo
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graph", array('series' => array_values($series), 'options' => $options));

$smarty->display("inc_occupation_rate.tpl");