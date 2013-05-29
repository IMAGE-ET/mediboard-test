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
 * specDomain: V13940 (C-0-D11555-V13940-cpt)
 */
class CCDARoleClassRoot extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ROL',
  );
  public $_union = array (
    'RoleClassAssociative',
    'RoleClassOntological',
    'RoleClassPartitive',
    'x_DocumentEntrySubject',
    'x_DocumentSubject',
    'x_InformationRecipientRole',
    'x_RoleClassAccommodationRequestor',
    'x_RoleClassCoverage',
    'x_RoleClassCoverageInvoice',
    'x_RoleClassCredentialedEntity',
    'x_RoleClassPayeePolicyRelationship',
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