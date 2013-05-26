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

class CPackExamensLabo extends CMbObject {
  // DB Table key
  public $pack_examens_labo_id;

  // DB references
  public $function_id;
  public $code;
  public $obsolete;

  // DB fields
  public $libelle;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CPackItemExamenLabo[] */
  public $_ref_items_examen_labo;

  /** @var CExamenLabo[] */
  public $_ref_examens_labo;

  function CPackExamensLabo() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack_examens_labo';
    $spec->key   = 'pack_examens_labo_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specsParent = parent::getProps();
    $specs = array (
      "code"         => "num",
      "function_id"  => "ref class|CFunctions",
      "libelle"      => "str notNull",
      "obsolete"     => "bool"
    );
    return array_merge($specsParent, $specs);
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items_examen_labo"]         = "CPackItemExamenLabo pack_examens_labo_id";
    $backProps["prescriptions_labo_examen"] = "CPrescriptionLaboExamen pack_examens_labo_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->libelle;
    $this->_view      = $this->libelle;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefFunction();
  }

  function loadRefFunction() {
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }

  function loadRefsItemExamenLabo(){
    $item = new CPackItemExamenLabo;
    $ljoin["examen_labo"] = "pack_item_examen_labo.examen_labo_id = examen_labo.examen_labo_id";
    $where = array("pack_examens_labo_id" => "= '$this->pack_examens_labo_id'");
    // Permet d'afficher dans le pack seulement les analyses non obsolètes
    $where["examen_labo.obsolete"] = " = '0'";
    $this->_ref_items_examen_labo = $item->loadList($where, null, null, null, $ljoin);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsItemExamenLabo();
    $this->_ref_examens_labo = array();
    foreach ($this->_ref_items_examen_labo as &$_item) {
      $_item->loadRefExamen();
      $_item->_ref_pack_examens_labo =& $this;
      $this->_ref_examens_labo[$_item->examen_labo_id] = $_item->_ref_examen_labo;
    }
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($perm_type) {
    if ($this->function_id) {
      $this->loadRefFunction();
      return $this->_ref_function->getPerm($perm_type);
    }

    return true;
  }
}
