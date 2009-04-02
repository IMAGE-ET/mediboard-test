<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

if (!class_exists("DOMDocument")) {
  return;
}

class CMbXMLDocument extends DOMDocument {
  
  var $schemapath       = null;
  var $schemafilename   = null;
  var $documentfilename = null;
  var $now              = null;
  
  function __construct() {
    parent::__construct("1.0", "iso-8859-1");
    
    $this->preserveWhiteSpace = false;
    $this->formatOutput = true;
  }
  
  function setDocument($documentfilename) {
    $this->documentfilename = $documentfilename;
  }
  
  function setSchema($schemafilename) {
    $this->schemapath     = dirname($schemafilename);
    $this->schemafilename = $schemafilename;
  }
  
	/**
	 * Try to load and validate XML File
	 * @param $docPath string Uploaded file temporary path
	 * @return string Store-like message 
	 */
	function loadAndValidate($docPath) {
	  // Chargement
	  if (!$this->load($docPath)) {
	    return "Le fichier fourni n'est pas un document XML bien formé";
	  }
	  
	  // Validation
	  if ($this->checkSchema() && !$this->schemaValidate()) {
	    return "Catalogue d'élements de prescriptions invalide";
	  }
	  
	  return null;
	}
  
  function checkSchema() {
    if(!$this->schemafilename) {
      trigger_error("You haven't set the schema", E_USER_WARNING);
      return false;
    }
    if (!is_dir($this->schemapath)) {
      trigger_error("Schema directory is missing ($this->schemapath/)", E_USER_WARNING);
      return false;
    }
    
    if (!is_file($this->schemafilename)) {
      trigger_error("Schema is missing ($this->schemafilename)", E_USER_WARNING);
      return false;
    }
    
    return true;
  }

  function libxml_display_error($error) {
     $return = "<br/>\n";
     switch ($error->level) {
         case LIBXML_ERR_WARNING:
             $return .= "<b>Warning $error->code</b>: ";
             break;
         case LIBXML_ERR_ERROR:
             $return .= "<b>Error $error->code</b>: ";
             break;
         case LIBXML_ERR_FATAL:
             $return .= "<b>Fatal Error $error->code</b>: ";
             break;
     }
     $return .= trim($error->message);
     if ($error->file) {
         $return .=    " in <b>$error->file</b>";
     }
     $return .= " on line <b>$error->line</b>\n";
  
     return $return;
  }
  
  function libxml_display_errors() {
     $errors = libxml_get_errors();
     $chain_errors = "";
     
     foreach ($errors as $error) {
     	 $chain_errors .= strip_tags(preg_replace('/( in\ \/(.*))/', '', $this->libxml_display_error($error)))."\n";
       trigger_error($this->libxml_display_error($error), E_USER_WARNING);
     }
     libxml_clear_errors();

     return $chain_errors;
  }
  
  /**
   * Try to validate the document against a schema
   * will trigger errors when not validating
   * @param string Path of schema, use document inline schema if null 
   * @return boolean  
   */
  function schemaValidate($filename = null, $returnErrors = false) {
    if (!CAppUI::conf("dPinterop hprim_export validation")) {
      return true;
    }
    
    if (!$filename) {
      $filename = $this->schemafilename;
    }

    // PHP < 5.1.x
    if (!function_exists("libxml_use_internal_errors")) {
      return parent::schemaValidate($filename);
    }
    
    // Enable user error handling
    libxml_use_internal_errors(true);
    
    if (!parent::schemaValidate($filename)) {
       $errors = $this->libxml_display_errors();
       	 return $returnErrors ? $errors : false;
    }
    
    return true;
  }
  
  function libxml_tabs_erros() {
  	
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = null) {
    $elName  = utf8_encode($elName );
    $elValue = utf8_encode($elValue);
    return $elParent->appendChild(new DOMElement($elName, $elValue, $elNS));
	}
  
  function addDateTimeElement($elParent, $elName, $dateValue = null) {
    $this->addElement($elParent, $elName, mbTransformTime(null, $dateValue, "%Y-%m-%dT%H:%M:%S"));
  }
  
  function addAttribute($elParent, $atName, $atValue) {
    $atName  = utf8_encode($atName );
    $atValue = utf8_encode($atValue);
    return $elParent->setAttribute($atName, $atValue);
  }

  function purgeEmptyElements() {
    $this->purgeEmptyElementsNode($this->documentElement);
  }
  
  function purgeEmptyElementsNode($node) {
    // childNodes undefined for non-element nodes (eg text nodes)
    if ($node->childNodes) {
      // Copy childNodes array
      $childNodes = array();
      foreach($node->childNodes as $childNode) {
        $childNodes[] = $childNode;
      }
 
      // Browse with the copy (recursive call)    
      foreach ($childNodes as $childNode) {
        $this->purgeEmptyElementsNode($childNode);      
      }
      
      // Remove if empty
      if (!$node->hasChildNodes() && !$node->hasAttributes()) {
//        trigger_error("Removing child node $node->nodeName in parent node {$node->parentNode->nodeName}", E_USER_NOTICE);
        $node->parentNode->removeChild($node);
      }
    }
  }
  
  function saveFile() {
    parent::save($this->documentfilename);
  }
  
  /**
   * Create a CFile attachment to given CMbObject
   * @return string store-like message, null if successful
   */
  function addFile($object) {
    global $AppUI;
    $this->saveFile();
    $file = new CFile();
    $file->object_id          = $object->_id;
    $file->object_class       = $object->_class_name;
    $file->file_name          = $object->_class_name."-".$object->_id.".xml";
    $file->file_type          = "text/xml";
    $file->file_size          = filesize($this->documentfilename);
    $file->file_date          = mbDateTime();
    $file->file_real_filename = uniqid(rand());
    $file->file_owner         = $AppUI->user_id;
    if (!$file->moveFile($this->documentfilename)) {
      return "error-CFile-move-file";
    }

    return $file->store();
  }
}

?>