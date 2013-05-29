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
 * abstDomain: V18977 (C-0-D10317-V18977-cpt)
 */
class CCDAActRelationshipConditional extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CIND',
    'PRCN',
    'TRIG',
  );
  public $_union = array (
    'ActRelationshipReason',
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