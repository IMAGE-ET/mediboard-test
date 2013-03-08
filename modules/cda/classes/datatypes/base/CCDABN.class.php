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
    $props["value"] = "CCDA_bn xml|attribute";
    return $props;
  }

  function setValue($value) {
    $this->value = $value;
  }

  function test() {
    $tabTest = array();
    /**
     * Test avec une valeur null
     */
    $tabTest[] = $this->sample("Test avec une valeur null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur erroné
     */
    $bn = new CCDA_bn();
    $bn->setData("TESTTEST");
    $this->setValue($bn);

    $tabTest[] = $this->sample("Test avec une valeur erronée", "Document invalide");

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
