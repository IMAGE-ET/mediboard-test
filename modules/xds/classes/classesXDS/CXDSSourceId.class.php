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
 * Classe ExternalIdentifier représentant la variable SourceId
 */
class CXDSSourceId extends CXDSExternalIdentifier {

  /**
   * @see parent::__construct()
   */
  function __construct($id, $registryObject, $value) {
    parent::__construct($id, $registryObject, $value);
    $this->identificationScheme = "urn:uuid:554ac39e-e3fe-47fe-b233-965d2a147832";
    $this->name = new CXDSName("XDSSubmissionSet.sourceId");
  }
}