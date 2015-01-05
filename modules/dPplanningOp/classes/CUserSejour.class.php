<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Affectation de mediuser pour un séjour
 */
class CUserSejour extends CMbObject {

  // DB Table key
  public $sejour_affectation_id;

  // DB Fields
  public $sejour_id;
  public $user_id;

  // Object References
  /** @var  CSejour $_ref_sejour*/
  public $_ref_sejour;
  /** @var  CMediusers $_ref_user*/
  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour_affectation';
    $spec->key   = 'sejour_affectation_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"] = "ref notNull class|CSejour";
    $props["user_id"]   = "ref notNull class|CMediusers";
    return $props;
  }

  /**
   * Chargement de l'intervention
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Chargement du libellé
   *
   * @return CMediusers
   */
  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    return $this->_ref_user;
  }
} 