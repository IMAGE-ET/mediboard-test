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
 * vocSet: D15888 (C-0-D15888-cpt)
 */
class CCDAEntityNamePartQualifier extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'OrganizationNamePartQualifier',
    'PersonNamePartQualifier',
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