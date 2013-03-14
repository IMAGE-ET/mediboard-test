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
 * Description
 */
class CCDARTO_QTY_QTY extends CCDAQTY {

  /**
   * The quantity that is being divided in the ratio.  The
   * default is the integer number 1 (one).
   *
   * @var CCDAQTY
   */
  public $numerator;

  /**
   * The quantity that devides the numerator in the ratio.
   * The default is the integer number 1 (one).
   * The denominator must not be zero.
   *
   * @var CCDAQTY
   */
  public $denominator;

  /**
   * retourne le nom de la classe
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    return $name;
  }

  /**
   * @param \CCDAQTY $denominator
   */
  public function setDenominator($denominator) {
    $this->denominator = $denominator;
  }

  /**
   * @return \CCDAQTY
   */
  public function getDenominator() {
    return $this->denominator;
  }

  /**
   * @param \CCDAQTY $numerator
   */
  public function setNumerator($numerator) {
    $this->numerator = $numerator;
  }

  /**
   * @return \CCDAQTY
   */
  public function getNumerator() {
    return $this->numerator;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["numerator"] = "CCDAQTY xml|element default|1 abstract";
    $props["denominator"] = "CCDAQTY xml|element default|1 abstract";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {
    $tabTest = array();

    /**
     * Test avec un numerator incorrecte
     */

    /**
     * Test avec les valeurs null
     */

    $tabTest[] = $this->sample("Test avec les valeurs null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    $num = new CCDAINT();
    $int = new CCDA_int();
    $int->setData("10.25");
    $num->setValue($int);
    $this->setNumerator($num);
    $tabTest[] = $this->sample("Test avec un numerator incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un numerator correcte
     */

    $int->setData("10");
    $num->setValue($int);
    $this->setNumerator($num);
    $tabTest[] = $this->sample("Test avec un numerator correcte, séquence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un denominator incorrecte
     */

    $num = new CCDAINT();
    $int = new CCDA_int();
    $int->setData("10.25");
    $num->setValue($int);
    $this->setDenominator($num);
    $tabTest[] = $this->sample("Test avec un denominator incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un denominator correcte
     */

    $int->setData("15");
    $num->setValue($int);
    $this->setDenominator($num);
    $tabTest[] = $this->sample("Test avec un denominator correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un numerator correcte
     */

    $num = new CCDAREAL();
    $real = new CCDA_real();
    $real->setData("10.25");
    $num->setValue($real);
    $this->setDenominator($num);
    $tabTest[] = $this->sample("Test avec un denominator correcte en real", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
