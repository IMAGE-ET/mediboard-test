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
 *  Coded data, consists of a code, display name, code system,
 * and original text. Used when a single code value must be sent.
 */
class CCDACS extends CCDACV {
  
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    //$props["originalText"] = "CCDAED xml|element max|1 prohibited";
    //$props["qualifier"] = "CCDACR xml|element prohibited";
    $props["translation"] = "CCDACD xml|element prohibited";
    $props["codeSystem"] = "CCDA_uid xml|attribute prohibited";
    $props["codeSystemName"] = "CCDA_st xml|attribute prohibited";
    $props["codeSystemVersion"] = "CCDA_st xml|attribute prohibited";
    $props["displayName"] = "CCDA_st xml|attribute prohibited";
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
     * Test avec code erroné
     */
    $codeTest = new CCDA_cs();
    $codeTest->setData(" ");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec code erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec code bon
     */
    $codeTest->setData("TEST");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec code bon", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem incorrecte
     */

    $codeSystemTest = new CCDA_uid();
    $codeSystemTest->setData("*");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec codeSystem incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem bon
     */

    $codeSystemTest->setData("HL7");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec codeSystem bon", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName incorrecte
     */
    $codeSystemNameTest = new CCDA_st();
    $codeSystemNameTest->setData("");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec codeSystemName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName bon
     */


    $codeSystemNameTest->setData("test");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec codeSystemName bon", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion incorrecte
     */
    $codeSystemVersionTest = new CCDA_st();
    $codeSystemVersionTest->setData("");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec codeSystemVersion incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion bon
     */

    $codeSystemVersionTest->setData("test");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec codeSystemVersion bon", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName incorrecte
     */

    $displayNameTest = new CCDA_st();
    $displayNameTest->setData("");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec displayName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName bon
     */

    $displayNameTest->setData("test");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec displayName bon", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un translation bon sans valeur
     */
    $translation = new CCDACD();
    $this->setTranslation($translation);

    $tabTest[] = $this->sample("Test avec translation bon sans valeur", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un translation bon et une bonne valeur avec qualifier
     */
    /*
    $translation->setDataTranslation("test");
    $this->setTranslation($translation);

    $tabTest[] = $this->sample("Test avec translation bon avec qualifier", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    return $tabTest;
  }
}
