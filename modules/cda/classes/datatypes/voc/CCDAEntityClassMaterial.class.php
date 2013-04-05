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
 * specDomain: V10883 (C-0-D10882-V13922-V10883-cpt)
 */
class CCDAEntityClassMaterial extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'MAT',
    'CHEM',
    'FOOD',
  );
  public $_union = array (
    'EntityClassManufacturedMaterial',
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