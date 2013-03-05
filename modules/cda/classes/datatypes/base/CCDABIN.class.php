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
class CCDABIN extends CCDAANY {

  /**
   * Specifies the representation of the binary data that
   * is the content of the binary data value.
   * @var CCDABinaryDataEncoding
   */
  public $representation;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["representation"] = "CCDABinaryDataEncoding xml|attribute default|TXT";
    return $props;
  }

  /**
   * Modifie la representation
   *
   * @param String $representation Representation
   */
  function setRepresentation($representation) {
    $this->representation = $representation;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {

    $tabTest = array();
    /**
     * Test avec une valeur null
     */

    $tabTest[] = $this->sample("Test avec une representation null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur erroné
     */

    $binaryDataEncoding = new CCDABinaryDataEncoding();
    $binaryDataEncoding->setData("TESTTEST");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur bonne
     */

    $binaryDataEncoding->setData("B64");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation bonne", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
