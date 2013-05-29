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
 * abstDomain: V15914 (C-0-D15913-V15914-cpt)
 */
class CCDAOrganizationNameUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'C',
    'L',
  );
  public $_union = array (
    'EntityNameSearchUse',
    'NameRepresentationUse',
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