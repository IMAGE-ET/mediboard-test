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
 * CCDABinaryDataEncoding class
 */
class CCDABinaryDataEncoding extends CCDA_Datatype {

  public $data;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    parent::getProps();
    $props["data"] = "str xml|data enum|B64|TXT";
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

  function getData() {
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
    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur correcte
     */
    $this->setData("B64");
    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData(" ");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
