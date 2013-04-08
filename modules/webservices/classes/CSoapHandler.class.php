<?php

/**
 * SOAP handler
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * CSoapHandler
 */
class CSoapHandler {
  /**
   * @var array Params specs
   */
  static $paramSpecs = array(
    "calculatorAuth" => array(
      "parameters" => array(
        "operation" => "string",
        "entier1"   => "int",
        "entier2"   => "int"
      ),
      "return" => array(
        "result" => "int"
      )
    ),
  );

  /**
   * Get param specs
   *
   * @return array
   */
  static function getParamSpecs() {
    return self::$paramSpecs;
  }
  
  /**
   * Calculation method (add/substract)
   * 
   * @param string $operation Type de l'opération (add / substract)
   * @param int    $entier1   Premier entier
   * @param int    $entier2   Deuxième entier
   * 
   * @return int $result Operation result
   */
  function calculatorAuth($operation, $entier1, $entier2) {
    $result = 0;
    
    if (($operation != "add") && ($operation != "substract")) {
      return "Veuillez utiliser une methode d'operation valable (add/substract).";
    } 
    if (!$entier1 || !$entier2) {
      return "Veuillez indiquer 2 entiers.";
    }
    if ($operation == "add") {
      $result = $entier1 + $entier2;
    }
    if ($operation == "substract") {
      $result = $entier1 -$entier2;
    }
    
    return $result;
  }
}