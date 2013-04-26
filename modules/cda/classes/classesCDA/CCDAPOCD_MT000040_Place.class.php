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
 * POCD_MT000040_Place Class
 */
class CCDAPOCD_MT000040_Place extends CCDARIMPlace {


  /**
   * Setter name
   *
   * @param CCDAEN $inst CCDAEN
   *
   * @return void
   */
  function setName(CCDAEN $inst) {
    $this->name = $inst;
  }

  /**
   * Getter name
   *
   * @return CCDAEN
   */
  function getName() {
    return $this->name;
  }

  /**
   * Setter addr
   *
   * @param CCDAAD $inst CCDAAD
   *
   * @return void
   */
  function setAddr(CCDAAD $inst) {
    $this->addr = $inst;
  }

  /**
   * Getter addr
   *
   * @return CCDAAD
   */
  function getAddr() {
    return $this->addr;
  }

  /**
   * ASsigne classCode à PLC
   *
   * @return void
   */
  function setClassCode() {
    $entityClass = new CCDAEntityClassPlace();
    $entityClass->setData("PLC");
    $this->classCode = $entityClass;
  }

  /**
   * Getter classCode
   *
   * @return CCDAEntityClassPlace
   */
  function getClassCode() {
    return $this->classCode;
  }

  /**
   * Assigne determinerCode à INSTANCE
   *
   * @return void
   */
  function setDeterminerCode() {
    $determiner = new CCDAEntityDeterminer();
    $determiner->setData("INSTANCE");
    $this->determinerCode = $determiner;
  }

  /**
   * Getter determinerCode
   *
   * @return CCDAEntityDeterminer
   */
  function getDeterminerCode() {
    return $this->determinerCode;
  }


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["typeId"]         = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["name"]           = "CCDAEN xml|element max|1";
    $props["addr"]           = "CCDAAD xml|element max|1";
    $props["classCode"]      = "CCDAEntityClassPlace xml|attribute fixed|PLC";
    $props["determinerCode"] = "CCDAEntityDeterminer xml|attribute fixed|INSTANCE";
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
     * Test avec les valeurs null
     */

    $tabTest[] = $this->sample("Test avec les valeurs null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeId correct
     */

    $this->setTypeId();
    $tabTest[] = $this->sample("Test avec un typeId correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un realmCode incorrect
     */

    $cs = new CCDACS();
    $cs->setCode(" ");
    $this->appendRealmCode($cs);
    $tabTest[] = $this->sample("Test avec un realmCode incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un realmCode correct
     */

    $cs->setCode("FR");
    $this->resetListRealmCode();
    $this->appendRealmCode($cs);
    $tabTest[] = $this->sample("Test avec un realmCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un templateId incorrect
     */

    $ii = new CCDAII();
    $ii->setRoot("4TESTTEST");
    $this->appendTemplateId($ii);
    $tabTest[] = $this->sample("Test avec un templateId incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un templateId correct
     */

    $ii->setRoot("1.2.250.1.213.1.1.1.1");
    $this->resetListTemplateId();
    $this->appendTemplateId($ii);
    $tabTest[] = $this->sample("Test avec un templateId correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un name incorrect
     */

    $en = new CCDAEN();
    $en->setUse(array("TESTTEST"));
    $this->setName($en);
    $tabTest[] = $this->sample("Test avec un name incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un name incorrect
     */

    $en->setUse(array("I"));
    $this->setName($en);
    $tabTest[] = $this->sample("Test avec un name icorrect", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un addr incorrect
     */

    $ad = new CCDAAD();
    $ad->setUse(array("TESTTEST"));
    $this->setAddr($ad);
    $tabTest[] = $this->sample("Test avec un addr incorrect", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un addr correct
     */

    $ad->setUse(array("PST"));
    $this->setAddr($ad);
    $tabTest[] = $this->sample("Test avec un addr correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un classCode correct
     */

    $this->setClassCode();
    $tabTest[] = $this->sample("Test avec un classCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un determinerCode correct
     */

    $this->setDeterminerCode();
    $tabTest[] = $this->sample("Test avec un determinerCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}