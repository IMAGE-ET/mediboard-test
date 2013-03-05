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
 * A quantity specifying a point on the axis of natural time.
 * A point in time is most often represented as a calendar
 * expression.
 */
class CCDA_ts extends CCDA_Datatype {

  public $data;

  /**
   * setter data
   *
   * @param $data
   *
   * @return void
   */
  public function setData($data) {
    $this->data = $data;
  }

  /**
   * Getter Data
   *
   * @return mixed
   */
  public function getData() {
    return $this->data;
  }
  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["data"] = "str xml|data pattern|[0-9]{1,8}|([0-9]{9,14}|[0-9]{14,14}\\.[0-9]+)([+\\-][0-9]{1,4})?";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return nothing
   */
  function test() {

    $tabTest = array();

    /**
     * Test avec une valeur null
     */

    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un use erronée
     */

    $this->setData("TESTEST");
    $tabTest[] = $this->sample("Test avec une valeur erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne
     */

    $this->setData("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
