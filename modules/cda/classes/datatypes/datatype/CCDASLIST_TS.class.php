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
 * CCDASLIST_TS class
 */
class CCDASLIST_TS extends CCDAANY {

  /**
   * The origin of the list item value scale, i.e., the
   * physical quantity that a zero-digit in the sequence
   * would represent.
   *
   * @var CCDATS
   */
  public $origin;

  /**
   * A ratio-scale quantity that is factored out of the
   * digit sequence.
   *
   * @var CCDAPQ
   */
  public $scale;

  /**
   * A sequence of raw digits for the sample values. This is
   * typically the raw output of an A/D converter.
   *
   * @var CCDAlist_int
   */
  public $digits;

  /**
   * Setter digits
   *
   * @param \CCDAlist_int $digits \CCDAlist_int
   *
   * @return void
   */
  public function setDigits($digits) {
    $this->digits = $digits;
  }

  /**
   * Getter digits
   *
   * @return \CCDAlist_int
   */
  public function getDigits() {
    return $this->digits;
  }

  /**
   * Setter origin
   *
   * @param \CCDATS $origin \CCDATS
   *
   * @return void
   */
  public function setOrigin($origin) {
    $this->origin = $origin;
  }

  /**
   * Getter origin
   *
   * @return \CCDATS
   */
  public function getOrigin() {
    return $this->origin;
  }

  /**
   * Setter scale
   *
   * @param \CCDAPQ $scale \CCDAPQ
   *
   * @return void
   */
  public function setScale($scale) {
    $this->scale = $scale;
  }

  /**
   * Getter scale
   *
   * @return \CCDAPQ
   */
  public function getScale() {
    return $this->scale;
  }

  /**
   * retourne le nom du type CDA
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    return $name;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["origin"] = "CCDATS xml|element required";
    $props["scale"] = "CCDAPQ xml|element required";
    $props["digits"] = "CCDAlist_int xml|element required";
    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = array();

    /**
     * Test avec les valeurs null
     */

    $tabTest[] = $this->sample("Test avec les valeurs null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une origin correcte
     */

    $ori= new CCDATS();
    $ts = new CCDA_base_ts();
    $ts->setData("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $ori->setValue($ts);
    $this->setOrigin($ori);
    $tabTest[] = $this->sample("Test avec une origin correcte, séquence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un scale correcte
     */

    $sca= new CCDAPQ();
    $cs = new CCDA_base_cs();
    $cs->setData("test");
    $sca->setUnit($cs);
    $this->setScale($sca);
    $tabTest[] = $this->sample("Test avec un scale correcte, séquence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un digits correcte
     */

    $dig= new CCDAlist_int();
    $int = new CCDA_base_int();
    $int->setData("10");
    $dig->addData($int);
    $this->setDigits($dig);
    $tabTest[] = $this->sample("Test avec un digts correcte, séquence correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
