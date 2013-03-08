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

  function test() {

    $tabTest = array();
    /**
     * Test avec un nullFlavor null
     */
    $tabTest[] = $this->sample("Test avec un nullFlavor null", "Document valide");

    /**
     * Test avec un nullFlavor correct
     */
    $nullFlavor = new CCDANullFlavor();
    $nullFlavor->setData("NP");
    $this->setNullFlavor($nullFlavor);
    $tabTest[] = $this->sample("Test avec un nullFlavor correct", "Document invalide");

    /**
     * Test avec un nullFlavor incorrect
     */

    $nullFlavor->setData("TESTEST");
    $this->setNullFlavor($nullFlavor);
    $tabTest[] = $this->sample("Test avec un nullFlavor incorrect", "Document invalide");

    return $tabTest;
  }
}
