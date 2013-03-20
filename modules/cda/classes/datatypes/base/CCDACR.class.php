<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 */
 
/**
 * A concept qualifier code with optionally named role.
 * Both qualifier role and value codes must be defined by
 * the coding system.  For example, if SNOMED RT defines a
 * concept "leg", a role relation "has-laterality", and
 * another concept "left", the concept role relation allows
 * to add the qualifier "has-laterality: left" to a primary
 * code "leg" to construct the meaning "left leg".
 */
class CCDACR extends CCDAANY {

  /**
   * Specifies the manner in which the concept role value
   * contributes to the meaning of a code phrase.  For
   * example, if SNOMED RT defines a concept "leg", a role
   * relation "has-laterality", and another concept "left",
   * the concept role relation allows to add the qualifier
   * "has-laterality: left" to a primary code "leg" to
   * construct the meaning "left leg".  In this example
   * "has-laterality" is the CR.name.
   *
   * @var CCDACV
   */
  public $name;

  /**
   * The concept that modifies the primary code of a code
   * phrase through the role relation.  For example, if
   * SNOMED RT defines a concept "leg", a role relation
   * "has-laterality", and another concept "left", the
   * concept role relation allows adding the qualifier
   * "has-laterality: left" to a primary code "leg" to
   * construct the meaning "left leg".  In this example
   * "left" is the CR.value.
   *
   * @var CCDACD
   */
  public $value;

  /**
   * Indicates if the sense of the role name is inverted.
   * This can be used in cases where the underlying code
   * system defines inversion but does not provide reciprocal
   * pairs of role names. By default, inverted is false.
   *
   * @var CCDA_base_bn
   */
  public $inverted;

  /**
   * Setter inverted
   *
   * @param \CCDA_base_bn $inverted \CCDA_base_bn
   *
   * @return void
   */
  public function setInverted($inverted) {
    $this->inverted = $inverted;
  }

  /**
   * Getter Inverted
   *
   * @return \CCDA_base_bn
   */
  public function getInverted() {
    return $this->inverted;
  }

  /**
   * Setter Name
   *
   * @param \CCDACV $name \CCDACV
   *
   * @return void
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Getter Name
   *
   * @return \CCDACV
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Setter Value
   *
   * @param \CCDACD $value \CCDACD
   *
   * @return void
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * Getter Value
   *
   * @return \CCDACD
   */
  public function getValue() {
    return $this->value;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["name"] = "CCDACV xml|element max|1";
    $props["value"] = "CCDACD xml|element max|1";
    $props["inverted"] = "CCDA_base_bn xml|attribute default|false";
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
     * Test avec inverted incorrecte
     */
    $bn = new CCDA_base_bn();
    $bn->setData(" ");
    $this->setInverted($bn);
    $tabTest[] = $this->sample("Test avec un inverted incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec inverted correcte
     */
    $bn = new CCDA_base_bn();
    $bn->setData("false");
    $this->setInverted($bn);
    $tabTest[] = $this->sample("Test avec un inverted correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec name incorrecte
     */
    $cv = new CCDACV();
    $code = new CCDA_base_cs();
    $code->setData(" ");
    $cv->setCode($code);
    $this->setName($cv);
    $tabTest[] = $this->sample("Test avec un name incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec name correcte
     */
    $code->setData("test");
    $cv->setCode($code);
    $this->setName($cv);
    $tabTest[] = $this->sample("Test avec un name correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec value incorrecte
     */
    $valuetest = new CCDACD();
    $code = new CCDA_base_cs();
    $code->setData(" ");
    $valuetest->setCode($code);
    $this->setValue($valuetest);
    $tabTest[] = $this->sample("Test avec une value incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec value correcte
     */
    $valuetest = new CCDACD();
    $code = new CCDA_base_cs();
    $code->setData("test");
    $valuetest->setCode($code);
    $this->setValue($valuetest);
    $tabTest[] = $this->sample("Test avec une value correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
