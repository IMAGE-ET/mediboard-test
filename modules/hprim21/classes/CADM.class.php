<?php

/**
 * Transfert de données d'admission - H'2.1
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CADM 
 * Transfert de données d'admission
 */
class CADM {
  static $versions = array (
    "2.1"
  );
  
  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrim21ADML",
    // C - Liaisons entre laboratoires et établissements cliniques ou hospitaliers
    "C" => "CHPrim21ADMC",
    // R - Liaisons entre cabinets de radiologie et établissements cliniques ou hospitaliers
    "R" => "CHPrim21ADMR",
  );
  
  function __construct() {
    $this->type = "ADM";
  }
  
  /**
   * Retrieve events list of data format
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
}

?>