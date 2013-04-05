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
 * abstDomain: V19105 (C-0-D11555-V13940-V19313-V19105-cpt)
 */
class CCDARoleClassPassive extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ACCESS',
    'BIRTHPL',
    'EXPR',
    'HLD',
    'HLTHCHRT',
    'IDENT',
    'MNT',
    'OWN',
    'RGPR',
    'TERR',
    'WRTE',
  );
  public $_union = array (
    'RoleClassDistributedMaterial',
    'RoleClassManufacturedProduct',
    'RoleClassServiceDeliveryLocation',
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