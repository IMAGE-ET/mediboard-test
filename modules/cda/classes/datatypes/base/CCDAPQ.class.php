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
 * A dimensioned quantity expressing the result of a
 * measurement act.
 */
class CCDAPQ extends CCDAQTY {

  /**
   * An alternative representation of the same physical
   * quantity expressed in a different unit, of a different
   * unit code system and possibly with a different value.
   *
   * @var array
   */
  var $translation = array();

  /**
   * The unit of measure specified in the Unified Code for
   * Units of Measure (UCUM)
   * [http://aurora.rg.iupui.edu/UCUM].
   *
   * @var CCDA_cs
   */
  public $unit;

  /**
   * @param \CCDAPQR
   */
  public function appendTranslation($translation) {
    $this->translation[] = $translation;
  }

  /**
   * @return array
   */
  public function getTranslation() {
    return $this->translation;
  }

  /**
   * @param \CCDA_cs $unit
   */
  public function setUnit($unit) {
    $this->unit = $unit;
  }

  /**
   * @return \CCDA_cs
   */
  public function getUnit() {
    return $this->unit;
  }



  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["translation"] = "CCDAPQR xml|element";
    $props["value"] = "CCDA_real xml|attribute";
    $props["unit"] = "CCDA_cs xml|attribute default|1";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec une valeur incorrecte
     */

    $real = new CCDA_real();
    $real->setData("test");
    $this->setValue($real);
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte
     */

    $real->setData("10.25");
    $this->setValue($real);
    $tabTest[] = $this->sample("Test avec une valeur correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une unit incorrecte
     */

    $cs = new CCDA_cs();
    $cs->setData(" ");
    $this->setUnit($cs);
    $tabTest[] = $this->sample("Test avec une unit incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une unit correcte
     */

    $cs->setData("test");
    $this->setUnit($cs);
    $tabTest[] = $this->sample("Test avec une unit correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une translation incorrecte
     */

    $pqr = new CCDAPQR();
    $real = new CCDA_real();
    $real->setData("test");
    $pqr->setValue($real);
    $this->appendTranslation($pqr);
    $tabTest[] = $this->sample("Test avec une translation incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une translation correcte
     */

    $real->setData("10.25");
    $pqr->setValue($real);
    $this->appendTranslation($pqr);
    $tabTest[] = $this->sample("Test avec une translation correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
