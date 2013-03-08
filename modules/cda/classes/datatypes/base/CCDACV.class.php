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
 * Coded data, consists of a code, display name, code system,
 * and original text. Used when a single code value must be sent.
 */
class CCDACV extends CCDACE {

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["translation"] = "CCDACD xml|element prohibited";
    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    if(get_class($this) === "CCDAPQR") {
      return $tabTest;
    }
    /**
     * Test avec un translation correct sans valeur
     */
    $translation = new CCDACD();
    $this->setTranslation($translation);

    $tabTest[] = $this->sample("Test avec translation correct sans valeur", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un translation correct avec valeur
     */
    $translation = new CCDACD();
    $codeSystemNameTest = new CCDA_st();
    $codeSystemNameTest->setData("test");
    $translation->setCodeSystemName($codeSystemNameTest);
    $this->setTranslation($translation);

    $tabTest[] = $this->sample("Test avec translation correct avec valeur", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
