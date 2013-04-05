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
 * abstDomain: V14900 (C-0-D10317-V10329-V14900-cpt)
 */
class CCDAActRelationshipAccounting extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'ActRelationshipCostTracking',
    'ActRelationshipPosting',
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