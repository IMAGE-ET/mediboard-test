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
 * specDomain: V17887 (C-0-D10642-V17887-cpt)
 */
class CCDADeliveryAddressLine extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'DAL',
    'DINST',
    'DINSTA',
    'DINSTQ',
    'DMOD',
    'DMODID',
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