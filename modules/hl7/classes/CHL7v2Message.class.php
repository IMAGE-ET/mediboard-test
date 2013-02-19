<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2Message 
 * Message HL7
 */
class CHL7v2Message extends CHMessage {
  static $enteredHeaders     = array("MSH", "FHS", "BHS");
  
  static $build_mode         = "normal";
  static $handle_mode        = "normal";

  static $header_segment_name = "MSH";
  static $segment_header_pattern = "[A-Z]{2}[A-Z0-9]";

  protected $keep_original = array("MSH.1", "MSH.2", "NTE.3", "OBX.5");

  var $extension    = null;
  var $i18n_code    = null;
  var $version      = "2.5";
  
  function __construct($version = null) {
    if (preg_match("/([A-Z]{2})_(.*)/", $version, $matches)) {
      $this->extension = $version;
      $this->i18n_code = $matches[2];
      $this->version   = "2.5";
    }
    else {
      parent::__construct($version);
    }  
  }

  function getHeaderSegmentName() {
    return self::$header_segment_name;
  }

  static function setBuildMode($build_mode) {
    self::$build_mode = $build_mode;
  }
  
  static function resetBuildMode() {
    self::$build_mode = "normal";
  }
  
  static function setHandleMode($handle_mode) {
    self::$handle_mode = $handle_mode;
  }
  
  static function resetHandleMode() {
    self::$handle_mode = "normal";
  }
  
  function getI18NEventName() {
    if ($this->i18n_code) {
      return "{$this->event_name}_{$this->i18n_code}";
    }
    
    return $this->event_name;
  }
  
  function toXML($event_code = null, $hl7_datatypes = true, $encoding = "utf-8") {
    $name = $this->getXMLName();
    
    $dom = CHL7v2MessageXML::getEventType($event_code);
    $root = $dom->addElement($dom, $name);
    $dom->addNameSpaces($name);
   
    return $this->_toXML($root, $hl7_datatypes, $encoding);
  }

  function getXMLName(){
    $field = $this->children[0]->fields[8]->items[0];
    if ($field->children[0]->data == "ACK") {
      return $field->children[0]->data;
    }

    return $field->children[0]->data."_".$field->children[1]->data;
  }
  
  static function isWellFormed($data, $strict_segment_terminator = false) {
    // remove all chars before MSH
    $msh_pos = strpos($data, self::$header_segment_name);
    if ($msh_pos === false) {
      throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $data);
    }
    
    $data = substr($data, $msh_pos);
    $data = self::fixRawER7($data, $strict_segment_terminator);
  
    // first tokenize the segments
    if (($data == null) || (strlen($data) < 4)) {
      throw new CHL7v2Exception(CHL7v2Exception::EMPTY_MESSAGE, $data);
    }
    
    $fieldSeparator = $data[3];
    
    // valid separator
    if (!preg_match("/[^a-z0-9]/i", $fieldSeparator) ) {
      throw new CHL7v2Exception(CHL7v2Exception::INVALID_SEPARATOR, substr($data, 0, 10));
    }
    
    $lines = CHL7v2::split(self::DEFAULT_SEGMENT_TERMINATOR, $data);
    
    // validation de la syntaxe : chaque ligne doit commencer par 3 lettre + un separateur + au moins une donnée
    $sep_preg = preg_quote($fieldSeparator);

