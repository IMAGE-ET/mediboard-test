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
class CCDAset_PostalAddressUse extends CCDA_Datatype {

  public $listData = array();

  /**
   * ADD a CCDAPostalAddressUse to the array listData
   *
   * @param \CCDAPostalAddressUse $listData CCDAPostalAddressUse
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
    $props["listData"] = "CCDAPostalAddressUse xml|data";
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
     * Test avec un PostalAddressUse incorrecte
     */
    $post = new CCDAPostalAddressUse();
    $post->setData("TESTTEST");
    $this->addData($post);
    $tabTest[] = $this->sample("Test avec un PostalAddressUse erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un PostalAddressUse correcte
     */

    $post->setData("PST");
    $this->razlistData();
    $this->addData($post);
    $tabTest[] = $this->sample("Test avec un PostalAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux PostalAddressUse correcte
     */

    $post2 = new CCDAPostalAddressUse();
    $post2->setData("TMP");
    $this->addData($post2);
    $tabTest[] = $this->sample("Test avec deux PostalAddressUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
