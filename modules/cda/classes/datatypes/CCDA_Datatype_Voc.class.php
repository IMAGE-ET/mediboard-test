<?php

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Description
 */
class CCDA_Datatype_Voc extends CCDA_Datatype {

  public $_enumeration     = array();
  public $_all_enumeration = array();
  public $_union = array();
  public $data;

  function __construct() {
    $this->_enumeration     = $this->getEnumeration();
    $this->_all_enumeration = $this->getEnumeration(true);
  }

  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    if (get_class($this) === "CCDA_cs") {
      $name = CMbArray::get(explode("_", $name), 1);
    }

    return $name;
  }

  function getEnumeration($all = false) {
    if (!$all) {
      return $this->_enumeration;
    }

    $enumerations = array();
    $enumerations = array_merge($this->_enumeration, $enumerations);
    foreach ($this->_union as $_union) {
      $_union = "CCDA".$_union;
      $_truc = new $_union;
      $enumerations = array_merge($enumerations, $_truc->getEnumeration());
    }

    return $enumerations;
  }

  function setData($data) {
    $this->data = $data;
  }

  function getData() {
    return $this->data;
  }

  function getProps() {
    $props = array();
    return $props;
  }

  function getUnion() {
    return $this->_union;
  }
  /**
   * Fonction qui teste si la classe est valide
   *
   * @return void|void
   */
  function test() {

    $tabTest = array();
    /**
     * Test avec une valeur null
     */
    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur erronée
     */

    $this->setData(" ");
    $tabTest[] = $this->sample("Test avec une valeur erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne
     */
    if ($this->_enumeration) {
      $enum = $this->_enumeration;
      $this->setData($enum[0]);

      $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    }



    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne d'un union
     */
    $union = $this->getUnion();
    if ($union) {
      $unionName = "CCDA".$union[0];
      $unionClass = new $unionName;
      $unionEnum = $unionClass->getEnumeration(true);
      if ($unionEnum) {
        $this->setData($unionEnum[0]);
        $tabTest[] = $this->sample("Test avec une valeur bonne d'un union", "Document valide");
      }
      /*-------------------------------------------------------------------------------------*/
    }


    return $tabTest;
  }

}
