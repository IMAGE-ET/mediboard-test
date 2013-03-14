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
 * CCDAset_TelecommunicationAddressUse Class
 */
class CCDAset_TelecommunicationAddressUse extends CCDA_Datatype_Set {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDATelecommunicationAddressUse xml|data";
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
     * Test avec un TelecommunicationAddressUse incorrecte
     */
    $tel = new CCDATelecommunicationAddressUse();
    $tel->setData("TESTTEST");
    $this->addData($tel);
    $tabTest[] = $this->sample("Test avec un TelecommunicationAddressUse incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un TelecommunicationAddressUse correcte
     */
    $tel->setData("AS");
    $this->razlistData();
    $this->addData($tel);
    $tabTest[] = $this->sample("Test avec un TelecommunicationAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux TelecommunicationAddressUse correcte
     */

    $tel2 = new CCDATelecommunicationAddressUse();
    $tel2->setData("BAD");
    $this->addData($tel2);
    $tabTest[] = $this->sample("Test avec deux TelecommunicationAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
