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
 * CCDA_en_given class
 */
class CCDA_en_given extends CCDAENXP {

   private $XMLName = "en.given";

  function __construct() {
    $part = new CCDAEntityNamePartType();
    $part->setData("GIV");
    $this->setPartType($part);
  }
  /**
   * Return the name of the class
   *
   * @return string
   */
  function getNameClass() {
    return $this->XMLName;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["partType"] = "CCDAEntityNamePartType xml|attribute fixe|GIV";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {

    $tabTest = parent::test();


    /**
     * Test avec le parttype correcte
     */

    $tabTest[] = $this->sample("Test avec le parttype fixe correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
