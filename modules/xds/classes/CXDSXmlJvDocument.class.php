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
 * Classe xml pour les jeux de valeurs
 */
class CXDSXmlJvDocument extends CMbXMLDocument {

  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct("UTF-8");
    $this->formatOutput = true;
    $this->addElement($this, "jeuxValeurs");
  }

  /**
   * Ajoute une ligne dans le xml
   *
   * @param String $oid  OID
   * @param String $id   Identifiant
   * @param String $name Nom
   *
   * @return void
   */
  function appendLine($oid, $id, $name) {
    $element = $this->addElement($this->documentElement, "line");
    $this->addAttribute($element, "id"  , $id);
    $this->addAttribute($element, "oid" , $oid);
    $this->addAttribute($element, "name", $name);
  }
}