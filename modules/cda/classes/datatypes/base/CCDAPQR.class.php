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
 * A representation of a physical quantity in a unit from
 * any code system. Used to show alternative representation
 * for a physical quantity.
 */
class CCDAPQR extends CCDACV {

  /**
   * The magnitude of the measurement value in terms of
   * the unit specified in the code.
   *
   * @var CCDA_real
   */
  public $value;

  public function setValue($value) {
    $this->value = $value;
  }

  public function getValue() {
    return $this->value;
  }


  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["value"] = "CCDA_real xml|attribute";
    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec une valeur incorrecte
     */
    $real = new CCDA_real();
    $real->setData("test");
    $this->setValue($real);
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte
     */

    $real->setData("10.5");
    $this->setValue($real);
    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}