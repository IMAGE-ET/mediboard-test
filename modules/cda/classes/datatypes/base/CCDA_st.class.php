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
 * The character string data type stands for text data,
 * primarily intended for machine processing (e.g.,
 * sorting, querying, indexing, etc.) Used for names,
 * symbols, and formal expressions.
 */
class CCDA_st extends CCDA_Datatype {

  public $data;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    parent::getProps();
    $props["data"] = "str xml|data minlength|1";
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
     * Test avec une valeur bonne
     */
    $this->setData("TESTTEST");

    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData("");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
