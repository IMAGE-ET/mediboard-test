<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CExtractPassages extends CMbObject {
  // DB Table key
  var $extract_passages_id     = null;
  
  // DB Fields
  var $date_extract    = null;
  var $debut_selection = null;
  var $fin_selection   = null;
  var $date_echange    = null;
  var $nb_tentatives   = null;
  var $message         = null;
  var $message_valide  = null;
  var $type            = null;
  
  // Form fields
  var $_nb_rpus        = null;
  var $_nb_urgences    = null;  
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'extract_passages';
    $spec->key   = 'extract_passages_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passages_rpu"] = "CRPUPassage extract_passages_id";
    return $backProps;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["date_extract"]    = "dateTime notNull";
    $specs["debut_selection"] = "dateTime notNull";
    $specs["fin_selection"]   = "dateTime notNull";
    $specs["date_echange"]    = "dateTime";
    $specs["nb_tentatives"]   = "num";
    $specs["message"]         = "xml show|0";
    $specs["message_valide"]  = "bool";
    $specs["type"]            = "enum list|rpu|urg default|rpu";
    
    $specs["_nb_rpus"]        = "num";
    return $specs;
  }
  
  function loadRefsBack() {
    // Backward references
    $this->countDocItems();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
        
    $this->_nb_rpus = $this->countRPUs();
  }
  
  function countRPUs() {
    $rpu_passage = new CRPUPassage();
    $rpu_passage->extract_passages_id = $this->_id;
        
    return $rpu_passage->countMatchingList();
  }
  
  function addFile($filename, $filedata) {
    $file = new CFile();
    $file->setObject($this);
    $file->file_name = $filename;
    $file->file_type = "text/plain";
    $file->file_size = strlen($filedata);
    $file->file_owner = CAppUI::$instance->user_id;
    $file->fillFields();
    if (!$file->putContent($filedata)) {
      return false;
    }
    $file->store();
    
    return true;
  }
  
  /**
   * Try and instanciate document sender according to module configuration
   * @return CRPUSender sender or null on error
   */
  static function getRPUSender() {
    if (null == $rpu_sender = CAppUI::conf("dPurgences rpu_sender")) {
      return;
    }
    
    if (!is_subclass_of($rpu_sender, "CRPUSender")) {
      trigger_error("Instanciation du RPU Sender impossible.");
      return;
    }
    
    return new $rpu_sender;
  }
}
?>