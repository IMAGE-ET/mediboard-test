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
 * A name for an organization. A sequence of name parts.
 */
class CCDAON extends CCDAEN {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["family"] = "CCDA_en_family xml|element prohibited";
    $props["given"] = "CCDA_en_given xml|element prohibited";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return void
   */
  function test() {
    $tabTest = parent::test();

    /**
     * test avec un family correcte
     */

    $enxp = new CCDA_en_family();
    $this->append("family", $enxp);
    $tabTest[] = $this->sample("Test avec un family correcte, interdit dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un given correcte
     */

    $this->razListdata("family");
    $enxp = new CCDA_en_given();
    $this->append("given", $enxp);
    $tabTest[] = $this->sample("Test avec un given correcte, interdit dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
