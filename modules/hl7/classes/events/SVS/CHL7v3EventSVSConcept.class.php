<?php

/**
 * Retrieve concept
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CHL7v3EventSVSConcept
 * Retrieve concept
 */
class CHL7v3EventSVSConcept {
  public $displayName;
  public $codeSystem;
  public $code;

  function bind(CHL7v3MessageXML $dom, DOMElement $elt) {
    $this->displayName = $dom->getValueAttributNode($elt, "displayName");
    $this->codeSystem  = $dom->getValueAttributNode($elt, "codeSystem");
    $this->code        = $dom->getValueAttributNode($elt, "code");
  }
}