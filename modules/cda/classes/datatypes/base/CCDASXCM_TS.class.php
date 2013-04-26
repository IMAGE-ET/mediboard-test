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
 * CCDASXCM_TS class
 */
class CCDASXCM_TS extends CCDATS {

  /**
   * A code specifying whether the set component is included
   * (union) or excluded (set-difference) from the set, or
   * other set operations with the current set component and
   * the set as constructed from the representation stream
   * up to the current point.
   */
  public $operator;

  /**
   * Setter operator
   *
   * @param String $operator String
   *
   * @return void
   */
  public function setOperator($operator) {
    if (!$operator) {
      $this->operator = null;
      return;
    }
    $setOP = new CCDASetOperator();
    $setOP->setData($operator);
    $this->operator = $setOP;
  }

  /**
   * Getter operator
   *
   * @return CCDASetOperator
   */
  public function getOperator() {
    return $this->operator;
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
    $props["operator"] = "CCDASetOperator xml|attribute default|I";
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
     * Test avec un operator incorrecte
     */

    $this->setOperator("TESTTEST");
    $tabTest[] = $this->sample("Test avec un operator correcte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un operator correcte
     */

    $this->setOperator("H");
    $tabTest[] = $this->sample("Test avec un operator correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
