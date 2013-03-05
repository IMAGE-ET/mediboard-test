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
 * A telecommunications address  specified according to
 * Internet standard RFC 1738
 * [http://www.ietf.org/rfc/rfc1738.txt]. The
 * URL specifies the protocol and the contact point defined
 * by that protocol for the resource.  Notable uses of the
 * telecommunication address data type are for telephone and
 * telefax numbers, e-mail addresses, Hypertext references,
 * FTP references, etc.
 */
class CCDA_url extends CCDA_Datatype {

  public $data;

  /**
   * Setter data
   *
   * @param \String $data String
   *
   * @return void
   */
  public function setData($data) {
    $this->data = $data;
  }

  /**
   * Getter data
   *
   * @return URI
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
    $props["data"] = "uri xml|data";
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

    $tabTest[] = $this->sample("Test avec une valeur null", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur erronée
     */

    $this->setData(":::$:!:");
    $tabTest[] = $this->sample("Test avec une valeur erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne
     */


    $this->setData("test");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
