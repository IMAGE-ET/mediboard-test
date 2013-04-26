<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe regroupant les fonctions de type ActRelationship
 */
class CCDAActRelationshipCDA extends CCDADocumentCDA {

  /**
   * Création de l'actrelationship Component
   *
   * @return CCDAPOCD_MT000040_Component2
   */
  function setComponent2() {
    $component2 = new CCDAPOCD_MT000040_Component2();

    $component2->setNonXMLBody(parent::$act->setNonXMLBody());
    return $component2;
  }

  /**
   * Création documentOf
   *
   * @return CCDAPOCD_MT000040_DocumentationOf
   */
  function setDocumentOf() {
    $documentOf = new CCDAPOCD_MT000040_DocumentationOf();
    $documentOf->setServiceEvent(parent::$act->setServiceEvent());
    return $documentOf;
  }

  /**
   * Création componentOf
   *
   * @return CCDAPOCD_MT000040_Component1
   */
  function setComponentOf() {
    $componentOf = new CCDAPOCD_MT000040_Component1();
    $componentOf->setEncompassingEncounter(parent::$act->setEncompassingEncounter());
    return $componentOf;
  }
}