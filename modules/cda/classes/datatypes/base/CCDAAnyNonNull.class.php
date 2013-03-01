<?php

/**
 * $Id$
 *  
 * @category ${Module}
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
    $props["nullFlavor"] = "CCDANullFlavor attribute prohibited";
    return $props;
  }

  function test() {
    $name = $this->getName();
    $tabTest[$name] = array();
    /**
     * Test avec un nullFlavor null
     */
    $tabTest[$name][] = $this->sample("Test avec un nullFlavor null", "Document valide");

    /**
     * Test avec un nullFlavor bon
     */
    $this->setNullFlavor("NP");
    $tabTest[$name][] = $this->sample("Test avec un nullFlavor bon", "Document invalide");

    /**
     * Test avec un nullFlavor incorrect
     */
    $this->setNullFlavor("TESTTEST");
    $tabTest[$name][] = $this->sample("Test avec un nullFlavor incorrect", "Document invalide");

    return $tabTest;
  }
}
