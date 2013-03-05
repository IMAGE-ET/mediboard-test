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
 * A globally unique string representing an ISO Object Identifier
 * (OID) in a form that consists only of non-negative numbers with
 * no leading zeros and dots (e.g., "2.16.840.1.113883.3.1").
 * According to ISO, OIDs are paths in a tree structure, with the
 * left-most number representing the root and the right-most number
 * representing a leaf.
 */
class CCDA_oid extends CCDA_Datatype {

  public $data;

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public function getValue() {
    return $this->data;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["data"] = "str xml|data pattern|[0-2](\\.(0|[1-9][0-9]*))*";
    return $props;
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
    $this->setData("2.16.840.1.113883.3.1");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData("TESTTEST");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
