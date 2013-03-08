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
 * An identifier that uniquely identifies a thing or object.
 * Examples are object identifier for HL7 RIM objects,
 * medical record number, order id, service catalog item id,
 * Vehicle Identification Number (VIN), etc. Instance
 * identifiers are defined based on ISO object identifiers.
 */
class CCDAII extends CCDAANY {

  /**
   * A unique identifier that guarantees the global uniqueness
   * of the instance identifier. The root alone may be the
   * entire instance identifier.
   *
   * @var CCDA_uid
   */
  public $root;

  /**
   * A character string as a unique identifier within the
   * scope of the identifier root.
   *
   * @var CCDA_st
   */
  public $extension;

  /**
   * A human readable name or mnemonic for the assigning
   * authority. This name may be provided solely for the
   * convenience of unaided humans interpreting an II value
   * and can have no computational meaning. Note: no
   * automated processing must depend on the assigning
   * authority name to be present in any form.
   *
   * @var CCDA_st
   */
  public $assigningAuthorityName;

  /**
   * Specifies if the identifier is intended for human
   * display and data entry (displayable = true) as
   * opposed to pure machine interoperation (displayable
   * = false).
   *
   * @var CCDA_bl
   */
  public $displayable;

  /**
   * @param \CCDA_st $assigningAuthorityName
   */
  public function setAssigningAuthorityName($assigningAuthorityName) {
    $this->assigningAuthorityName = $assigningAuthorityName;
  }

  /**
   * @return \CCDA_st
   */
  public function getAssigningAuthorityName() {
    return $this->assigningAuthorityName;
  }

  /**
   * @param \CCDA_bl $displayable
   */
  public function setDisplayable($displayable) {
    $this->displayable = $displayable;
  }

  /**
   * @return \CCDA_bl
   */
  public function getDisplayable() {
    return $this->displayable;
  }

  /**
   * @param \CCDA_st $extension
   */
  public function setExtension($extension) {
    $this->extension = $extension;
  }

  /**
   * @return \CCDA_st
   */
  public function getExtension() {
    return $this->extension;
  }

  /**
   * @param \CCDA_uid $root
   */
  public function setRoot($root) {
    $this->root = $root;
  }

  /**
   * @return \CCDA_uid
   */
  public function getRoot() {
    return $this->root;
  }


  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["root"] = "CCDA_uid xml|attribute";
    $props["extension"] = "CCDA_st xml|attribute";
    $props["assigningAuthorityName"] = "CCDA_st xml|attribute";
    $props["displayable"] = "CCDA_bl xml|attribute";
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
     * Test avec un uid incorrect
     */
    $uid = new CCDA_uid();
    $uid->setData("4TESTTEST");
    $this->setRoot($uid);
    $tabTest[] = $this->sample("Test avec un root incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un uid correct
     */
    $uid->setData("HL7");
    $this->setRoot($uid);
    $tabTest[] = $this->sample("Test avec un root correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un extension incorrect
     */
    $st = new CCDA_st();
    $st->setData("");
    $this->setExtension($st);
    $tabTest[] = $this->sample("Test avec un extension incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un extension correct
     */
    $st->setData("HL7");
    $this->setExtension($st);
    $tabTest[] = $this->sample("Test avec un extension correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un assigningAuthorityName incorrect
     */
    $st = new CCDA_st();
    $st->setData("");
    $this->setAssigningAuthorityName($st);
    $tabTest[] = $this->sample("Test avec un assigningAuthorityName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un assigningAuthorityName correct
     */
    $st->setData("HL7");
    $this->setAssigningAuthorityName($st);
    $tabTest[] = $this->sample("Test avec un assigningAuthorityName correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un displayable incorrect
     */
    $bl = new CCDA_bl();
    $bl->setData("TESTTEST");
    $this->setDisplayable($bl);
    $tabTest[] = $this->sample("Test avec un displayable incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un displayable correct
     */
    $bl->setData("true");
    $this->setDisplayable($bl);
    $tabTest[] = $this->sample("Test avec un displayable correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
