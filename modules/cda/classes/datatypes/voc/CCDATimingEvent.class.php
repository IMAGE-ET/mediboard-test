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
 * vocSet: D10706 (C-0-D10706-cpt)
 */
class CCDATimingEvent extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'AC',
    'ACD',
    'ACM',
    'ACV',
    'HS',
    'IC',
    'ICD',
    'ICM',
    'ICV',
    'PC',
    'PCD',
    'PCM',
    'PCV',
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