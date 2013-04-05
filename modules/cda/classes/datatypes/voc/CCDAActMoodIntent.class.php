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
 * specDomain: V10199 (C-0-D10196-V16742-V10199-cpt)
 */
class CCDAActMoodIntent extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'INT',
    'APT',
    'ARQ',
    'PRMS',
    'PRP',
    'RQO',
    'SLOT',
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