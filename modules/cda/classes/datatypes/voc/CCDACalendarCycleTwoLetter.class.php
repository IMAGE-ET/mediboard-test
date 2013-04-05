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
 * abstDomain: V10685 (C-0-D10684-V10685-cpt)
 */
class CCDACalendarCycleTwoLetter extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CD',
    'CH',
    'CM',
    'CN',
    'CS',
    'CW',
    'CY',
    'DM',
    'DW',
    'DY',
    'HD',
    'MY',
    'NH',
    'SN',
    'WY',
  );
  public $_union = array (
    'GregorianCalendarCycle',
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