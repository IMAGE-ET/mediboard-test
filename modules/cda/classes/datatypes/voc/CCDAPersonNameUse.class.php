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
 * abstDomain: V200 (C-0-D15913-V200-cpt)
 */
class CCDAPersonNameUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'A',
    'ASGN',
    'C',
    'I',
    'L',
    'R',
  );
  public $_union = array (
    'EntityNameSearchUse',
    'NamePseudonymUse',
    'NameRepresentationUse',
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