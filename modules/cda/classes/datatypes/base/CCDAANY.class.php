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
    parent::getProps();
    $props["nullFlavor"] = "CCDANullFlavor xml|attribute";

    return $props;
  }

  function test() {
    $tabTest = array();
    /**
     * Test avec un nullFlavor null
     */

    $tabTest[] = $this->sample("Test avec un nullFlavor null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor erroné
     */
    $nullFlavor = new CCDANullFlavor();
    $nullFlavor->setData("TESTEST");
    $this->setNullFlavor($nullFlavor);

    $tabTest[] = $this->sample("Test avec un nullFlavor erroné", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor bon
     */
    $nullFlavor->setData("NP");
    $this->setNullFlavor($nullFlavor);

    $tabTest[] = $this->sample("Test avec un nullFlavor bon", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}