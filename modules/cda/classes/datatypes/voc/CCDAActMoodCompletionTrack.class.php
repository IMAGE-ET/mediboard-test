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
 * abstDomain: V10197 (C-0-D10196-V10197-cpt)
 */
class CCDAActMoodCompletionTrack extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'DEF',
    'EVN',
  );
  public $_union = array (
    'ActMoodIntent',
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