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
}
