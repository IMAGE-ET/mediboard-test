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

/**
 * Génération des données des graphiques du palmarès ressources
 *
 * @param string $module   Module concerné
 * @param string $date     Date de référence
 * @param string $element  Type de données à afficher
 * @param string $interval Interval de temps à analyser
 * @param int    $numelem  Nombre d'éléments maximum
 *
 * @return array Les données de palmarès
 */

function graphRessourceLog($module, $date, $element = 'duration', $interval = 'day', $numelem = 4) {
  if (!$date) $date = CMbDT::date();

  switch ($interval) {
    default:
    case "day":
      $startx = "$date 00:00:00";
      $endx   = "$date 23:59:59";
      break;
    case "month":
      $startx = CMbDT::dateTime("-1 MONTH", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      break;
    case "hyear":
      $startx = CMbDT::dateTime("-27 WEEKS", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      break;
  }

  if ($module == "total") {
    $groupmod = 0;
    $module_name = null;
  }
  elseif ($module == "modules") {
    $groupmod = 1;
    $module_name = null;
  }
  else {
    $groupmod = 0;
    $module_name = $module;
  }

  $logs = CAccessLog::loadAgregation($startx, $endx, $groupmod, $module_name);

  $series = array();
  $i = 0;
  foreach ($logs as $data) {
    $series[$i]["data"]  = array(array(0, $data->$element));
    $series[$i]["label"] = $module != 'modules' ? $data->action : $data->module;
    $i++;
  }

  if (!function_exists('compareDataPie')) {
    /**
     * Comparaison entre deux données du graphique en pie
     *
     * @param array $a Première donnée
     * @param array $b Deuxième donnée
     *
     * @return bool La première valeur est-elle inférieure à la deuxième
     */
    function compareDataPie($a, $b) {
      return $a["data"][0][1] < $b["data"][0][1];
    }
  }

  usort($series, "compareDataPie");
  $seriesNew = array_slice($series, 0, $numelem);
  if (count($series) > $numelem) {
    $other = array_slice($series, $numelem);
    $seriesNew[$numelem]["data"] = array(array(0, 0));
    $seriesNew[$numelem]["label"] = "Autres";
    $n = 0;
    foreach ($other as $_other) {
      $seriesNew[$numelem]["data"][0][1] += $_other["data"][0][1];
      $n++;
    }
    $seriesNew[$numelem]["label"] .= " ($n)";
  }
  $series = $seriesNew;

  // Set up the title for the graph
  $title = CMbDT::format($date, "%A %d %b %Y");
  if ($module) {
    $title .= " : ".CAppUI::tr($module);
  }

  $options = array(
    'title' => utf8_encode($title),
    'HtmlText' => false,
    'grid' => array(
      'verticalLines' => false,
      'horizontalLines' => false,
      'outlineWidth' => 0
    ),
    'xaxis' => array('showLabels' => false),
    'yaxis' => array('showLabels' => false),
    'pie' => array(
      'show' => true,
      'sizeRatio' => 0.5
    ),
    'legend' => array(
      'backgroundOpacity' => 0.3
    )
  );

  return array('series' => $series, 'options' => $options);
}
