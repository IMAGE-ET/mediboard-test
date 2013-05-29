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
 * vocSet: D10317 (C-0-D10317-cpt)
 */
class CCDAActRelationshipType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'ActRelationshipConditional',
    'ActRelationshipHasComponent',
    'ActRelationshipOutcome',
    'ActRelationshipPertains',
    'ActRelationshipSequel',
    'x_ActRelationshipDocument',
    'x_ActRelationshipEntry',
    'x_ActRelationshipEntryRelationship',
    'x_ActRelationshipExternalReference',
    'x_ActRelationshipPatientTransport',
    'x_ActRelationshipPertinentInfo',
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