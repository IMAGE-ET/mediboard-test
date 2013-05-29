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
 * specDomain: V19580 (C-0-D11527-V13856-V11529-V19580-cpt)
 */
class CCDAActClassCondition extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'COND',
  );
  public $_union = array (
    'ActClassPublicHealthCase',
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