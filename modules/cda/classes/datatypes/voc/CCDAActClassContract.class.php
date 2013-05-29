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
 * specDomain: V14002 (C-0-D11527-V13856-V14002-cpt)
 */
class CCDAActClassContract extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'CNTRCT',
  );
  public $_union = array (
    'ActClassFinancialContract',
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