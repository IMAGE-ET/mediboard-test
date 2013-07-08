<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe Classification représentant la variable EventCodeList
 */
class CXDSEventCodeList extends CXDSClass {

  /**
   * @see parent::__construct()
   */
  function __construct($id, $classifiedObject, $nodeRepresentation) {
    parent::__construct($id, $classifiedObject, $nodeRepresentation);
    $this->classificationScheme = "urn:uuid:2c6b8cb7-8b2a-4051-b291-b1ae6a575ef4";
  }
}