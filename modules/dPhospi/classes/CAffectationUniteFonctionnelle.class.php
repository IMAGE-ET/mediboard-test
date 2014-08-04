<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
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

  /**
   * Retourne les affectations d'uf étant dans les bornes d'une date
   *
   * @param null $object     Object de l'affecation
   * @param null $type       Type d'uf
   * @param null $date_debut Date de début
   * @param null $date_fin   Date de fin
   *
   * @return CAffectationUniteFonctionnelle[]
   */
  function getAffDates ($object, $type, $date_debut = null, $date_fin = null) {
    $ljoin = array();
    $ljoin["uf"] = "affectation_uf.uf_id = uf.uf_id";

    $where = array();
    $where["object_id"]     = " = '$object->_id'";
    $where["object_class"]  = " = '$object->_class'";
    $where["uf.type"] = " = '$type'";
    if ($date_fin) {
      $where["uf.date_debut"] = " < '$date_fin'";
    }
    if ($date_debut) {
      $where["uf.date_fin"] = " > '$date_debut'";
    }
    return $this->loadList($where, null, null, null, $ljoin);
  }

  /**
   * @see parent::store()
   */
  function store() {
    $uf = $this->loadRefUniteFonctionnelle();
    if (count($this->getAffDates($this->loadTargetObject(), $uf->type, $uf->date_debut, $uf->date_fin))) {
      return "Collision d'affection d'unité foncitonnelle";
    }
    return parent::store();
  }

}
