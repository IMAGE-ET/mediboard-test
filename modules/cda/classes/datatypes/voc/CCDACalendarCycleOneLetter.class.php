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
 * abstDomain: V10701 (C-0-D10684-V10701-cpt)
 */
class CCDACalendarCycleOneLetter extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'D',
    'H',
    'J',
    'M',
    'N',
    'S',
    'W',
    'Y',
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