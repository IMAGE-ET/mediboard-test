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
 * abstDomain: V19445 (C-0-D11527-V13856-V19445-cpt)
 */
class CCDAActContainer extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'FOLDER',
  );
  public $_union = array (
    'ActClassComposition',
    'ActClassEntry',
    'ActClassExtract',
    'ActClassOrganizer',
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