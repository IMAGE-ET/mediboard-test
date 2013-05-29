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
 * vocSet: D10642 (C-0-D10642-cpt)
 */
class CCDAAddressPartType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CAR',
    'CEN',
    'CNT',
    'CPA',
    'CTY',
    'DEL',
    'POB',
    'PRE',
    'STA',
    'ZIP',
  );
  public $_union = array (
    'AdditionalLocator',
    'DeliveryAddressLine',
    'StreetAddressLine',
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