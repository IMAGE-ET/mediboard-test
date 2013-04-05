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
 * specDomain: V10329 (C-0-D10317-V10329-cpt)
 */
class CCDAActRelationshipPertains extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'PERT',
    'AUTH',
    'CAUS',
    'COVBY',
    'DRIV',
    'EXPL',
    'ITEMSLOC',
    'LIMIT',
    'MFST',
    'NAME',
    'PREV',
    'REFR',
    'REFV',
    'SUBJ',
    'SUMM',
  );
  public $_union = array (
    'ActRelationshipAccounting',
    'TemporallyPertains',
    'hasSupport',
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