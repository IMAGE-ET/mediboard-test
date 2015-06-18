<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe CAppelSejour
 *
 * Appels de séjour
 */
class CAppelSejour extends CMbObject {
  // DB Table key
  public $appel_id;

  // DB Table key
  public $sejour_id;
  public $user_id;
  public $datetime;
  public $type;
  public $etat;
  public $commentaire;

  //Distant field
  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_appel';
    $spec->key   = 'appel_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]   = "ref notNull class|CSejour";
    $props["user_id"]     = "ref notNull class|CMediusers";
    $props["datetime"]    = "dateTime notNull";
    $props["type"]        = "enum notNull list|admission|sortie default|admission";
    $props["etat"]        = "enum notNull list|realise|echec default|realise";
    $props["commentaire"] = "text";
    return $props;
  }

  /**
   * Load the user
   *
   * @return CMediusers The user object
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id");
  }
}
