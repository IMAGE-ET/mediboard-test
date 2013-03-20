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
   * @var CCDA_base_cs
   */
  public $unit;

  /**
   * Ajoute une instance de translation
   *
   * @param \CCDAPQR $translation \CCDAPQR
   *
   * @return void
   */
  public function appendTranslation($translation) {
    $this->translation[] = $translation;
  }

  /**
   * Getter translation
   *
   * @return array
   */
  public function getTranslation() {
    return $this->translation;
  }

  /**
   * Setter unit
   *
   * @param \CCDA_base_cs $unit \CCDA_base_cs
   *
   * @return void
   */
  public function setUnit($unit) {
    $this->unit = $unit;
  }

  /**
   * Getter unit
   *
   * @return \CCDA_base_cs
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
    $props["value"] = "CCDA_base_real xml|attribute";
    $props["unit"] = "CCDA_base_cs xml|attribute default|1";
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

    $real = new CCDA_base_real();
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

    $cs = new CCDA_base_cs();
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
    $real = new CCDA_base_real();
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
