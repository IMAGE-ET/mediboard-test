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
 * CCDAHXIT_PQ class
 */
class CCDAHXIT_PQ extends CCDAPQ {

  /**
   * The time interval during which the given information
   * was, is, or is expected to be valid. The interval can
   * be open or closed, as well as infinite or undefined on
   * either side.
   *
   * @var CCDAIVL_TS
   */
  public $validTime;

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
   * Setter validTime
   *
   * @param \CCDAIVL_TS $validTime \CCDAIVL_TS
   *
   * @return void
   */
  public function setValidTime($validTime) {
    $this->validTime = $validTime;
  }

  /**
   * Getter validTime
   *
   * @return \CCDAIVL_TS
   */
  public function getValidTime() {
    return $this->validTime;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["validTime"] = "CCDAIVL_TS xml|element max|1";
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
     * Test avec un validTime incorrecte
     */

    $ivl = new CCDAIVL_TS();
    $ivbx = new CCDAIVXB_TS();
    $bl = new CCDA_base_bl();
    $bl->setData("TESTTESt");
    $ivbx->setInclusive($bl);
    $ivl->setLow($ivbx);
    $this->setValidTime($ivl);
    $tabTest[] = $this->sample("Test avec un validTime incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une quantity correcte
     */

    $bl->setData("true");
    $ivbx->setInclusive($bl);
    $ivl->setLow($ivbx);
    $this->setValidTime($ivl);
    $tabTest[] = $this->sample("Test avec un validTime correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
