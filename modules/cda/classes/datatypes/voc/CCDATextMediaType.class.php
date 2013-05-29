<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * abstDomain: V14825 (C-0-D14824-V14825-cpt)
 */
class CCDATextMediaType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'text/html',
    'text/plain',
    'text/rtf',
    'text/sgml',
    'text/x-hl7-ft',
    'text/xml',
  );
  public $_union = array (
  );


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    parent::getProps();
    $props["data"] = "str xml|data enum|".implode("|", $this->getEnumeration(true));
    return $props;
  }
}