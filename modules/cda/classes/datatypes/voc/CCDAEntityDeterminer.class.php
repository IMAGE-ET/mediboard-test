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
 * vocSet: D10878 (C-0-D10878-cpt)
 */
class CCDAEntityDeterminer extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'INSTANCE',
  );
  public $_union = array (
    'EntityDeterminerDetermined',
    'x_DeterminerInstanceKind',
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