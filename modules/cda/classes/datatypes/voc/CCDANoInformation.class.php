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
 * specDomain: V10610 (C-0-D10609-V10610-cpt)
 */
class CCDANoInformation extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'NI',
    'MSK',
    'NA',
  );
  public $_union = array (
    'Other',
    'Unknown',
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