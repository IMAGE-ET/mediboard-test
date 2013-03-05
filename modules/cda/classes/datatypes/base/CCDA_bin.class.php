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
 * Binary data is a raw block of bits. Binary data is a
 * protected type that MUST not be used outside the data
 * type specification.
 */
class CCDA_bin extends CCDA_Datatype {

  public $data;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    parent::getProps();
    $props["data"] = "base64 xml|data";
    return $props;
  }

  /**
   * Modifie la variable data
   *
   * @param String $data Data
   *
   * @return void
   */
  function setData($data) {
    $this->data = $data;
  }

  function getdata() {
    return $this->data;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return void
   */
  function test() {
    $tabTest = array();
    /**
     * Test avec une valeur null
     */
    $tabTest[] = $this->sample("Test avec une valeur null", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne
     */
    $this->setData("JVBERi0xLjUNCiW1tbW1DQoxIDAgb2Jq");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData("111111111");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
