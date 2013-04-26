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
 * POCD_MT000040_Component1 Class
 */
class CCDAPOCD_MT000040_Component1 extends CCDARIMActRelationship {

  /**
   * @var CCDAPOCD_MT000040_EncompassingEncounter
   */
  public $encompassingEncounter;

  /**
   * Setter encompassingEncounter
   *
   * @param CCDAPOCD_MT000040_EncompassingEncounter $inst CCDAPOCD_MT000040_EncompassingEncounter
   *
   * @return void
   */
  function setEncompassingEncounter(CCDAPOCD_MT000040_EncompassingEncounter $inst) {
    $this->encompassingEncounter = $inst;
  }

  /**
   * Getter encompassingEncounter
   *
   * @return CCDAPOCD_MT000040_EncompassingEncounter
   */
  function getEncompassingEncounter() {
    return $this->encompassingEncounter;
  }

  /**
   * Assigne typeCode à COMP
   *
   * @return void
   */
  function setTypeCode() {
    $actRela = new CCDAActRelationshipHasComponent();
    $actRela->setData("COMP");
    $this->typeCode = $actRela;
  }

  /**
   * Getter typeCode
   *
   * @return CCDAActRelationshipHasComponent
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
    $props["typeId"]                = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["encompassingEncounter"] = "CCDAPOCD_MT000040_EncompassingEncounter xml|element required";
    $props["typeCode"]              = "CCDAActRelationshipHasComponent xml|attribute fixed|COMP";
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
     * Test avec un encompassingEncounter correcte
     */

    $encou = new CCDAPOCD_MT000040_EncompassingEncounter();
    $ivl_ts = new CCDAIVL_TS();
    $hi = new CCDAIVXB_TS();
    $hi->setValue("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $ivl_ts->setHigh($hi);
    $encou->setEffectiveTime($ivl_ts);
    $this->setEncompassingEncounter($encou);
    $tabTest[] = $this->sample("Test avec un encompassingEncounter correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode correcte
     */

    $this->setTypeCode();
    $tabTest[] = $this->sample("Test avec un typeCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}