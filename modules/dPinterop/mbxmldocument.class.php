<?php /* $Id: mbxmldocument.class.php,v 1.7 2006/04/07 15:01:35 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.7 $
* @author Thomas Despoix
*/

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

class CMbXMLDocument extends DOMDocument {
  function __construct() {
    parent::__construct("1.0", "iso-8859-1");

    $this->format_output = true;
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
     foreach ($errors as $error) {
         trigger_error($this->libxml_display_error($error), E_USER_WARNING);
     }
     libxml_clear_errors();
  }
  
  function schemaValidate($filename) {
    // PHP < 5.1.x
    if (!function_exists("libxml_use_internal_errors")) {
      return parent::schemaValidate($filename);
    }
    
    // Enable user error handling
    libxml_use_internal_errors(true);
    
    if (!parent::schemaValidate($filename)) {
       $this->libxml_display_errors();
       return false;
    }
    
    return true;
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = "http://www.hprim.org/hprimXML") {
    $elName  = utf8_encode($elName );
    $elValue = utf8_encode($elValue);
    return $elParent->appendChild(new DOMElement($elName, $elValue, $elNS));
	}
  
  function addDateTimeElement($elParent, $elName, $dateValue = null) {
    $this->addElement($elParent, $elName, mbTranformTime(null, $dateValue, "%Y-%m-%dT%H:%M:%S"));
  }
  
  function addAttribute($elParent, $atName, $atValue) {
    $atName  = utf8_encode($atName );
    $atValue = utf8_encode($atValue);
    return $elParent->setAttribute($atName, $atValue);
  }
}

?>