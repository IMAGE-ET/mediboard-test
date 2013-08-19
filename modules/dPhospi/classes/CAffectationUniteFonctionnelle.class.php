<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Lien entre une UF et un élément (chambre, service, praticien, etc)
 */
class CAffectationUniteFonctionnelle extends CMbMetaObject {
  // DB Table key
  public $affectation_uf_id;
  
  // DB Fields
  public $uf_id;
  
  /** @var CUniteFonctionnelle */
  public $_ref_uf;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation_uf';
    $spec->key   = 'affectation_uf_id';
    $spec->uniques['unique']= array("object_class", "object_id", "uf_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["uf_id"]        = "ref class|CUniteFonctionnelle notNull";
    $props["object_id"]    = "ref class|CMbObject meta|object_class cascade notNull";
    $props["object_class"] = "enum list|CService|CChambre|CLit|CMediusers|CFunctions|CSejour|CProtocole show|0 notNull";
    return $props;
  }

  /**
   * Charge l'UF
   *
   * @return CUniteFonctionnelle
   */
  function loadRefUniteFonctionnelle(){
    return $this->_ref_uf = $this->loadFwdRef("uf_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUniteFonctionnelle();
    $this->_view = $this->_ref_object->_view . " : " . $this->_ref_uf->_view;
  }
}

