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
 * abstDomain: V10659 (C-0-D15888-V10659-cpt)
 */
class CCDAPersonNamePartQualifier extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'IN',
    'TITLE',
  );
  public $_union = array (
    'PersonNamePartAffixTypes',
    'PersonNamePartChangeQualifier',
    'PersonNamePartMiscQualifier',
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