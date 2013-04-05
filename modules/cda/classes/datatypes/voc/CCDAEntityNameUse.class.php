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
 * vocSet: D15913 (C-0-D15913-cpt)
 */
class CCDAEntityNameUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'C',
  );
  public $_union = array (
    'EntityNameSearchUse',
    'NameRepresentationUse',
    'OrganizationNameUse',
    'PersonNameUse',
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