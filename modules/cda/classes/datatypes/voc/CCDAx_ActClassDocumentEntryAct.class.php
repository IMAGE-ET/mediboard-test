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
 * abstDomain: V19604 (C-0-D11527-V13856-V19604-cpt)
 */
class CCDAx_ActClassDocumentEntryAct extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ACT',
    'ACCM',
    'CONS',
    'CTTEVENT',
    'INC',
    'INFRM',
    'PCPR',
    'REG',
    'SPCTRT',
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