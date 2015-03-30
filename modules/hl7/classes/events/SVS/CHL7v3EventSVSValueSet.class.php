<?php

/**
 * Retrieve value set
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CHL7v3EventSVSValueSet
 * Retrieve value set
 */
class CHL7v3EventSVSValueSet {
  public $id;
  public $version;
  public $displayName;

  /** @var array */
  public $concept_list = array();

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
    $this->id          = $dom->getValueAttributNode($elt, "id");
    $this->version     = $dom->getValueAttributNode($elt, "version");
    $this->displayName = $dom->getValueAttributNode($elt, "displayName");

    foreach ($dom->queryNodes($prefix."ConceptList", $elt) as $_concept_list) {
      $concept_list = new CHL7v3EventSVSConceptList($dom);
      $concept_list->bind($dom, $_concept_list, $prefix);

      $this->concept_list[] = $concept_list;
    }
  }
}