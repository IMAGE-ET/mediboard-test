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
 * A telephone number (voice or fax), e-mail address, or
 * other locator for a resource (information or service)
 * mediated by telecommunication equipment. The address
 * is specified as a Universal Resource Locator (URL)
 * qualified by time specification and use codes that help
 * in deciding which address to use for a given time and
 * purpose.
 */
class CCDATEL extends CCDAURL {

  /**
   * Specifies the periods of time during which the
   * telecommunication address can be used.  For a
   * telephone number, this can indicate the time of day
   * in which the party can be reached on that telephone.
   * For a web address, it may specify a time range in
   * which the web content is promised to be available
   * under the given address.
   *
   * @var CCDASXCM_TS
   */
  public $useablePeriod;

  /**
   * One or more codes advising a system or user which
   * telecommunication address in a set of like addresses
   * to select for a given telecommunication need.
   *
   * @var CCDAset_TelecommunicationAddressUse
   */
  public $use;

  /**
   * Setter use
   *
   * @param \CCDAset_TelecommunicationAddressUse $use set_TelecommunicationAddressUse
   *
   * @return void
   */
  public function setUse($use) {
    $this->use = $use;
  }

  /**
   * Getter use
   *
   * @return \CCDAset_TelecommunicationAddressUse
   */
  public function getUse() {
    return $this->use;
  }

  /**
   * Setter useablePeriod
   *
   * @param \CCDASXCM_TS $useablePeriod SXCM_TS
   *
   * @return void
   */
  public function setUseablePeriod($useablePeriod) {
    $this->useablePeriod = $useablePeriod;
  }

  /**
   * Getter useablePeriod
   *
   * @return \CCDASXCM_TS
   */
  public function getUseablePeriod() {
    return $this->useablePeriod;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["useablePeriod"] = "CCDASXCM_TS xml|element";
    $props["use"] = "CCDAset_TelecommunicationAddressUse xml|attribute";
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
     * Test avec un useablePeriod incorrecte
     */

    $useable = new CCDASXCM_TS();
    $op = new CCDASetOperator();
    $op->setData("TESTEST");
    $useable->setOperator($op);
    $this->setUseablePeriod($useable);
    $tabTest[] = $this->sample("Test avec une useablePeriod incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un useablePeriod correct
     */

    $op->setData("H");
    $useable->setOperator($op);
    $this->setUseablePeriod($useable);
    $tabTest[] = $this->sample("Test avec un useablePeriod correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un use incorrecte
     */

    $us = new CCDAset_TelecommunicationAddressUse();
    $tel = new CCDATelecommunicationAddressUse();
    $tel->setData("TESTTEST");
    $us->addData($tel);
    $this->setUse($us);
    $tabTest[] = $this->sample("Test avec un use incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un use correct
     */

    $tel->setData("AS");
    $us->razlistData();
    $us->addData($tel);
    $this->setUse($us);
    $tabTest[] = $this->sample("Test avec un use correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
