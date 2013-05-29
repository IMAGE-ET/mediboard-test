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
 * Besoin en ressource materielle
 * Class CBesoinRessource
 */
class CBesoinRessource extends CMbObject {
  public $besoin_ressource_id;

  // DB References
  public $type_ressource_id;
  public $protocole_id;
  public $operation_id;
  public $commentaire;

  /** @var CTypeRessource */
  public $_ref_type_ressource;

  /** @var COperation */
  public $_ref_operation;

  /** @var CProtocole */
  public $_ref_protocole;

  /** @var CUsageRessource */
  public $_ref_usage;

  // Form Fields
  public $_color;
  public $_width;
  public $_debut_offset;
  public $_fin_offset;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'besoin_ressource';
    $spec->key   = 'besoin_ressource_id';
    $spec->xor["owner"] = array("operation_id", "protocole_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["type_ressource_id"] = "ref class|CTypeRessource notNull";
    $props["operation_id"]      = "ref class|COperation";
    $props["protocole_id"]      = "ref class|CProtocole";
    $props["commentaire"]       = "text helped";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["usages"] = "CUsageRessource besoin_ressource_id";
    return $backProps;
  }

  /**
   * Chargement du type de ressource correspondant
   *
   * @return CTypeRessource
   */
  function loadRefTypeRessource() {
    return $this->_ref_type_ressource = $this->loadFwdRef("type_ressource_id", true);
  }

  /**
   * Chargement de l'intervention correspondante
   *
   * @return COperation
   */
  function loadRefOperation() {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", true);
  }

  /**
   * Chargement du protocole correspondant
   *
   * @return CProtocole
   */
  function loadRefProtocole() {
    return $this->_ref_protocole = $this->loadFwdRef("protocole_id", true);
  }

  /**
   * Chargement de l'utilisation de la ressource correspondante
   *
   * @return CUsageRessource
   */
  function loadRefUsage() {
    return $this->_ref_usage = $this->loadUniqueBackRef("usages", true);
  }
}
