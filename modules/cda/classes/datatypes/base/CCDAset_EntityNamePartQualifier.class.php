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
    $entity = new CCDAEntityNamePartQualifier();
    $entity->setData("TESTTEST");
    $this->addData($entity);
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un EntityNamePartQualifier correcte
     */
    $entity->setData("LS");
    $this->razlistData();
    $this->addData($entity);
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux EntityNamePartQualifier correcte
     */

    $entity2 = new CCDAEntityNamePartQualifier();
    $entity2->setData("TITLE");
    $this->addData($entity2);
    $tabTest[] = $this->sample("Test avec deux EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
