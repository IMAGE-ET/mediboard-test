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
class CCDAANYNonNull extends CCDAANY {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["nullFlavor"] = "CCDANullFlavor xml|attribute prohibited";
    return $props;
  }

  /**
   * Fonction qui permet de vérifier si la classe fonctionne
   *
   * @return array
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec un nullFlavor incorrect
     */

    $nullFlavor = new CCDANullFlavor();
    $nullFlavor->setData("TESTEST");
    $this->setNullFlavor($nullFlavor);
    $tabTest[] = $this->sample("Test avec un nullFlavor incorrect", "Document invalide");
    $this->setNullFlavor(null);

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
