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
 * A ST that optionally may have a code attached.
 * The text must always be present if a code is present. The
 * code is often a local code.
 */
class CCDASC extends CCDAST {

  /**
   * The plain code symbol defined by the code system.
   * For example, "784.0" is the code symbol of the ICD-9
   * code "784.0" for headache.
   * @var CCDA_cs
   */
  public $code;

  /**
   * Specifies the code system that defines the code.
   * @var CCDA_uid
   */
  public $codeSystem;

  /**
   * A common name of the coding system.
   * @var CCDA_st
   */
  public $codeSystemName;

  /**
   * If applicable, a version descriptor defined
   * specifically for the given code system.
   * @var CCDA_st
   */
  public $codeSystemVersion;

  /**
   * A name or title for the code, under which the sending
   * system shows the code value to its users.
   * @var CCDA_st
   */
  public $displayName;

  /**
   * Getter code
   *
   * @return CCDA_cs CCDA_cs code
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Getter CodeSystem
   *
   * @return \CCDA_uid
   */
  public function getCodeSystem() {
    return $this->codeSystem;
  }


  /**
   * Getter CodeSystemName
   *
   * @return \CCDA_st
   */
  public function getCodeSystemName() {
    return $this->codeSystemName;
  }

  /**
   * Getter CodeSystemVersion
   *
   * @return \CCDA_st
   */
  public function getCodeSystemVersion() {
    return $this->codeSystemVersion;
  }

  /**
   * Getter DisplayName
   *
   * @return \CCDA_st
   */
  public function getDisplayName() {
    return $this->displayName;
  }

  /**
   * Setter Code
   *
   * @param \CCDA_cs $code CCDA_cs
   *
   * @return void
   */
  public function setCode($code) {
    $this->code = $code;
  }

  /**
   * Setter CodeSystem
   *
   * @param \CCDA_uid $codeSystem CCDA_uid
   *
   * @return void
   */
  public function setCodeSystem($codeSystem) {
    $this->codeSystem = $codeSystem;
  }

  /**
   * Setter codeSystemName
   *
   * @param \CCDA_st $codeSystemName CCDA_st
   *
   * @return void
   */
  public function setCodeSystemName($codeSystemName) {
    $this->codeSystemName = $codeSystemName;
  }

  /**
   * Setter codeSystemVersion
   *
   * @param \CCDA_st $codeSystemVersion CCDA_st
   *
   * @return void
   */
  public function setCodeSystemVersion($codeSystemVersion) {
    $this->codeSystemVersion = $codeSystemVersion;
  }

  /**
   * Setter displayName
   *
   * @param \CCDA_st $displayName CCDA_st
   *
   * @return void
   */
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["code"] = "CCDA_cs xml|attribute";
    $props["codeSystem"] = "CCDA_uid xml|attribute";
    $props["codeSystemName"] = "CCDA_st xml|attribute";
    $props["codeSystemVersion"] = "CCDA_st xml|attribute";
    $props["displayName"] = "CCDA_st xml|attribute";
    return $props;
  }


  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();
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
     * Test avec code correct
     */
    $codeTest->setData("TEST");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec code correct", "Document valide");

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
     * Test avec codeSystem correct
     */

    $codeSystemTest->setData("HL7");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec codeSystem correct", "Document valide");

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
     * Test avec codeSystemName correct
     */


    $codeSystemNameTest->setData("test");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec codeSystemName correct", "Document valide");

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
     * Test avec codeSystemVersion correct
     */

    $codeSystemVersionTest->setData("test");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec codeSystemVersion correct", "Document valide");

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
     * Test avec displayName correct
     */

    $displayNameTest->setData("test");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec displayName correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
