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
   * @var CCDA_BinaryDataEncoding
   */
  public $representation;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["representation"] = "CCDA_BinaryDataEncoding xml|attribute default|TXT";
    $props["data"] = "str xml|data";
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

    $tabTest = parent::test();

    /**
     * Test avec des données
     */

    $this->setData("test");
    $tabTest[] = $this->sample("Test avec des données", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) !== "CCDABIN" || get_class($this) !== "CCDAED") {
      return $tabTest;
    }

    /**
     * Test avec une valeur incorrecte
     */

    $binaryDataEncoding = new CCDA_BinaryDataEncoding();
    $binaryDataEncoding->setData("TESTTEST");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte
     */

    $binaryDataEncoding->setData("B64");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
