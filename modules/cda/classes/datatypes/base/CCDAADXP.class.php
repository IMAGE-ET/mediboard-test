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
 * A character string that may have a type-tag signifying its
 * role in the address. Typical parts that exist in about
 * every address are street, house number, or post box,
 * postal code, city, country but other roles may be defined
 * regionally, nationally, or on an enterprise level (e.g. in
 * military addresses). Addresses are usually broken up into
 * lines, which are indicated by special line-breaking
 * delimiter elements (e.g., DEL).
 */
class CCDAADXP extends CCDAST {

  /**
   * Specifies whether an address part names the street,
   * city, country, postal code, post box, etc. If the type
   * is NULL the address part is unclassified and would
   * simply appear on an address label as is.
   *
   * @var CCDAAddressPartType
   */
  public $partType;

  /**
   * @param \CCDAAddressPartType $partType
   */
  public function setPartType($partType) {
    $this->partType = $partType;
  }

  /**
   * @return \CCDAAddressPartType
   */
  public function getPartType() {
    return $this->partType;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["partType"] = "CCDAAdressPartType xml|attribute";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {

    $tabTest = parent::test();

    if (get_class($this) !== "CCDAADXP") {
      return $tabTest;
    }
    /**
     * Test avec un parttype incorrecte
     */

    $part = new CCDAAddressPartType();
    $part->setData("TEstTEst");
    $this->setPartType($part);

    $tabTest[] = $this->sample("Test avec un parttype incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un parttype correcte
     */

    $part->setData("ZIP");
    $this->setPartType($part);

    $tabTest[] = $this->sample("Test avec un parttype correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
