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
 * A code for a common (periodical) activity of daily
 * living based on which the event related periodic
 * interval is specified.
 */
class CCDAEIVL_event extends CCDACE {

  private $name = "EIVL.event";

  function __construct() {
    $codeSystemTest = new CCDA_uid();
    $codeSystemTest->setData("2.16.840.1.113883.5.139");
    $this->setCodeSystem($codeSystemTest);

    $codeSystemNameTest = new CCDA_st();
    $codeSystemNameTest->setData("TimingEvent");
    $this->setCodeSystemName($codeSystemNameTest);
  }
  /**
   * retourne le nom du type CDA
   *
   * @return string
   */
  function getNameClass() {
    return $this->name;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["code"] = "CCDATimigEvent xml|attribute";
    $props["codeSystem"] = "CCDA_uid xml|attribute fixed|2.16.840.1.113883.5.139";
    $props["codeSystemName"] = "CCDA_st xml|attribute fixed|TimingEvent";
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
     * Test avec code incorrecte
     */
    $codeTest = new CCDATimingEvent();
    $codeTest->setData(" ");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec code correct
     */
    $codeTest->setData("ICM");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
