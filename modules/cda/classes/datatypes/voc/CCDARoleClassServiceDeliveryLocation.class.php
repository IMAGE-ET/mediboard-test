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
 * specDomain: V16927 (C-0-D11555-V13940-V19313-V19105-V16927-cpt)
 */
class CCDARoleClassServiceDeliveryLocation extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'SDLOC',
    'DSDLOC',
    'ISDLOC',
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