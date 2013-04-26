<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * POCD_MT000040_ReferenceRange Class
 */
class CCDAPOCD_MT000040_ReferenceRange extends CCDARIMActRelationship {

  /**
   * @var CCDAPOCD_MT000040_ObservationRange
   */
  public $observationRange;

  /**
   * Setter observationRange
   *
   * @param CCDAPOCD_MT000040_ObservationRange $inst CCDAPOCD_MT000040_ObservationRange
   *
   * @return void
   */
  function setObservationRange(CCDAPOCD_MT000040_ObservationRange $inst) {
    $this->observationRange = $inst;
  }

  /**
   * Getter observationRange
   *
   * @return CCDAPOCD_MT000040_ObservationRange
   */
  function getObservationRange() {
    return $this->observationRange;
  }

  /**
   * Assigne typeCode à REFV
   *
   * @return void
   */
  function setTypeCode() {
    $actRela = new CCDAActRelationshipType();
    $actRela->setData("REFV");
    $this->typeCode = $actRela;
  }

  /**
   * Getter typeCode
   *
   * @return CCDAActRelationshipType
   */
  function getTypeCode() {
    return $this->typeCode;
  }


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["typeId"]           = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["observationRange"] = "CCDAPOCD_MT000040_ObservationRange xml|element required";
    $props["typeCode"]         = "CCDAActRelationshipType xml|attribute fixed|REFV";
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
     * Test avec un observationRange correct
     */

    $obRange = new CCDAPOCD_MT000040_ObservationRange();
    $obRange->setMoodCode();
    $this->setObservationRange($obRange);
    $tabTest[] = $this->sample("Test avec un observationRange correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode correct
     */

    $this->setTypeCode();
    $tabTest[] = $this->sample("Test avec un typeCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}