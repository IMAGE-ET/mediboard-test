<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Utilisation des ressources materielles au bloc
 * Class CUsageRessource
 */
class CUsageRessource extends CMbObject{
  public $usage_ressource_id;

  // DB References
  public $ressource_materielle_id;
  public $besoin_ressource_id;
  public $commentaire;

  /** @var CRessourceMaterielle */
  public $_ref_ressource;

  /** @var  CBesoinRessource */
  public $_ref_besoin;

  // Form Fields
  public $_debut_offset;
  public $_fin_offset;
  public $_width;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'usage_ressource';
    $spec->key   = 'usage_ressource_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["ressource_materielle_id"] = "ref class|CRessourceMaterielle notNull";
    $props["besoin_ressource_id"]     = "ref class|CBesoinRessource notNull";
    $props["commentaire"]             = "text helped";

    return $props;
  }

  /**
   * Chargement de la ressource materielle
   *
   * @return CRessourceMaterielle
   */
  function loadRefRessource() {
    return $this->_ref_ressource = $this->loadFwdRef("ressource_materielle_id", true);
  }

  /**
   * Chargement du besoin de la ressource
   *
   * @return CBesoinRessource
   */
  function loadRefBesoin() {
    return $this->_ref_besoin = $this->loadFwdRef("besoin_ressource_id", true);
  }
}
