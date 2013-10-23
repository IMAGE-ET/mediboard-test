<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * The CFilesCategory class
 */
class CFilesCategory extends CMbObject {
  // DB Table key
  public $file_category_id;
  public $nom;
  public $class;
  public $send_auto;

  public $_count_documents;
  public $_count_files;
  public $_count_doc_items;

  public $_count_unsent_documents;
  public $_count_unsent_files;
  public $_count_unsent_doc_items;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_category';
    $spec->key   = 'file_category_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["categorized_documents"] = "CCompteRendu file_category_id";
    $backProps["categorized_files"]     = "CFile file_category_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]   = "str notNull seekable";
    $props["class"] = "str";
    $props["send_auto"] = "bool";
    return $props;
  }

  /**
   * @see parent::countDocItems()
   */
  function countDocItems($permType = null) {
    $this->_count_documents = $this->countBackRefs("categorized_documents");
    $this->_count_files     = $this->countBackRefs("categorized_files"    );
    $this->_count_doc_items = $this->_count_documents + $this->_count_files;
  }

  /**
   * Count unsent document items
   *
   * @return void
   */
  function countUnsentDocItems() {
    $where["file_category_id"] = "= '$this->_id'";
    $where["etat_envoi"      ] = "!= 'oui'";
    $where["object_id"       ] = "IS NOT NULL";

    $file = new CFile();
    $this->_count_unsent_files = $file->countList($where);;

    $document = new CCompteRendu();
    $this->_count_unsent_documents = $document->countList($where);
    $this->_count_unsent_doc_items = $this->_count_unsent_documents + $this->_count_unsent_files;
  }

  /**
   * Load categories by class
   *
   * @return self[]
   */
  static function loadListByClass() {
    $category = new CFilesCategory();

    /** @var CFilesCategory[] $categories */
    $categories = $category->loadList(null, "nom");

    $catsByClass = array();
    foreach ($categories as $_category) {
      $catsByClass[$_category->class][$_category->_id] = $_category; 
    }
    unset($catsByClass[""]);
    return $catsByClass;
  }

  /**
   * Get the list of categories for a specific class
   *
   * @param string $class Class name
   *
   * @return self[]
   */
  static function listCatClass($class = null) {
    $instance = new CFilesCategory();
    $where = array(
      $instance->_spec->ds->prepare("`class` IS NULL OR `class` = %", $class)
    );
    return $instance->loadList($where, "nom");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}
