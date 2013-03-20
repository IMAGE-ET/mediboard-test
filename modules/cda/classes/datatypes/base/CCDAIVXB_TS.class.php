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
 * CCDAIVXB_TS class
 */
class CCDAIVXB_TS extends CCDATS {

  /**
   * Specifies whether the limit is included in the
   * interval (interval is closed) or excluded from the
   * interval (interval is open).
   *
   * @var CCDA_base_bl
   */
  public $inclusive;

  /**
   * Setter inclusive
   *
   * @param \CCDA_base_bl $inclusive \CCDA_base_bl
   *
   * @return void
   */
  public function setInclusive($inclusive) {
    $this->inclusive = $inclusive;
  }

  /**
   * Getter inclusive
   *
   * @return \CCDA_base_bl
   */
  public function getInclusive() {
    return $this->inclusive;
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
    $props["inclusive"] = "CCDA_base_bl xml|attribute default|true";
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
     * test avec inclusive incorrecte
     */

    $bl = new CCDA_base_bl();
    $bl->setData("test");
    $this->setInclusive($bl);
    $tabTest[] = $this->sample("Test avec un inclusive incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec inclusive correcte
     */

    $bl = new CCDA_base_bl();
    $bl->setData("true");
    $this->setInclusive($bl);
    $tabTest[] = $this->sample("Test avec un inclusive correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }

}
