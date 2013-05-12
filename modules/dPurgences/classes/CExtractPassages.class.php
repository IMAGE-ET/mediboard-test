<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CExtractPassages extends CMbObject {
  // DB Table key
  public $extract_passages_id;
  
  // DB Fields
  public $date_extract;
  public $debut_selection;
  public $fin_selection;
  public $date_echange;
  public $nb_tentatives;
  public $message;
  public $message_valide;
  public $type;
  public $group_id;
  
  // Form fields
  public $_nb_rpus;
  public $_nb_urgences;

  /** @var CGroups */
  public $_ref_group;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'extract_passages';
    $spec->key   = 'extract_passages_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passages_rpu"] = "CRPUPassage extract_passages_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["date_extract"]    = "dateTime notNull";
    $props["debut_selection"] = "dateTime notNull";
    $props["fin_selection"]   = "dateTime notNull";
    $props["date_echange"]    = "dateTime";
    $props["nb_tentatives"]   = "num";
    $props["message"]         = "xml show|0";
    $props["message_valide"]  = "bool";
    $props["type"]            = "enum list|rpu|urg default|rpu";
    $props["group_id"]        = "ref notNull class|CGroups";
    
    $props["_nb_rpus"]        = "num";
    return $props;
  }

  /**
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id");
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->countDocItems();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
        
    $this->_nb_rpus = $this->countRPUs();
  }
  
  function countRPUs() {
    $rpu_passage = new CRPUPassage();
    $rpu_passage->extract_passages_id = $this->_id;
        
    return $rpu_passage->countMatchingList();
  }

  /**
   * Store a CFile linked to $this
   *
   * @param string $filename File name
   * @param string $filedata File contents
   *
   * @return bool
   */
  function addFile($filename, $filedata) {
    $file = new CFile();
    $file->setObject($this);
    $file->file_name = $filename;
    $file->file_type = "text/plain";
    $file->file_size = strlen($filedata);
    $file->author_id = CAppUI::$instance->user_id;
    $file->fillFields();
    if (!$file->putContent($filedata)) {
      return false;
    }
    $file->store();
    
    return true;
  }
  
  /**
   * Try and instanciate document sender according to module configuration
   *
   * @return CRPUSender|null sender or null on error
   */
  static function getRPUSender() {
    if (null == $rpu_sender = CAppUI::conf("dPurgences rpu_sender")) {
      return null;
    }
    
    if (!is_subclass_of($rpu_sender, "CRPUSender")) {
      trigger_error("Instanciation du RPU Sender impossible.");
      return null;
    }
    
    return new $rpu_sender;
  }
}