    $pattern = self::$segment_header_pattern;
    foreach ($lines as $_line) {
      if (!preg_match("/^($pattern)$sep_preg/", $_line)) {
        throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $_line);
      }
    }
    
    return true;
  }

  function parse($data, $parse_body = true) {
    try {
      self::isWellFormed($data, $this->strict_segment_terminator);
    } catch(CHL7v2Exception $e) {
      $this->error($e->getMessage(), $e->extraData);
      //return false;
    }

     // remove all chars before MSH
    $msh_pos = strpos($data, self::$header_segment_name);
    
    if ($msh_pos === false) {
      throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $data);
    }
    
    $data = substr($data, $msh_pos);
    $data = self::fixRawER7($data, $this->strict_segment_terminator);
    
    parent::parse($data);
    
    $message = $this->data;
    
    // 4 to 7
    if (!isset($message[7])) {
      throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $message);
    }
    
    $this->fieldSeparator = $message[3];
    
    $nextDelimiter = strpos($message, $this->fieldSeparator, 4);
    if ($nextDelimiter > 4) {
      // usually ^
      $this->componentSeparator = $message[4];
    }
    if ($nextDelimiter > 5) {
      // usually ~
      $this->repetitionSeparator = $message[5];
    }
    if ($nextDelimiter > 6) {
      // usually \
      $this->escapeCharacter = $message[6];
    }
    if ($nextDelimiter > 7) {
      // usually &
      $this->subcomponentSeparator = $message[7];
    }

    // replace the special case of ^~& with ^~\&
    if ("^~&|" == substr($message, 4, 4)) {
      $this->escapeCharacter       = "\\";
      $this->subcomponentSeparator = "&";
      $this->repetitionSeparator   = "~";
      $this->componentSeparator    = "^";
    }
    
    $this->initEscapeSequences();
    
    $this->lines = CHL7v2::split($this->segmentTerminator, $this->data);
    
    // we extract the first line info "by hand"
    $first_line = CHL7v2::split($this->fieldSeparator, reset($this->lines));
    
    if (!isset($first_line[11])) {
      throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $message);
    }
    
    // version
    if (array_key_exists(16, $first_line)) {
      $this->parseRawVersion($first_line[11], $first_line[16]);
    }
    else {
      $this->parseRawVersion($first_line[11]);
    }
    
    // message type
    $message_type = explode($this->componentSeparator, $first_line[8]);

    if ($message_type[0]) {
      $this->name  = $message_type[0];

      if ($this->name == "ACK") {
        $this->event_name = $message_type[0];
      }
      else {
        if (isset($message_type[2]) && $message_type[2] && !preg_match("/^[A-Z]{3}_[A-Z]\d{2}$/", $message_type[2])) {
          throw new CHL7v2Exception(CHL7v2Exception::WRONG_MESSAGE_TYPE, $message_type[2]);
        }

        if (strlen($message_type[0]) != 3 || strlen($message_type[1]) != 3) {
          $msg = $message_type[0].$this->componentSeparator.$message_type[1];
          throw new CHL7v2Exception(CHL7v2Exception::WRONG_MESSAGE_TYPE, $msg);
        }

        $this->event_name = $message_type[0].$message_type[1];
      }
    }
    else {
      $this->event_name = preg_replace("/[^A-Z0-9]/", "", $message_type[2]);
      $this->name       = substr($message_type[2], 0, 3);
    }
    
    if (!$spec = $this->getSpecs()) {
      throw new CHL7v2Exception(CHL7v2Exception::UNKNOWN_MSG_CODE);
    }
    
    $this->description = (string)$spec->description;
    
    $this->readHeader();
    
    if ($parse_body) {
      $this->readSegments();
    }
  }
  
  private function parseRawVersion($raw, $country_code = null){
    $parts = explode($this->componentSeparator, $raw);
   
    CMbArray::removeValue("", $parts);
    
    $this->version = $version = $parts[0];
    
    // Version spécifique française spécifiée
    if (count($parts) > 1) {
      $this->i18n_code = $parts[1];
      $this->extension = $version = "$parts[1]_$parts[2]";
    } 
    // Recherche depuis le code du pays
    /* @todo Pas top */
    elseif ($country_code == "FRA") {
      $this->i18n_code = "FR";
      $this->extension = $version = "FR_2.3";
    }

    // Dans le cas où la version passée est incorrecte on met par défaut 2.5
    if (!in_array($version, self::$versions)) {
      $this->version = CAppUI::conf("hl7 default_version");
    }
  }

  function getSchema($type, $name) {
    $extension = $this->extension;

    /*if (empty(self::$schemas)) {
      self::$schemas = SHM::get("hl7-v2-schemas");
    }*/

    $version = $this->getVersion();

    if (isset(self::$schemas[$version][$type][$name][$extension])) {
      return self::$schemas[$version][$type][$name][$extension];
    }

    if (!in_array($version, self::$versions)) {
      $this->error(CHL7v2Exception::VERSION_UNKNOWN, $version);
    }

    if ($extension && $extension !== "none" && preg_match("/([A-Z]{2})_(.*)/", $extension, $matches)) {
      $lang = strtolower($matches[1]);
      $v    = "v".str_replace(".", "_", $matches[2]);
      $version_dir = "extensions/$lang/$v";
    }
    else {
      $version_dir = "hl7v".preg_replace("/[^0-9]/", "_", $version);
    }

    $name_dir = preg_replace("/[^A-Z0-9_]/", "", $name);

    $this->spec_filename = self::LIB_HL7."/$version_dir/$type$name_dir.xml";

    if (!file_exists($this->spec_filename)) {
      $this->error(CHL7v2Exception::SPECS_FILE_MISSING, $this->spec_filename);
    }

    $schema = @simplexml_load_file($this->spec_filename, "CHL7v2SimpleXMLElement");

    self::$schemas[$version][$type][$name][$extension] = $schema;

    //SHM::put("hl7-v2-schemas", self::$schemas);

    return $this->specs = $schema;
  }

  function loadDataType($datatype) {
    return CHL7v2DataType::load($this, $datatype, $this->getVersion(), $this->extension);
  }

  static function highlight($msg){
    $msg = str_replace("\r", "\n", $msg);

    $prefix = self::$header_segment_name;
    preg_match("/^[^$prefix]*$prefix(.)(.)(.)(.)(.)/", $msg, $matches);

    // highlight segment name
    $pattern = self::$segment_header_pattern;
    $msg = preg_replace("/^($pattern)/m", '<strong>$1</strong>', $msg);
    $msg = preg_replace("/^(.*)/m", '<div class="segment">$1</div>', $msg); // we assume $message->segmentTerminator is always \n
    $msg = str_replace("\n", "", $msg);

    $pat = array(
      $matches[1] => "<span class='fs'>$matches[1]</span>",
      $matches[2] => "<span class='cs'>$matches[2]</span>",
      $matches[3] => "<span class='scs'>$matches[3]</span>",
      $matches[4] => "<span class='re'>$matches[4]</span>",
    );

    return "<pre class='er7'>".strtr($msg, $pat)."</pre>";
  }

  /**
   * Get segment
   *
   * @param string $name Segment name
   *
   * @return CHL7v2Segment
   */
  function getSegmentByName($name) {
    foreach ($this->children as $_segment) {
      if ($_segment->name == $name) {
        return $_segment;
      }
    }
  }
}
