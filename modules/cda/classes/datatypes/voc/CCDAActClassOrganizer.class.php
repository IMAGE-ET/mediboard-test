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
 * specDomain: V19443 (C-0-D11527-V13856-V19445-V19443-cpt)
 */
class CCDAActClassOrganizer extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ORGANIZER',
    'CATEGORY',
    'DOCBODY',
    'DOCSECT',
    'TOPIC',
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