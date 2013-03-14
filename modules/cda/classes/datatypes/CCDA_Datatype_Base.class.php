<?php

/**
 * $Id$
 *  
 * @category ${Module}
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
   * Fonction permettant de tester la classe
   *
   * @return void
   */
  function test() {
    $tabTest = array();

    if (get_class($this) === "CCDA_bin" || get_class($this) === "CCDA_url") {
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
