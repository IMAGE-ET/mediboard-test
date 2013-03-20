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
 * A character string token representing a part of a name.
 * May have a type code signifying the role of the part in
 * the whole entity name, and a qualifier code for more detail
 * about the name part type. Typical name parts for person
 * names are given names, and family names, titles, etc.
 */
class CCDAENXP extends CCDAST {

  /**
   * Indicates whether the name part is a given name, family
   * name, prefix, suffix, etc.
   *
   * @var CCDAEntityNamePartType
   */
  public $partType;

  /**
   * The qualifier is a set of codes each of which specifies
   * a certain subcategory of the name part in addition to
   * the main name part type. For example, a given name may
   * be flagged as a nickname, a family name may be a
   * pseudonym or a name of public records.
   *
   * @var CCDAset_EntityNamePartQualifier
   */
  public $qualifier;

  /**
   * Setter partType
   *
   * @param \CCDAEntityNamePartType $partType \CCDAEntityNamePartType
   *
   * @return void
   */
  public function setPartType($partType) {
    $this->partType = $partType;
  }

  /**
   * Getter partType
   *
   * @return \CCDAEntityNamePartType
   */
  public function getPartType() {
    return $this->partType;
  }

  /**
   * Setter qualifier
   *
   * @param \CCDAset_EntityNamePartQualifier $qualifier \CCDAset_EntityNamePartQualifier
   *
   * @return void
   */
  public function setQualifier($qualifier) {
    $this->qualifier = $qualifier;
  }

  /**
   * Getter qualifier
   *
   * @return \CCDAset_EntityNamePartQualifier
   */
  public function getQualifier() {
    return $this->qualifier;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["partType"] = "CCDAEntityNamePartType xml|attribute";
    $props["qualifier"] = "CCDAset_EntityNamePartQualifier xml|attribute";
    return $props;
  }

  /**
   * fonction permettant de tester la validité de la classe
   *
   * @return array()
   */
  function test() {

    $tabTest = parent::test();

    if (get_class($this) !== "CCDAENXP") {
      return $tabTest;
    }
    /**
     * Test avec un parttype incorrecte
     */

    $part = new CCDAEntityNamePartType();
    $part->setData("TEstTEst");
    $this->setPartType($part);

    $tabTest[] = $this->sample("Test avec un partType incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un parttype correcte
     */

    $part->setData("FAM");
    $this->setPartType($part);

    $tabTest[] = $this->sample("Test avec un partType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un qualifier incorrecte
     */

    $setEntity = new CCDAset_EntityNamePartQualifier();
    $entity = new CCDAEntityNamePartQualifier();
    $entity->setData("TESTTEST");
    $setEntity->addData($entity);
    $this->setQualifier($setEntity);

    $tabTest[] = $this->sample("Test avec un qualifier incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un qualifier correcte
     */

    $entity->setData("LS");
    $setEntity->razlistData();
    $setEntity->addData($entity);
    $this->setQualifier($setEntity);

    $tabTest[] = $this->sample("Test avec un qualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
