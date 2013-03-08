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
   * @param \CCDAset_EntityNameUse $use
   */
  public function setUse($use) {
    $this->use = $use;
  }

  /**
   * @return \CCDAset_EntityNameUse
   */
  public function getUse() {
    return $this->use;
  }

  /**
   * @param \CCDAIVL_TS $validTime
   */
  public function setValidTime($validTime) {
    $this->validTime = $validTime;
  }

  /**
   * @return \CCDAIVL_TS
   */
  public function getValidTime() {
    return $this->validTime;
  }

  function append($name, $value) {
    array_push($this->$name, $value);
  }

  function get($name) {
    return $this->$name;
  }

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
    return $props;
  }

  /**
  * fonction permettant de tester la validité de la classe
  *
  * @return void
  */
  function test() {
    $tabTest = array();

    /**
     * test avec les valeurs null
     */

    $tabTest[] = $this->sample("Test avec des valeurs null", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
