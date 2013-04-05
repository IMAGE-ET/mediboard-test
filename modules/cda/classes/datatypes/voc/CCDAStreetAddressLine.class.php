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
 * specDomain: V14822 (C-0-D10642-V14822-cpt)
 */
class CCDAStreetAddressLine extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'SAL',
    'DIR',
  );
  public $_union = array (
    'BuildingNumber',
    'StreetName',
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