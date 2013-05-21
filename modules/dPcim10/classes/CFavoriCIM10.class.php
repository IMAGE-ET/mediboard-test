<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

class CFavoriCIM10 extends CMbObject {
  public $favoris_id;
  public $favoris_code;
  public $favoris_user;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cim10favoris';
    $spec->key   = 'favoris_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["favoris_user"] = "ref notNull class|CUser";
    $props["favoris_code"] = "str notNull maxLength|16 seekable";
    return $props;
  }

  static function getTree($user_id) {
    return CFavoriCCAM::getTreeGeneric($user_id, "CFavoriCIM10");
  }
}
