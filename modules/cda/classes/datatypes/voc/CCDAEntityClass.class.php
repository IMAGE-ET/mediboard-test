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
 * vocSet: D10882 (C-0-D10882-cpt)
 */
class CCDAEntityClass extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'EntityClassRoot',
    'x_EntityClassDocumentReceiving',
    'x_EntityClassPersonOrOrgReceiving',
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