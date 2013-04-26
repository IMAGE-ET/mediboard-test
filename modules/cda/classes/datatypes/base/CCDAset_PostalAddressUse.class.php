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
 * CCDAset_PostalAddressUse Class
 */
class CCDAset_PostalAddressUse extends CCDA_Datatype_Set {

  /**
   * ADD a class
   *
   * @param String $data String
   *
   * @return void
   */
  function addData($data) {
    $tel = new CCDAPostalAddressUse();
    $tel->setData($data);
    $this->listData[] = $tel;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDAPostalAddressUse xml|data";
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
     * Test avec un PostalAddressUse incorrecte
     */

    $this->addData("TESTTEST");
    $tabTest[] = $this->sample("Test avec un PostalAddressUse incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un PostalAddressUse correcte
     */

    $this->resetListData();
    $this->addData("PST");
    $tabTest[] = $this->sample("Test avec un PostalAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux PostalAddressUse correcte
     */

    $this->addData("TMP");
    $tabTest[] = $this->sample("Test avec deux PostalAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
