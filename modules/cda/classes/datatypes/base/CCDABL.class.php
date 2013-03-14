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
 * The Boolean type stands for the values of two-valued logic.
 * A Boolean value can be either true or
 * false, or, as any other value may be NULL.
 */
class CCDABL extends CCDAANY {

  public $value;

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["value"] = "CCDA_bl xml|attribute notNullFlavor";
    return $props;
  }

  function setValue($value) {
    $this->value = $value;
  }

  function test() {
    $tabTest = parent::test();

    /**
     * Test avec une valeur incorrecte
     */
    $bl = new CCDA_bl();
    $bl->setData("TESTTEST");
    $this->setValue($bl);

    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte
     */

    $bl->setData("true");
    $this->setValue($bl);

    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte et avec un nullflavor
     */

    $nullFlavor = new CCDANullFlavor();
    $bl->setData("true");
    $this->setValue($bl);
    $this->setNullFlavor($nullFlavor);
    $tabTest[] = $this->sample("Test avec une valeur correcte et un nullflavor", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
