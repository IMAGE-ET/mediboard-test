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
 * A DCE Universal Unique Identifier is a globally unique
 * string consisting of 5 groups of upper- or lower-case
 * hexadecimal digits having 8, 4, 4, 4, and 12 places
 * respectively. UUIDs are assigned using Ethernet MAC
 * addresses, the point in time of creation and some random
 * components. This mix is believed to generate sufficiently
 * unique identifiers without any organizational policy for
 * identifier assignment (in fact this piggy-backs on the
 * organization of MAC address assignment.)
 */
class CCDA_uuid extends CCDA_Datatype {

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
    $props["data"] = "str xml|data pattern|[0-9a-zA-Z]{8}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{12}";
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
    $this->setData("azer1254-azer-azer-azer-Azert1257825");
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
