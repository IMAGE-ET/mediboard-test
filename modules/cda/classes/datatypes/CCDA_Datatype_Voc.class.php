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

  function __construct() {
    $this->_enumeration     = $this->getEnumeration();
    $this->_all_enumeration = $this->getEnumeration(true);
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

  function getUnion() {
    return $this->_union;
  }
  /**
   * Fonction qui teste si la classe est valide
   *
   * @return nothing|void
   */
  function test() {
    $name = $this->getName();
    $tabTest[$name] = array();
    /**
     * Test avec un valeur null
     */
    $tabTest[$name][] = $this->sample("Test avec une valeur null", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec un valeur erronée
     */

    $this->setData("TESTTEST");
    $tabTest[$name][] = $this->sample("Test avec une valeur erronée", "Document invalide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec un valeur bonne
     */
    $enum = $this->_enumeration;
    $this->setData($enum[0]);

    $tabTest[$name][] = $this->sample("Test avec une valeur bonne", "Document valide");


    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec un valeur bonne d'un union
     */
    $union = $this->getUnion();
    if ($union) {
      $unionName = "CCDA".$union[0];
      $unionClass = new $unionName;
      $unionEnum = $unionClass->getEnumeration(true);
      $this->setData($unionEnum[0]);
      $tabTest[$name][] = $this->sample("Test avec une valeur bonne d'un union", "Document valide");

      /*-------------------------------------------------------------------------------------*/
    }


    return $tabTest;
  }

}
