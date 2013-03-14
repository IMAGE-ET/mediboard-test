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
 * A thumbnail is an abbreviated rendition of the full
 * data. A thumbnail requires significantly fewer
 * resources than the full data, while still maintaining
 * some distinctive similarity with the full data. A
 * thumbnail is typically used with by-reference
 * encapsulated data. It allows a user to select data
 * more efficiently before actually downloading through
 * the reference.
 */
class CCDAthumbnail extends CCDAED {

  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["thumbnail"] = "CCDAthumbnail xml|element prohibited";
    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec une valeur correcte
     */

    $thum = new CCDAthumbnail();
    $integrityalgo = new CCDAintegrityCheckAlgorithm();
    $integrityalgo->setData("SHA-256");
    $thum->setIntegrityCheckAlgorithm($integrityalgo);

    $this->setThumbnail($thum);
    $tabTest[] = $this->sample("Test avec un thumbnail correcte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
