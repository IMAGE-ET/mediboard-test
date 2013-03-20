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
 * CCDAlist_int Class
 */
class CCDAlist_int extends CCDA_Datatype_Set {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDA_base_int xml|data";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return array()
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec un int incorrecte
     */
    $int = new CCDA_base_int();
    $int->setData("10.25");
    $this->addData($int);
    $tabTest[] = $this->sample("Test avec un int incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un int correcte
     */
    $int->setData("10");
    $this->razlistData();
    $this->addData($int);
    $tabTest[] = $this->sample("Test avec un int correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux int correcte
     */

    $int2 = new CCDA_base_int();
    $int2->setData("11");
    $this->addData($int2);
    $tabTest[] = $this->sample("Test avec deux int correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
