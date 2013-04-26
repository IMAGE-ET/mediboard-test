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
 * CCDAUVP_TS class
 */
class CCDAUVP_TS extends CCDATS {

  /**
   * The probability assigned to the value, a decimal number
   * between 0 (very uncertain) and 1 (certain).
   *
   * @var CCDA_base_probability
   */
  public $probability;

  /**
   * Setter probability
   *
   * @param String $probability String
   *
   * @return void
   */
  public function setProbability($probability) {
    if (!$probability) {
      $this->probability = null;
      return;
    }
    $prob = new CCDA_base_probability();
    $prob->setData($probability);
    $this->probability = $prob;
  }

  /**
   * Getter probability
   *
   * @return \CCDA_base_probability
   */
  public function getProbability() {
    return $this->probability;
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
    $props["probability"] = "CCDAEIVL_event xml|attribute";
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
     * Test avec une probability incorrecte
     */

    $this->setProbability("2.0");
    $tabTest[] = $this->sample("Test avec une probability incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un probability correcte
     */

    $this->setProbability("0.80");
    $tabTest[] = $this->sample("Test avec une probability correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
