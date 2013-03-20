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
 * CCDAset_EntityNameUse Class
 */
class CCDAset_EntityNameUse extends CCDA_Datatype_Set {

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDAEntityNameUse xml|data";
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
     * Test avec un EntityNameUse incorrecte
     */
    $post = new CCDAEntityNameUse();
    $post->setData("TESTTEST");
    $this->addData($post);
    $tabTest[] = $this->sample("Test avec un EntityNameUse incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un EntityNameUse correcte
     */

    $post->setData("C");
    $this->razlistData();
    $this->addData($post);
    $tabTest[] = $this->sample("Test avec un EntityNameUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux EntityNameUse correcte
     */

    $post2 = new CCDAEntityNameUse();
    $post2->setData("I");
    $this->addData($post2);
    $tabTest[] = $this->sample("Test avec deux EntityNameUse correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
