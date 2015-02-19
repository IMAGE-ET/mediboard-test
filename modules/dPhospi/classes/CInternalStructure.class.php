<?php

/**
 * $Id$
 *
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CInternalStructure extends CEntity {

  // DB Fields
  public $typologie;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["typologie"] = "str";

    return $props;
  }

}
