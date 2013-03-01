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
 * Defines the basic properties of every data value. This
 * is an abstract type, meaning that no value can be just
 * a data value without belonging to any concrete type.
 * Every concrete type is a specialization of this
 * general abstract DataValue type.
 */
class CCDAANY extends CCDA_Datatype{

  /**
   * An exceptional value expressing missing information
   * and possibly the reason why the information is missing.
   * @var CCDANullFlavor
   */
  public $nullFlavor;

  function setNullFlavor($nullFlavor) {
    $this->nullFlavor = $nullFlavor;
  }

  function getProps() {
    $props = array();
    $props["nullFlavor"] = "CCDANullFlavor attribute";

    return $props;
  }

  function test() {
    $name = $this->getName();
    $tabTest[$name] = array();
    /**
     * Test avec un nullFlavor null
     */

    $tabTest[$name][] = $this->sample("Test avec un nullFlavor null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor erroné
     */
    $this->setNullFlavor("TESTEST");

    $tabTest[$name][] = $this->sample("Test avec un nullFlavor erroné", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor bon
     */

    $this->setNullFlavor("NP");

    $tabTest[$name][] = $this->sample("Test avec un nullFlavor bon", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }


}
