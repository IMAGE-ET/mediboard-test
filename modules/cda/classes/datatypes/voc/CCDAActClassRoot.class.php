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
 * specDomain: V13856 (C-0-D11527-V13856-cpt)
 */
class CCDAActClassRoot extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ACT',
    'ACCM',
    'ACCT',
    'ACSN',
    'ADJUD',
    'CONS',
    'CONTREG',
    'CTTEVENT',
    'DISPACT',
    'ENC',
    'INC',
    'INFRM',
    'INVE',
    'LIST',
    'MPROT',
    'PCPR',
    'PROC',
    'REG',
    'REV',
    'SBADM',
    'SPCTRT',
    'SUBST',
    'TRNS',
    'VERIF',
    'XACT',
  );
  public $_union = array (
    'ActClassContract',
    'ActClassControlAct',
    'ActClassObservation',
    'ActClassSupply',
    'ActContainer',
    'x_ActClassDocumentEntryAct',
    'x_ActClassDocumentEntryOrganizer',
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