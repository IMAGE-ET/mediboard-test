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
 * abstDomain: V19462 (C-0-D10882-V19462-cpt)
 */
class CCDAx_EntityClassDocumentReceiving extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'HCE',
    'PSN',
  );
  public $_union = array (
    'EntityClassOrganization',
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