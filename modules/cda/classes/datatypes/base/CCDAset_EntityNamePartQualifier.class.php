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
 * CCDAset_EntityNamePartQualifier Class
 */
class CCDAset_EntityNamePartQualifier extends CCDA_Datatype_Set {

  /**
   * ADD a class
   *
   * @param String $data String
   *
   * @return void
   */
  function addData($data) {
    $ent = new CCDAEntityNamePartQualifier();
    $ent->setData($data);
    $this->listData[] = $ent;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDAEntityNamePartQualifier xml|data";
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
     * Test avec un EntityNamePartQualifier incorrecte
     */

    $this->addData("TESTTEST");
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un EntityNamePartQualifier correcte
     */

    $this->resetListData();
    $this->addData("LS");
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux EntityNamePartQualifier correcte
     */

    $this->addData("TITLE");
    $tabTest[] = $this->sample("Test avec deux EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
