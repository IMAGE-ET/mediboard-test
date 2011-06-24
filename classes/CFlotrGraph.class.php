<?php /* $Id: mbGraph.class.php 8209 2010-03-04 20:01:54Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 8209 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CFlotrGraph {
  private static $profiles = array(
    // Base profile
    "base" => array(
      "legend"      => array(
        "show" => true,
        "position" => "nw",
      ),
      "grid"        => array(
        "verticalLines" => false,
        "backgroundColor" => "#FFFFFF",
      ),
      "mouse"       => array(
        "relative" => true, 
        "position" => "ne",
      ),
      "yaxis"       => array(
        "min" => 0,
        "autoscaleMargin" => 1,
      ),
      "y2axis"       => array(
        "min" => 0,
        "autoscaleMargin" => 1,
      ),
      "xaxis" => array(
        "labelsAngle" => 45,
      ),
      "HtmlText"    => false,
      "spreadsheet" => array(
        "show" => true,
        "tabGraphLabel"    => "Graphique",
        "tabDataLabel"     => "Donn&eacute;es",
        "toolbarDownload"  => "T&eacute;l&eacute;charger le fichier CSV",
        "toolbarSelectAll" => "S&eacute;lectionner le tableau",
        "csvFileSeparator" => ";",
        "decimalSeparator" => ",",
      ),
    ),
    
    // Lines graph
    "lines" => array(
      "lines"       => array("show" => true),
      "points"      => array("show" => true),
      "markers"     => array("show" => true),
      "mouse"       => array("track" => true),
    ),
    
    // Bars graph
    "bars" => array(
      "bars" => array(
        "show" => true,
        "barWidth"    => 0.8, 
        "fillOpacity" => 0.6,
      ),
    ),
    
    // Pie chart
    "pie" => array(
      "pie" => array(
        "show" => true,
        "explode" => 0,
      ),
    ),
  );
  
  static function merge($from, $options = array(), $merge_with_base = true) {
    if (is_string($from) && isset(self::$profiles[$from]))
      $from = self::$profiles[$from];
    else if (!is_array($from))
      return false;
    
    $base = $merge_with_base ? self::$profiles["base"] : array();
    return array_replace_recursive($base, $from, $options);
  }
  
  static function computeTotals(&$series, &$options) {
    $serie = array();
    
    if (count($series) <= 1) {
      $series[0]["markers"]["show"] = true;
      return;
    }
    
    $options["xaxis"]["min"] = -0.5;
    $options["xaxis"]["max"] = count($series[0]["data"])-0.5;
    
    $options["yaxis"]["min"] = 0;
    $options["yaxis"]["max"] = null;
    
    // X totals
    foreach($series as $_index => &$_serie) {
      $new_serie = array(count($series[$_index]["data"]), 0);
      
      foreach($_serie["data"] as $_key => $_data) {
        $new_serie[1] += $_data[1];
      }
      
      $series[$_index]["data"][] = $new_serie;
    }
    
    // Y totals
    foreach($series as $_index => &$_serie) {
      foreach($_serie["data"] as $_key => $_data) {
        if (!isset($serie[$_key])) $serie[$_key] = array($_data[0], 0);
        $serie[$_key][1] += $_data[1];
      }
    }
    
    foreach($serie as $_key => $_value) {
      if ($_key == count($serie)-1) break;
      $options["yaxis"]["max"] = max($_value[1], $options["yaxis"]["max"]);
    }
    
    $options["yaxis"]["max"] *= 1.1;
    
    $series[] = array(
      "data" => $serie, 
      "label" => "total", 
      //"hide" => true, 
      "markers" => array("show" => true), 
      "bars" => array("show" => false), 
      "lines" => array("show" => false),
    );
  }
}
