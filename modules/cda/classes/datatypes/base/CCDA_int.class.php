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
 * Integer numbers (-1,0,1,2, 100, 3398129, etc.) are precise
 * numbers that are results of counting and enumerating.
 * Integer numbers are discrete, the set of integers is
 * infinite but countable.  No arbitrary limit is imposed on
 * the range of integer numbers. Two NULL flavors are
 * defined for the positive and negative infinity.
 */
class CCDA_int extends CCDA_Datatype {

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
    $props["data"] = "integer xml|data";
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
    $this->setData("10.25");
    $tabTest[] = $this->sample("Test avec un data incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec data correcte
     */
    $this->setData("10");
    $tabTest[] = $this->sample("Test avec un data correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
