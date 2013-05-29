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
 * specDomain: V10430 (C-0-D11555-V13940-V10429-V10430-cpt)
 */
class CCDARoleClassIngredientEntity extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'INGR',
    'ACTI',
    'ACTM',
    'ADTV',
    'BASE',
  );
  public $_union = array (
    'RoleClassInactiveIngredient',
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