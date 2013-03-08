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
class CCDAset_TelecommunicationAddressUse extends CCDA_Datatype {

  public $listData = array();

  /**
   * ADD a CCDATelecommunicationAddressUse to the array listData
   *
   * @param \CCDATelecommunicationAddressUse $listData CCDATelecommunicationAddressUse
   *
   * @return void
   */
  function addData($listData) {
    $this->listData[] = $listData;
  }

  /**
   * Reinitialise la variable
   *
   * @return void
   */
  function razlistData () {
    $this->listData = array();
  }

  /**
   * retourne le nom de la classe
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    return $name;
  }

  /**
   * Getter listData
   *
   * @return array
   */
  public function getData() {
    $listdata = "";
    foreach ($this->listData as $_tel) {
      $listdata .= $_tel->getData()." ";
    }
    $listdata = substr($listdata, 0, strlen($listdata)-1);
    return $listdata;
  }

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
    $tabTest = array();

    /**
     * Test avec les valeurs nulls
     */
    $tabTest[] = $this->sample("Test avec les valeurs null", "Document valide");
    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un TelecommunicationAddressUse incorrecte
     */
    $tel = new CCDATelecommunicationAddressUse();
    $tel->setData("TESTTEST");
    $this->addData($tel);
    $tabTest[] = $this->sample("Test avec un TelecommunicationAddressUse erronée", "Document invalide");

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
