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
 * A name for a person, organization, place or thing. A
 * sequence of name parts, such as given name or family
 * name, prefix, suffix, etc. Examples for entity name
 * values are "Jim Bob Walton, Jr.", "Health Level Seven,
 * Inc.", "Lake Tahoe", etc. An entity name may be as simple
 * as a character string or may consist of several entity name
 * parts, such as, "Jim", "Bob", "Walton", and "Jr.", "Health
 * Level Seven" and "Inc.", "Lake" and "Tahoe".
 */
class CCDAEN extends CCDAANY {

  var $delimiter = array();
  var $family = array();
  var $given = array();
  var $prefix = array();
  var $suffix = array();

  /**
   * An interval of time specifying the time during which
   * the name is or was used for the entity. This
   * accomodates the fact that people change names for
   * people, places and things.
   *
   * @var CCDAIVL_TS
   */
  public $validTime;

  /**
   * A set of codes advising a system or user which name
   * in a set of like names to select for a given purpose.
   * A name without specific use code might be a default
   * name useful for any purpose, but a name with a specific
   * use code would be preferred for that respective purpose.
   *
   * @var CCDAset_EntityNameUse
   */
  public $use;

  /**
   * Setter use
   *
   * @param \CCDAset_EntityNameUse $use \CCDAset_EntityNameUse
   *
   * @return void
   */
  public function setUse($use) {
    $this->use = $use;
  }

  /**
   * Getter use
   *
   * @return \CCDAset_EntityNameUse
   */
  public function getUse() {
    return $this->use;
  }

  /**
   * Setter validTime
   *
   * @param \CCDAIVL_TS $validTime \CCDAIVL_TS
   *
   * @return void
   */
  public function setValidTime($validTime) {
    $this->validTime = $validTime;
  }

  /**
   * Getter validTime
   *
   * @return \CCDAIVL_TS
   */
  public function getValidTime() {
    return $this->validTime;
  }

  /**
   * Ajoute l'instance dans le champ spécifié
   *
   * @param String $name  String
   * @param mixed  $value mixed
   *
   * @return void
   */
  function append($name, $value) {
    array_push($this->$name, $value);
  }

  /**
   * retourne le tableau d'instance du champ spécifié
   *
   * @param String $name String
   *
   * @return mixed
   */
  function get($name) {
    return $this->$name;
  }

  /**
   * Efface le tableau d'instance du champ spécifié
   *
   * @param String $name String
   *
   * @return void
   */
  function razListdata($name) {
    $this->$name = array();
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["delimiter"] = "CCDA_en_delimiter xml|element";
    $props["family"] = "CCDA_en_family xml|element";
    $props["given"] = "CCDA_en_given xml|element";
    $props["prefix"] = "CCDA_en_prefix xml|element";
    $props["suffix"] = "CCDA_en_suffix xml|element";
    $props["validTime"] = "CCDAIVL_TS xml|element max|1";
    $props["use"] = "CCDAset_EntityNameUse xml|attribute";
    $props["data"] = "str xml|data";
    return $props;
  }

  /**
  * fonction permettant de tester la validité de la classe
  *
  * @return array()
  */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec des données
     */

    $this->setData("test");
    $tabTest[] = $this->sample("Test avec des données", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un use incorrecte
     */

    $us = new CCDAset_EntityNameUse();
    $person = new CCDAEntityNameUse();
    $person->setData("TESTTEST");
    $us->addData($person);
    $this->setUse($us);
    $tabTest[] = $this->sample("Test avec un use incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un use correcte
     */

    $person->setData("C");
    $us->razlistData();
    $us->addData($person);
    $this->setUse($us);
    $tabTest[] = $this->sample("Test avec un use correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un validTime incorrecte
     */

    $valid = new CCDAIVL_TS();
    $ts = new CCDA_base_ts();
    $ts->setData("TESTTEST");
    $valid->setValue($ts);
    $this->setValidTime($valid);
    $tabTest[] = $this->sample("Test avec un validTime incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un validTime correcte
     */

    $ts->setData("75679245900741.869627871786625715081550660290154484483335306381809807748522068");
    $valid->setValue($ts);
    $this->setValidTime($valid);
    $tabTest[] = $this->sample("Test avec un validTime correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) === "CCDATN") {
      return $tabTest;
    }

    /**
     * test avec un delimiter correcte
     */

    $enxp = new CCDA_en_delimiter();
    $this->append("delimiter", $enxp);
    $tabTest[] = $this->sample("Test avec un delimiter correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deux delimiter correcte
     */

    $enxp = new CCDA_en_delimiter();
    $this->append("delimiter", $enxp);
    $tabTest[] = $this->sample("Test avec deux delimiter correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un prefix correcte
     */

    $enxp = new CCDA_en_prefix();
    $this->append("prefix", $enxp);
    $tabTest[] = $this->sample("Test avec un prefix correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deux prefix correcte
     */

    $enxp = new CCDA_en_prefix();
    $this->append("prefix", $enxp);
    $tabTest[] = $this->sample("Test avec deux prefix correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un suffix correcte
     */

    $enxp = new CCDA_en_suffix();
    $this->append("suffix", $enxp);
    $tabTest[] = $this->sample("Test avec un suffix correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deux prefix correcte
     */

    $enxp = new CCDA_en_suffix();
    $this->append("suffix", $enxp);
    $tabTest[] = $this->sample("Test avec deux suffix correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) === "CCDAON") {
      return $tabTest;
    }
    /**
     * test avec un family correcte
     */

    $enxp = new CCDA_en_family();
    $this->append("family", $enxp);
    $tabTest[] = $this->sample("Test avec un family correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deux family correcte
     */

    $enxp = new CCDA_en_family();
    $this->append("family", $enxp);
    $tabTest[] = $this->sample("Test avec deux family correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec un given correcte
     */

    $enxp = new CCDA_en_given();
    $this->append("given", $enxp);
    $tabTest[] = $this->sample("Test avec un given correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * test avec deux given correcte
     */

    $enxp = new CCDA_en_given();
    $this->append("given", $enxp);
    $tabTest[] = $this->sample("Test avec deux given correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
