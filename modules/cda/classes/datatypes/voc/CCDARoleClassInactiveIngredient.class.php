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
 * specDomain: V19089 (C-0-D11555-V13940-V10429-V10430-V19089-cpt)
 */
class CCDARoleClassInactiveIngredient extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'IACT',
    'COLR',
    'FLVR',
    'PRSV',
    'STBL',
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