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
 * POCD_MT000040_Participant1 Class
 */
class CCDAPOCD_MT000040_Participant1 extends CCDARIMParticipation {

  /**
   * @var CCDAPOCD_MT000040_AssociatedEntity
   */
  public $associatedEntity;

  /**
   * Setter functionCode
   *
   * @param CCDACE $inst CCDACE
   *
   * @return void
   */
  function setFunctionCode(CCDACE $inst) {
    $this->functionCode = $inst;
  }

  /**
   * Getter functionCode
   *
   * @return CCDACE
   */
  function getFunctionCode() {
    return $this->functionCode;
  }

  /**
   * Setter time
   *
   * @param CCDAIVL_TS $inst CCDAIVL_TS
   *
   * @return void
   */
  function setTime(CCDAIVL_TS $inst) {
    $this->time = $inst;
  }

  /**
   * Getter time
   *
   * @return CCDAIVL_TS
   */
  function getTime() {
    return $this->time;
  }

  /**
   * Setter associatedEntity
   *
   * @param CCDAPOCD_MT000040_AssociatedEntity $inst CCDAPOCD_MT000040_AssociatedEntity
   *
   * @return void
   */
  function setAssociatedEntity(CCDAPOCD_MT000040_AssociatedEntity $inst) {
    $this->associatedEntity = $inst;
  }

  /**
   * Getter associatedEntity
   *
   * @return CCDAPOCD_MT000040_AssociatedEntity
   */
  function getAssociatedEntity() {
    return $this->associatedEntity;
  }

  /**
   * Setter typeCode
   *
   * @param String $inst String
   *
   * @return void
   */
  function setTypeCode($inst) {
    if (!$inst) {
      $this->typeCode = null;
      return;
    }
    $part = new CCDAParticipationType();
    $part->setData($inst);
    $this->typeCode = $part;
  }

  /**
   * Getter typeCode
   *
   * @return CCDAParticipationType
   */
  function getTypeCode() {
    return $this->typeCode;
  }

  /**
   * Assigne contextControlCode à OP
   *
   * @return void
   */
  function setContextControlCode() {
    $context = new CCDAContextControl();
    $context->setData("OP");
    $this->contextControlCode = $context;
  }

  /**
   * Getter contextControlCode
   *
   * @return CCDAContextControl
   */
  function getContextControlCode() {
    return $this->contextControlCode;
  }


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["typeId"]             = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["functionCode"]       = "CCDACE xml|element max|1";
    $props["time"]               = "CCDAIVL_TS xml|element max|1";
    $props["associatedEntity"]   = "CCDAPOCD_MT000040_AssociatedEntity xml|element required";
    $props["typeCode"]           = "CCDAParticipationType xml|attribute required";
    $props["contextControlCode"] = "CCDAContextControl xml|attribute fixed|OP";
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
     * Test avec un associatedEntity correct
     */

    $associated = new CCDAPOCD_MT000040_AssociatedEntity();
    $associated->setClassCode("RoleClassPassive");
    $this->setAssociatedEntity($associated);
    $tabTest[] = $this->sample("Test avec un associatedEntity correct", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode incorrect
     */

    $this->setTypeCode("TESTTEST");
    $tabTest[] = $this->sample("Test avec un typeCode incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode correct
     */

    $this->setTypeCode("CST");
    $tabTest[] = $this->sample("Test avec un typeCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un contextControlCode correct
     */

    $this->setContextControlCode();
    $tabTest[] = $this->sample("Test avec un contextControlCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un functionCode incorrect
     */

    $ce = new CCDACE();
    $ce->setCode(" ");
    $this->setFunctionCode($ce);
    $tabTest[] = $this->sample("Test avec un functionCode incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un functionCode correct
     */

    $ce->setCode("TEST");
    $this->setFunctionCode($ce);
    $tabTest[] = $this->sample("Test avec un functionCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un effectiveTime incorrect
     */

    $ivl_ts = new CCDAIVL_TS();
    $hi = new CCDAIVXB_TS();
    $hi->setValue("TESTTEST");
    $ivl_ts->setHigh($hi);
    $this->setTime($ivl_ts);
    $tabTest[] = $this->sample("Test avec un effectiveTime incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un effectiveTime correct
     */

    $hi->setValue("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $ivl_ts->setHigh($hi);
    $this->setTime($ivl_ts);
    $tabTest[] = $this->sample("Test avec un effectiveTime correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}