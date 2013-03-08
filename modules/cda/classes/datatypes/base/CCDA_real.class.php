<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Fractional numbers. Typically used whenever quantities
 * are measured, estimated, or computed from other real
 * numbers.  The typical representation is decimal, where
 * the number of significant decimal digits is known as the
 * precision. Real numbers are needed beyond integers
 * whenever quantities of the real world are measured,
 * estimated, or computed from other real numbers. The term
 * "Real number" in this specification is used to mean
 * that fractional values are covered without necessarily
 * implying the full set of the mathematical real numbers.
 */
class CCDA_real extends CCDA_Datatype {

  public $data;

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    parent::getProps();
    $props["data"] = "float xml|data";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {
    $tabTest = array();

    /**
     * test avec des valeurs null
     */

    $tabTest[] = $this->sample("Test avec des valeurs Null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec data incorrecte
     */
    $this->setData("test");
    $tabTest[] = $this->sample("Test avec un data incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec data correcte
     */
    $this->setData("10.25");
    $tabTest[] = $this->sample("Test avec un data correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
