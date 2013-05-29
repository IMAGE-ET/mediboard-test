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
 * vocSet: D10684 (C-0-D10684-cpt)
 */
class CCDACalendarCycle extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'CalendarCycleOneLetter',
    'CalendarCycleTwoLetter',
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