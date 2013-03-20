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
 * Fractional numbers. Typically used whenever quantities
 * are measured, estimated, or computed from other real
 * numbers.  The typical representation is decimal, where
 * the number of significant decimal digits is known as the
 * precision. Real numbers are needed beyond integers
 * whenever quantities of the real world are measured,
 * estimated, or computed from other real numbers. The term
 * "Real number" in this specification is used to mean
 * that fractional values are covered without necessarily
 * implying the full set of the mathematical real numbers.
 */
class CCDA_base_real extends CCDA_Datatype_Base {

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    parent::getProps();
    $props["data"] = "float xml|data";
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
     * test avec data incorrecte
     */
    $this->setData("test");
    $tabTest[] = $this->sample("Test avec un data incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec data correcte
     */
    $this->setData("10.25");
    $tabTest[] = $this->sample("Test avec un data correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
