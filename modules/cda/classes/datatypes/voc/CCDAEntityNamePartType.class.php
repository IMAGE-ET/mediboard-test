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
 * vocSet: D15880 (C-0-D15880-cpt)
 */
class CCDAEntityNamePartType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'DEL',
    'FAM',
    'GIV',
    'PFX',
    'SFX',
  );
  public $_union = array (
    'x_OrganizationNamePartType',
    'x_PersonNamePartType',
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