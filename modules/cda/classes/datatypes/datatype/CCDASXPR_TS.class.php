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
 * CCDASXPR_TS class
 */
class CCDASXPR_TS extends CCDASXCM_TS {

  /**
   * @var CCDASXCM_TS
   */
  var $comp = array();

  /**
   * ADD a class
   *
   * @param \CCDASXCM_TS $listData \CCDASXCM_TS
   *
   * @return void
   */
  function addData($listData) {
    $this->comp[] = $listData;
  }

  /**
   * Reinitialise la variable
   *
   * @return void
   */
  function razlistData () {
    $this->comp = array();
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["comp"] = "CCDASXCM_TS xml|element min|2";
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
     * Test avec une comp incorrecte
     */

    $sx = new CCDASXCM_TS();
    $op = new CCDASetOperator();
    $op->setData("TESTTEST");
    $sx->setOperator($op);
    $this->addData($sx);
    $tabTest[] = $this->sample("Test avec une comp incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une comp correcte, minimum non atteint
     */

    $op->setData("E");
    $sx->setOperator($op);
    $this->razlistData();
    $this->addData($sx);
    $tabTest[] = $this->sample("Test avec une comp correcte, minimum non atteint", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une comp incorrecte, minimum atteint
     */

    $sx2 = new CCDASXCM_TS();
    $op2 = new CCDASetOperator();
    $op2->setData("TESTTEST");
    $sx2->setOperator($op2);
    $this->addData($sx2);
    $tabTest[] = $this->sample("Test avec une comp correcte et une incorrecte, minimum atteint", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une comp incorrecte, minimum atteint
     */


    $op2->setData("P");
    $sx2->setOperator($op2);
    $this->razlistData();
    $this->addData($sx);
    $this->addData($sx2);
    $tabTest[] = $this->sample("Test avec deux comp correcte, minimum atteint", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
