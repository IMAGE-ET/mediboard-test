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
 * abstDomain: V10202 (C-0-D10196-V10202-cpt)
 */
class CCDAActMoodPredicate extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'EVN.CRT',
    'GOL',
    'OPT',
    'PERM',
    'PERMRQ',
  );
  public $_union = array (
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