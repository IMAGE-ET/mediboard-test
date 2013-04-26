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

  /**
   * Setter nullFlavor
   *
   * @param String $nullFlavor String
   *
   * @return void
   */
  function setNullFlavor($nullFlavor) {
    if (!$nullFlavor) {
      $this->nullFlavor = null;
      return;
    }
    $null = new CCDANullFlavor();
    $null->setData($nullFlavor);
    $this->nullFlavor = $null;
  }

  /**
   * Getter nullFlavor
   *
   * @return \CCDANullFlavor
   */
  public function getNullFlavor() {
    return $this->nullFlavor;
  }

  /**
   * Getter props
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["nullFlavor"] = "CCDANullFlavor xml|attribute";

    return $props;
  }

  /**
   * Fonction qui permet de vérifié que la classe fonctionne
   *
   * @return array
   */
  function test() {

    $tabTest = array();

    /**
     * Test avec les valeurs null
     */

    $tabTest[] = $this->sample("Test avec les valeurs null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un nullFlavor incorrecte
     */

    $this->setNullFlavor("TESTEST");

    $tabTest[] = $this->sample("Test avec un nullFlavor incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) === "CCDAANYNonNull" || get_class($this) === "CCDABN") {
      return $tabTest;
    }

    /**
     * Test avec un nullFlavor correct
     */

    $this->setNullFlavor("NP");

    $tabTest[] = $this->sample("Test avec un nullFlavor correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}