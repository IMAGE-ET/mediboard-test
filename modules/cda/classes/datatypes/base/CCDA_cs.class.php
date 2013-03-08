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
 * Coded data in its simplest form, consists of a code.
 * The code system and code system version is fixed by
 * the context in which the CS value occurs. CS is used
 * for coded attributes that have a single HL7-defined
 * value set.
 */
class CCDA_cs extends CCDA_Datatype_Voc {

  public $data;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = array();
    $props["data"] = "str xml|data pattern|[^\\s]+";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {
    if(get_class($this) !== "CCDA_cs") {
      return parent::test();
    }

    $tabTest = array();
    /**
     * Test avec un valeur null
     */

    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec un valeur erronée
     */

    $this->setData(" ");
    $tabTest[] = $this->sample("Test avec une valeur erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec un valeur correcte
     */

    $this->setData("test");
    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
