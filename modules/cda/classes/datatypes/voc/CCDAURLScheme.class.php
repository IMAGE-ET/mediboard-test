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
 * vocSet: D14866 (C-0-D14866-cpt)
 */
class CCDAURLScheme extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'fax',
    'file',
    'ftp',
    'http',
    'mailto',
    'mllp',
    'modem',
    'nfs',
    'tel',
    'telnet',
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