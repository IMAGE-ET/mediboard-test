<?php

/**
 * Retrieve concept list
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CHL7v3EventSVSConceptList
 * Retrieve concept list
 */
class CHL7v3EventSVSConceptList {
  public $lang;

    /** @var array */
  public $concept = array();

  /**
   * Bind value set
   *
   * @param CHL7v3MessageXML $dom    Document
   * @param DOMElement       $elt    Element
   * @param string           $prefix Prefix
   *
   * @return void
   */
  function bind(CHL7v3MessageXML $dom, DOMElement $elt, $prefix) {
    $this->lang = $dom->getValueAttributNode($elt, "xml:lang");

    foreach ($dom->queryNodes($prefix."Concept", $elt) as $_concept) {
      $concept = new CHL7v3EventSVSConcept($dom);
      $concept->bind($dom, $_concept);

      $this->concept[] = $concept;
    }
  }
}