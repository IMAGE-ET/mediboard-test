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
 * CCDABXIT_IVL_PQ class
 */
class CCDABXIT_IVL_PQ extends CCDAIVL_PQ {

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
   * @param String $qty String
   *
   * @return void
   */
  public function setQty($qty) {
    if (!$qty) {
      $this->qty = null;
      return;
    }
    $int = new CCDA_base_int();
    $int->setData($qty);
    $this->qty = $int;
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

    $this->setQty("10.25");
    $tabTest[] = $this->sample("Test avec une quantity incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une quantity correcte
     */

    $this->setQty("10");
    $tabTest[] = $this->sample("Test avec une quantity correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
