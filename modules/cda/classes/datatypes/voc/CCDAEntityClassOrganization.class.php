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
 * specDomain: V10889 (C-0-D10882-V19463-V10889-cpt)
 */
class CCDAEntityClassOrganization extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ORG',
    'PUB',
  );
  public $_union = array (
    'State',
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