<?php

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class of specialty Asip
 */
class CSpecialtyAsip extends CMbObject {

  public $libelle;
  public $oid;
  public $code;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn = 'ASIP';
    $spec->table = 'authorspecialty_20121112';
    $spec->key   = 'table_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();

    $props["code"]    = "str";
    $props["oid"]     = "str";
    $props["libelle"] = "str";

    return $props;
  }
}