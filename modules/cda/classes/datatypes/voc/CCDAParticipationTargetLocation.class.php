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
 * specDomain: V10302 (C-0-D10901-V10302-cpt)
 */
class CCDAParticipationTargetLocation extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'LOC',
    'DST',
    'ELOC',
    'ORG',
    'RML',
    'VIA',
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