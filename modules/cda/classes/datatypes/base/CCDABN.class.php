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
 * The BooleanNonNull type is used where a Boolean cannot
 * have a null value. A Boolean value can be either
 * true or false.
 */
class CCDABN extends CCDAANYNonNull {

  public $value;

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["value"] = "CCDA_base_bn xml|attribute";
    return $props;
  }

  /**
   * Setter value
   *
   * @param CCDA_base_bn $value CCDA_base_bn
   *
   * @return CCDA_base_bn
   */
  function setValue($value) {
    $this->value = $value;
  }

  /**
   * Getter value
   *
   * @return CCDA_base_bn
   */
  function getvalue() {
    return $this->value;
  }

  /**
   * Foncntion permettant de tester la classe
   *
   * @return array
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec une valeur incorrecte
     */

    $bn = new CCDA_base_bn();
    $bn->setData("TESTTEST");
    $this->setValue($bn);

    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte
     */

    $bn->setData("true");
    $this->setValue($bn);

    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
