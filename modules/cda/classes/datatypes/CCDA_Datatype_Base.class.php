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
 * Classe dont hériteront les classes de base (real, int...)
 */
class CCDA_Datatype_Base extends CCDA_Datatype {
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    
    return $props;
  }

  /**
   * Retourne le nom du type utilisé dans le XSD
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);

    $name = substr($name, strrpos($name, "_")+1);

    return $name;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array()
   */
  function test() {
    $tabTest = array();

    if (get_class($this) === "CCDA_base_bin" || get_class($this) === "CCDA_base_url") {
      return $tabTest;
    }
    /**
     * Test avec une valeur null
     */

    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
