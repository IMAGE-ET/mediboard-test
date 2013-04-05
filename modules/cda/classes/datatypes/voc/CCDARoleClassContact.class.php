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
 * specDomain: V12205 (C-0-D11555-V13940-V19313-V19316-V10416-V14006-V11595-V12205-cpt)
 */
class CCDARoleClassContact extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CON',
    'ECON',
    'NOK',
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