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
 * A concept descriptor represents any kind of concept usually
 * by giving a code defined in a code system.  A concept
 * descriptor can contain the original text or phrase that
 * served as the basis of the coding and one or more
 * translations into different coding systems. A concept
 * descriptor can also contain qualifiers to describe, e.g.,
 * the concept of a "left foot" as a postcoordinated term built
 * from the primary code "FOOT" and the qualifier "LEFT".
 * In exceptional cases, the concept descriptor need not
 * contain a code but only the original text describing
 * that concept.
 */
class CCDACD extends CCDAANY {

  /**
   * The text or phrase used as the basis for the coding.
   * @var CCDAED
   */
  public $originalText;

  /**
   * Specifies additional codes that increase the
   * specificity of the primary code.
   * @var CCDACR
   */
  var $qualifier = array();

  /**
   * A set of other concept descriptors that translate
   * this concept descriptor into other code systems.
   * @var CCDACD
   */
  var $translation = array();

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
   * Getter OriginalText
   *
   * @return \CCDAED
   */
  public function getOriginalText() {
    return $this->originalText;
  }

  /**
   * Getter Qualifier
   *
   * @return \CCDACR
   */
  public function getQualifier() {
    return $this->qualifier;
  }

    /**
   * Getter Translation
   *
   * @return \CCDACD
   */
  public function getTranslation() {
    return $this->translation;
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
   * Setter originalText
   *
   * @param \CCDAED $originalText CCDAED
   * v
   */
  public function setOriginalText($originalText) {
    $this->originalText = $originalText;
  }

  /**
   * Setter qualifier
   *
   * @param \CCDACR $qualifier CCDACR
   *
   * @return void
   */
  public function setQualifier($qualifier) {
    array_push($this->qualifier, $qualifier);
  }

  /**
   * Setter translation
   *
   * @param \CCDACD $translation CCDACD
   *
   * @return void
   */
  public function setTranslation($translation) {
    array_push($this->translation, $translation);
  }

  public function razListTranslation() {
    $this->translation = array();
  }

  public function razListQualifier() {
    $this->qualifier = array();
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["originalText"] = "CCDAED xml|element max|1";
    $props["qualifier"] = "CCDACR xml|element";
    $props["translation"] = "CCDACD xml|element";
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

    if (get_class($this) === "CCDAEIVL_event" || get_class($this) === "CCDACS") {
      return $tabTest;
    }
    /**
     * Test avec code incorrecte
     */
    $codeTest = new CCDA_cs();
    $codeTest->setData(" ");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec code correct
     */
    $codeTest->setData("TEST");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem incorrecte
     */

    $codeSystemTest = new CCDA_uid();
    $codeSystemTest->setData("*");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec un codeSystem incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem correct
     */

    $codeSystemTest->setData("HL7");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec un codeSystem correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName incorrecte
     */
    $codeSystemNameTest = new CCDA_st();
    $codeSystemNameTest->setData("");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec un codeSystemName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName correct
     */


    $codeSystemNameTest->setData("test");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec un codeSystemName correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion incorrecte
     */
    $codeSystemVersionTest = new CCDA_st();
    $codeSystemVersionTest->setData("");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec un codeSystemVersion incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion correct
     */

    $codeSystemVersionTest->setData("test");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec un codeSystemVersion correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName incorrecte
     */

    $displayNameTest = new CCDA_st();
    $displayNameTest->setData("");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec un displayName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName correct
     */

    $displayNameTest->setData("test");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec un displayName correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) !== "CCDACD") {
      return $tabTest;
    }

    /**
     * Test avec un translation correct sans valeur
     */
    $translation = new CCDACD();
    $this->setTranslation($translation);

    $tabTest[] = $this->sample("Test avec une translation correct sans valeur", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec deux translation correct sans valeur
     */
    $translation2 = new CCDACD();
    $this->setTranslation($translation2);

    $tabTest[] = $this->sample("Test avec deux translation correct sans valeur", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un qualifier incorrect
     */

    $cr = new CCDACR();
    $bn = new CCDA_bn();
    $bn->setData("TESTTEST");
    $cr->setInverted($bn);
    $this->setQualifier($cr);

    $tabTest[] = $this->sample("Test avec un qualifier incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un qualifier correct
     */

    $bn->setData("true");
    $cr->setInverted($bn);
    $this->setQualifier($cr);

    $tabTest[] = $this->sample("Test avec un qualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec deux qualifier correct
     */

    $cr2 = new CCDACR();
    $bn2 = new CCDA_bn();
    $bn2->setData("true");
    $cr2->setInverted($bn2);
    $this->setQualifier($cr2);

    $tabTest[] = $this->sample("Test avec deux qualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un originalText incorrect
     */

    $ed = new CCDAED();
    $language = new CCDA_cs();
    $language->setData(" ");
    $ed->setLanguage($language);
    $this->setOriginalText($ed);

    $tabTest[] = $this->sample("Test avec un originalText incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un originalText correct
     */

    $language->setData("TEST");
    $ed->setLanguage($language);
    $this->setOriginalText($ed);

    $tabTest[] = $this->sample("Test avec un originalText correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
