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
 * {{$documentation}}
 */
class CCDA{{$name}} extends {{if $extend}}CCDA_{{$extend}}{{else}}CCDA_Datatype_Voc{{/if}} {

  public $_enumeration = {{$enumeration|smarty:nodefaults}};
  public $_union = {{$union|smarty:nodefaults}};


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