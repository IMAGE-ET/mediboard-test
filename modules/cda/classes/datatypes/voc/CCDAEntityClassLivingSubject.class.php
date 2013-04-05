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
 * specDomain: V10884 (C-0-D10882-V13922-V10884-cpt)
 */
class CCDAEntityClassLivingSubject extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'LIV',
    'PSN',
  );
  public $_union = array (
    'EntityClassNonPersonLivingSubject',
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