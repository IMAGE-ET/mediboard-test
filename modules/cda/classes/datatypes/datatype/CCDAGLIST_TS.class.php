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
 * CCDAGLIST_TS class
 */
class CCDAGLIST_TS extends CCDAANY {

  /**
   * This is the start-value of the generated list.
   *
   * @var CCDATS
   */
  public $head;

  /**
   * The difference between one value and its previous
   * different value. For example, to generate the sequence
   * (1; 4; 7; 10; 13; ...) the increment is 3; likewise to
   * generate the sequence (1; 1; 4; 4; 7; 7; 10; 10; 13;
   * 13; ...) the increment is also 3.
   *
   * @var CCDAPQ
   */
  public $increment;

  /**
   * If non-NULL, specifies that the sequence alternates,
   * i.e., after this many increments, the sequence item
   * values roll over to start from the initial sequence
   * item value. For example, the sequence (1; 2; 3; 1; 2;
   * 3; 1; 2; 3; ...) has period 3; also the sequence
   * (1; 1; 2; 2; 3; 3; 1; 1; 2; 2; 3; 3; ...) has period
   * 3 too.
   *
   * @var CCDA_base_int
   */
  public $period;

  /**
   * The integer by which the index for the sequence is
   * divided, effectively the number of times the sequence
   * generates the same sequence item value before
   * incrementing to the next sequence item value. For
   * example, to generate the sequence (1; 1; 1; 2; 2; 2; 3; 3;
   * 3; ...)  the denominator is 3.
   *
   * @var CCDA_base_int
   */
  public $denominator;

  /**
   * Setter denominator
   *
   * @param \CCDA_base_int $denominator \CCDA_base_int
   *
   * @return void
   */
  public function setDenominator($denominator) {
    $this->denominator = $denominator;
  }

  /**
   * Getter denominator
   *
   * @return \CCDA_base_int
   */
  public function getDenominator() {
    return $this->denominator;
  }

  /**
   * Setter head
   *
   * @param \CCDATS $head \CCDATS
   *
   * @return void
   */
  public function setHead($head) {
    $this->head = $head;
  }

  /**
   * Getter head
   *
   * @return \CCDATS
   */
  public function getHead() {
    return $this->head;
  }

  /**
   * Setter increment
   *
   * @param \CCDAPQ $increment \CCDAPQ
   *
   * @return void
   */
  public function setIncrement($increment) {
    $this->increment = $increment;
  }

  /**
   * Getter increment
   *
   * @return \CCDAPQ
   */
  public function getIncrement() {
    return $this->increment;
  }

  /**
   * Setter period
   *
   * @param \CCDA_base_int $period \CCDA_base_int
   *
   * @return void
   */
  public function setPeriod($period) {
    $this->period = $period;
  }

  /**
   * Getter period
   *
   * @return \CCDA_base_int
   */
  public function getPeriod() {
    return $this->period;
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
    $props["head"] = "CCDATS xml|element required";
    $props["increment"] = "CCDAPQ xml|element required";
    $props["period"] = "CCDA_base_int xml|attribute";
    $props["denominator"] = "CCDA_base_int xml|attribute";
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
     * Test avec une head correcte
     */

    $hea= new CCDATS();
    $ts = new CCDA_base_ts();
    $ts->setData("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $hea->setValue($ts);
    $this->setHead($hea);
    $tabTest[] = $this->sample("Test avec une head correcte, s�quence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un increment correcte
     */

    $inc= new CCDAPQ();
    $cs = new CCDA_base_cs();
    $cs->setData("test");
    $inc->setUnit($cs);
    $this->setIncrement($inc);
    $tabTest[] = $this->sample("Test avec un increment correcte, s�quence correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une period correcte
     */

    $int = new CCDA_base_int();
    $int->setData("10.25");
    $this->setPeriod($int);
    $tabTest[] = $this->sample("Test avec une period incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une period correcte
     */

    $int->setData("10");
    $this->setPeriod($int);
    $tabTest[] = $this->sample("Test avec une period correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un denominator correcte
     */

    $int = new CCDA_base_int();
    $int->setData("10.25");
    $this->setPeriod($int);
    $tabTest[] = $this->sample("Test avec un denominator incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un denominator correcte
     */

    $int->setData("10");
    $this->setPeriod($int);
    $tabTest[] = $this->sample("Test avec un denominator correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
