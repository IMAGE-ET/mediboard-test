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
 * vocSet: D10637 (C-0-D10637-cpt)
 */
class CCDAPostalAddressUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'PHYS',
    'PST',
  );
  public $_union = array (
    'AddressUse',
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