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
 * vocSet: D201 (C-0-D201-cpt)
 */
class CCDATelecommunicationAddressUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'AS',
    'EC',
    'MC',
    'PG',
  );
  public $_union = array (
    'AddressUse',
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