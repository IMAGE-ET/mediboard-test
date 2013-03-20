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
class CCDAURL extends CCDAANY {

  public $value;

  /**
   * Setter value
   *
   * @param \String $value String
   *
   * @return void
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * Getter value
   *
   * @return CCDA_base_url
   */
  public function getValue() {
    return $this->value;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["value"] = "url xml|attribute";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return array()
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec une valeur incorrecte
     */

    $url = new CCDA_base_url();
    $url->setData(":::$:!:");
    $this->setValue($url);
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur correcte
     */

    $url->setData("test");
    $this->setValue($url);
    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
