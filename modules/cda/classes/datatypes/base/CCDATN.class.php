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
 * A restriction of entity name that is effectively a simple string used
 * for a simple name for things and places.
 */
class CCDATN extends CCDAEN {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["delimiter"] = "CCDA_en_delimiter xml|element prohibited";
    $props["family"] = "CCDA_en_family xml|element prohibited";
    $props["given"] = "CCDA_en_given xml|element prohibited";
    $props["prefix"] = "CCDA_en_prefix xml|element prohibited";
    $props["suffix"] = "CCDA_en_suffix xml|element prohibited";
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

    /**
     * test avec un prefix correcte
     */

    $this->razListdata("given");
    $enxp = new CCDA_en_prefix();
    $this->append("prefix", $enxp);
    $tabTest[] = $this->sample("Test avec un prefix correcte, interdit dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un sufix correcte
     */

    $this->razListdata("prefix");
    $enxp = new CCDA_en_suffix();
    $this->append("suffix", $enxp);
    $tabTest[] = $this->sample("Test avec un sufix correcte, interdit dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un delimiter correcte
     */

    $this->razListdata("sufix");
    $enxp = new CCDA_en_delimiter();
    $this->append("delimiter", $enxp);
    $tabTest[] = $this->sample("Test avec un delimiter correcte, interdit dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
