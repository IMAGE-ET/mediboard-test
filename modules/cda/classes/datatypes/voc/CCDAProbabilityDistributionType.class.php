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
 * vocSet: D10747 (C-0-D10747-cpt)
 */
class CCDAProbabilityDistributionType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'B',
    'E',
    'F',
    'G',
    'LN',
    'N',
    'T',
    'U',
    'X2',
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