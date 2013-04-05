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
 * specDomain: V11529 (C-0-D11527-V13856-V11529-cpt)
 */
class CCDAActClassObservation extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'OBS',
    'ALRT',
    'CLNTRL',
    'CNOD',
    'DGIMG',
    'INVSTG',
    'SPCOBS',
  );
  public $_union = array (
    'ActClassCondition',
    'ActClassObservationSeries',
    'ActClassROI',
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