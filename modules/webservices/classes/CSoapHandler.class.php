<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSoapHandler {
  static $paramSpecs = array(
    "calculatorAuth" => array ( 
      "operation" => "string",
      "entier1"   => "int",
      "entier2"   => "int")
  );
  
  static $returnSpecs = array();
  
  static function getParamSpecs() {
    return self::$paramSpecs;
  }
  
  static function getReturnSpecs() {
    return self::$returnSpecs;
  }
  
  /**
   * Calculation method (add/substract)
   * @param operation Type de l'opération (add / substract)
   * @param entier1 Premier entier
   * @param entier2 Deuxième entier
   * @return result Operation result 
   **/
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
?>