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
 * CCDABXIT_CD class
 */
class CCDABXIT_CD extends CCDACD {

  /**
   * The quantity in which the bag item occurs in its containing bag.
   *
   * @var CCDASetOperator
   */
  public $qty;

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
   * Setter qty
   *
   * @param \CCDA_base_int $qty \CCDA_base_int
   *
   * @return void
   */
  public function setQty($qty) {
    $this->qty = $qty;
  }

  /**
   * Getter qty
   *
   * @return \CCDA_base_int
   */
  public function getQty() {
    return $this->qty;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["qty"] = "CCDA_base_int xml|attribute default|1";
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
     * Test avec une quantity incorrecte
     */

    $int = new CCDA_base_int();
    $int->setData("10.25");
    $this->setQty($int);
    $tabTest[] = $this->sample("Test avec une quantity incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une quantity correcte
     */

    $int->setData("10");
    $this->setQty($int);
    $tabTest[] = $this->sample("Test avec une quantity correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
