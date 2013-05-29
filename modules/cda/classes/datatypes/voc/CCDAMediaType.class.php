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
 * vocSet: D14824 (C-0-D14824-cpt)
 */
class CCDAMediaType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'ApplicationMediaType',
    'AudioMediaType',
    'ImageMediaType',
    'ModelMediaType',
    'MultipartMediaType',
    'TextMediaType',
    'VideoMediaType',
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