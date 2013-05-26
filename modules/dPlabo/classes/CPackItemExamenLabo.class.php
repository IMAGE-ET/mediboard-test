<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CPackItemExamenLabo extends CMbObject {
  // DB Table key
  public $pack_item_examen_labo_id;

  // DB references
  public $pack_examens_labo_id;
  public $examen_labo_id;

  // Forward references
  public $_ref_pack_examens_labo;
  public $_ref_examen_labo;

  function CPackItemExamenLabo() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack_item_examen_labo';
    $spec->key   = 'pack_item_examen_labo_id';
    return $spec;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }

    // Check unique item
    $other = new CPackItemExamenLabo;
    $other->pack_examens_labo_id = $this->pack_examens_labo_id;
    $other->examen_labo_id = $this->examen_labo_id;
    $other->loadMatchingObject();
    if ($other->_id && $other->_id != $this->_id) {
      return "$this->_class-unique-conflict";
    }

    return null;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["pack_examens_labo_id"] = "ref class|CPackExamensLabo notNull";
    $props["examen_labo_id"]       = "ref class|CExamenLabo notNull";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_shortview = $this->_ref_examen_labo->_shortview;
    $this->_view      = $this->_ref_examen_labo->_view;
  }

  function loadRefPack() {
    $this->_ref_pack_examens_labo = new CPackExamensLabo;
    $this->_ref_pack_examens_labo->load($this->pack_examens_labo_id);
  }

  function loadRefExamen() {
    $this->_ref_examen_labo = new CExamenLabo;
    $this->_ref_examen_labo->load($this->examen_labo_id);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefPack();
    $this->loadRefExamen();
  }
}
