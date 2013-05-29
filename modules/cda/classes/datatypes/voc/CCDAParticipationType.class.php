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
 * vocSet: D10901 (C-0-D10901-cpt)
 */
class CCDAParticipationType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CST',
    'RESP',
  );
  public $_union = array (
    'ParticipationAncillary',
    'ParticipationIndirectTarget',
    'ParticipationInformationGenerator',
    'ParticipationInformationRecipient',
    'ParticipationPhysicalPerformer',
    'ParticipationTargetDirect',
    'ParticipationTargetLocation',
    'ParticipationVerifier',
    'x_EncounterParticipant',
    'x_EncounterPerformerParticipation',
    'x_InformationRecipient',
    'x_ParticipationAuthorPerformer',
    'x_ParticipationEntVrf',
    'x_ParticipationPrfEntVrf',
    'x_ParticipationVrfRespSprfWit',
    'x_ServiceEventPerformer',
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