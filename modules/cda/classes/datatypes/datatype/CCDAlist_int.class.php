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
   * ADD a class
   *
   * @param String $listData String
   *
   * @return void
   */
  function addData($listData) {
    $int = new CCDA_base_int();
    $int->setData($listData);
    $this->listData[] = $int;
  }

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

    $this->addData("10.25");
    $tabTest[] = $this->sample("Test avec un int incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un int correcte
     */

    $this->resetListData();
    $this->addData("10");
    $tabTest[] = $this->sample("Test avec un int correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux int correcte
     */

    $this->addData("11");
    $tabTest[] = $this->sample("Test avec deux int correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
