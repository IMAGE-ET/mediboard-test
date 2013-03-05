<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
