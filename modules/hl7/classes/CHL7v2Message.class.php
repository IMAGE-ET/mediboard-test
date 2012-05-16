<?php /* $Id:$ */

/**
 * Message HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Message 
 * Message HL7
 */

class CHL7v2Message extends CHL7v2SegmentGroup {
  const DEFAULT_SEGMENT_TERMINATOR      = "\r";
  const DEFAULT_ESCAPE_CHARACTER        = "\\";
  const DEFAULT_FIELD_SEPARATOR         = "|";
  const DEFAULT_COMPONENT_SEPARATOR     = "^";
  const DEFAULT_REPETITION_SEPARATOR    = "~";
  const DEFAULT_SUBCOMPONENT_SEPARATOR  = "&";
  const DEFAULT_NULL_VALUE              = '""';

  static $enteredHeaders     = array("MSH", "FHS", "BHS");
  
  static $decorateToString   = false;
  
  static $build_mode         = "normal";
  static $handle_mode        = "normal";
   
  var $segmentTerminator     = self::DEFAULT_SEGMENT_TERMINATOR;
  var $escapeCharacter       = self::DEFAULT_ESCAPE_CHARACTER;
  var $fieldSeparator        = self::DEFAULT_FIELD_SEPARATOR;
  var $componentSeparator    = self::DEFAULT_COMPONENT_SEPARATOR;
  var $repetitionSeparator   = self::DEFAULT_REPETITION_SEPARATOR;
  var $subcomponentSeparator = self::DEFAULT_SUBCOMPONENT_SEPARATOR;
  var $nullValue             = self::DEFAULT_NULL_VALUE;
  
  var $escape_sequences   = null;
  var $unescape_sequences = null;

  var $extension    = null;
  var $i18n_code    = null;
  var $version      = "2.5";
  var $event_name   = null;
  var $name         = null;
  var $description  = null;
  var $lines        = array();
  var $current_line = 0;
  var $errors       = array();
  
  function __construct($version = null) {
    if (preg_match("/([A-Z]{2})_(.*)/", $version, $matches)) {
      $this->extension = $version;
      $this->i18n_code = $matches[2];
      $this->version   = "2.5";
    } else {
      $this->version = $version;
    }  
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
  
  function toXML($event_code = null, $hl7_datatypes = true, $encoding = "utf-8") {
    $field = $this->children[0]->fields[8]->items[0];
    if ($field->children[0]->data == "ACK") {
      $name = $field->children[0]->data;
    }
    else {
      $name = $field->children[0]->data."_".$field->children[1]->data;
    }
    
    $dom = CHL7v2MessageXML::getEventType($event_code);
    $root = $dom->addElement($dom, $name);
    $dom->addNameSpaces($name);
   
    return $this->_toXML($root, $hl7_datatypes, $encoding);
  }
  
  function _toXML(DOMNode $node, $hl7_datatypes, $encoding) {
    $doc = $node->ownerDocument;
    
    foreach($this->children as $_child) {
      $_child->_toXML($node, $hl7_datatypes, $encoding);
    }
    
    return $doc;
  }
  
  function getI18NEventName() {
    if ($this->i18n_code) {
      return "{$this->event_name}_{$this->i18n_code}";
    }
    
    return $this->event_name;
  }
  
  static function fixRawER7($data) {
    $data = trim($data);
    $data = str_replace("\r\n", "\r", $data);
    $data = str_replace("\n", "\r", $data);
    return $data;
  }
  
  static function isWellFormed($data) {
    // remove all chars before MSH
    $msh_pos = strpos($data, "MSH");
    $data = substr($data, $msh_pos);
    $data = self::fixRawER7($data);
  
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
    
    // we extract the first line info "by hand"
    $first_line = CHL7v2::split($fieldSeparator, reset($lines));
    
    // validation de la syntaxe : chaque ligne doit commencer par 3 lettre + un separateur + au moins une donnée
    $sep_preg = preg_quote($fieldSeparator);
    
    foreach($lines as $_line) {
      if (!preg_match("/^[A-Z]{2}[A-Z0-9]$sep_preg.+/", $_line)) {
        throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $_line);
      }
    }
    
    return true;
  }

  function parse($data, $parse_body = true) {
    try {
      self::isWellFormed($data);
    } catch(CHL7v2Exception $e) {
      $this->error($e->getMessage(), $e->extraData);
      //return false;
    }

     // remove all chars before MSH
    $msh_pos = strpos($data, "MSH");
    $data = substr($data, $msh_pos);
    $data = self::fixRawER7($data);
    
    parent::parse($data);
    
    $message = $this->data;
    
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
      $this->name       = $message_type[0];
      if ($this->name == "ACK") {
        $this->event_name = $message_type[0];
      } 
      else {
        $this->event_name = $message_type[0].$message_type[1];
      }
    }
    else {
      $this->event_name = preg_replace("/[^A-Z0-9]/", "", $message_type[2]);
      $this->name       = substr($message_type[2], 0, 3);
    }

    $this->description = (string)$this->getSpecs()->description;
    
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
    /*elseif ($country_code == "FRA") {
      $this->i18n_code = "FR";
      $this->extension = $version = "FR_2.3";
    }*/

    // Dans le cas où la version passée est incorrecte on met par défaut 2.5
    if (!in_array($version, CHL7v2Message::$versions)) {
      $this->version = CAppUI::conf("hl7 default_version");
    }    
  }
  
  function readHeader(){
    $first_line = $this->lines[0];
    $this->current_line++;
    
    // segment from line's string
    $segment = new CHL7v2Segment($this);
    $segment->parse($first_line);
    
    // this one will be the first segment
    $this->appendChild($segment);
  }
  
  function getCurrentLineHeader(){
    return substr($this->getCurrentLine(), 0, 3);
  }
  
  function getCurrentLine($offset = 0){
    return CValue::read($this->lines, $this->current_line+$offset, null);
  }
  
  static function getNext(CHL7v2SimpleXMLElement $current_node, CHL7v2Entity $current_group) {
    // On remet les compteurs d'utilisation a zero
    CHL7v2::d("RESET", "green");
    $current_node->reset();
    
    $next = $current_node->getNextSibling();
    if ($next) {
      CHL7v2::d(" --> Suivant = frere");
      $current_node = $next;
    }
    else {
      $parent = $current_node->getParent();
      
      if ($parent && (!$parent->isOpen() || $parent->isEmpty())) {
        CHL7v2::d(" --> Suivant = suivant du parent");
        return self::getNext($parent, $current_group->parent);
      }
      
      CHL7v2::d(" --> Suivant = parent");
      $current_node = $parent;
      $current_group = $current_group->parent;
    }
    
    return array($current_node, $current_group);
  }
  
  function handleLine(CHL7v2SimpleXMLElement $current_node, CHL7v2Entity $current_group) {
    // Increment du nb d'occurences
    $current_node->markUsed();
    
    // On enregistre le segment dans le groupe courant
    $_segment = new CHL7v2Segment($current_group);
    $_segment->parse($this->getCurrentLine());
    $current_group->appendChild($_segment);
    
    // On avance dans le fichier
    $this->current_line++;
    CHL7v2::d(" --> ### Creation du segment ###, ligne suivante : $this->current_line");
  }
  
  function readSegments() {
    $specs = $this->getSpecs();
    
    /**
     * Premier segment/groupe dans le fichier de spec venant de Mirth
     * 
     * @var CHL7v2SimpleXMLElement
     */
    $current_node = next($specs->xpath("/message/segments/*"));
    
    /**
     * Groupe courant dans lequel on va placer les CHL7v2Segment créés
     * 
     * @var CHL7v2SegmentGroup
     */
    $current_group = $this;
    
    $lines_count = count($this->lines);
    
    $n = 500; // pour eviter les boucles infinies !

    while($n-- && trim($this->getCurrentLine())/* && $current_node && $this->current_line < $lines_count*/) {
      if (!$current_node && $this->current_line <= count($this->children)) {
        $this->error(CHL7v2Exception::UNEXPECTED_SEGMENT, $this->getCurrentLine());
        break;
      }
      switch($current_node->getName()) {
        // SEGMENT //
        case "segment":
          CHL7v2::d($current_node->getSegmentHeader()." ".$current_node->state(), "red");
          
          $handled = false;
          
          if ($this->getCurrentLineHeader() == "") {
            break 2;
          }

          $seg_schema = $this->getSchema(self::PREFIX_SEGMENT_NAME, $this->getCurrentLineHeader(), $this->getMessage()->extension);
          if ($seg_schema == false) {
            $this->error(CHL7v2Exception::UNKOWN_SEGMENT_TYPE, $this->getCurrentLine());
            break 2;
          }
          
          // Si la spec correspond a la ligne courante
          if ($this->getCurrentLineHeader() == $current_node->getSegmentHeader()) {
            $this->handleLine($current_node, $current_group);
            $current_node->markNotEmpty();
            $handled = true;
          }
          
          // Segment non requis, on passe au suivant
          elseif(!$current_node->isRequired()) {
            CHL7v2::d(" --> Segment non présent et non requis");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
            break;
          }
          
          // Si le segment est requis et que le groupe est ouvert, alors erreur
          // pas de parent si à la racine (fils de <segments>) : bizarre 
          else {
            if (!$current_node->getParent() || $current_node->getParent()->isOpen()) {
              CHL7v2::d(" --> !!!!!!!!!!!!!!!!! Segment non present et groupe requis");
              $this->error(CHL7v2Exception::SEGMENT_MISSING, (string)$current_node);
            }
          }
       
          // le segment est multiple, on reste sur lui
          if ($handled && $current_node->isUnbounded()) {
            CHL7v2::d(" --> Segment multiple");
          }
          
          // Segment unique : Segment/groupe suivant ou suivant du parent
          else {
            CHL7v2::d(" --> Segment unique, passage au suivant");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
          }
          break;
          
          
        // GROUP //
        case "group":
          CHL7v2::d($current_group->name." ".$current_node->state(), "red");
          $current_node->markEmpty();
          
          if ($current_node->isUnbounded() || !$current_node->isUsed()) {
            CHL7v2::d(" --> Groupe multiple ou pas encore utilisé, on entre dedans");
            $current_group = new CHL7v2SegmentGroup($current_group, $current_node);
            
            $current_node = $current_node->getFirstChild();
          }
          else {
            CHL7v2::d(" --> Groupe utilisé ou pas multiple, on prend le parent ou frere");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
          }
          
          break;
          
          // custom attributes, should never get there
        default: 
          CHL7v2::d($current_node->getName()); 
          $current_node = $current_node->getNextSibling();
          break;
      }
    }
    
    $this->purgeEmptyGroups();
    
    // pas forcément utile : mais ceci donne tous les segments dans 
    // l'ordre de parcours, comme si on le faisait recursivement
    // $c = $specs->xpath("//segment | //group");
  }
  
  protected function validateSyntax() {
    
  }
  
  /*
  function validate() {
    // @todo validate segments sequence
  }
  */
  
  /**
   * @return string
   */
  function getVersion(){
    return $this->version;
  }
  
  /**
   * @return CHL7v2SimpleXMLElement
   */
  function getSpecs(){
    return $this->getSchema(self::PREFIX_MESSAGE_NAME, $this->event_name, $this->getMessage()->extension);
  }
  
  /**
   * @return CHL7v2Message
   */
  function getMessage() {
    return $this;
  }
  
  function getEncoding() {
    return "utf-8";
    
    /*if (isset($this->children[0]->fields[17]->items[0])) {
      $encoding = CHL7v2TableEntry::mapFrom(211, $this->children[0]->fields[17]->items[0]->data);
    }
    
    return strtolower($encoding);*/
  }
  
  function error($code, $data, $entity = null, $level = CHL7v2Error::E_ERROR) {
    $error = new CHL7v2Error;
    $error->line = $this->current_line+1;
    $error->entity = $entity;
    $error->code = $code;
    $error->data = $data;
    $error->level = $level;
    
    $this->errors[] = $error;
  }
  
  function isOK($min_level = 0) {
    foreach ($this->errors as $_error) {
      if ($_error->level >= $min_level) {
        return false;
      }
    }
    
    return true;
  }
  
  function dumpErrors($min_level = 0){
    $errors = array();
    
    foreach ($this->errors as $_error) {
      if ($_error->level > $min_level) {
        $_code  = CAppUI::tr("CHL7v2Exception-$_error->code");
        $_entity = ($_error->entity ? $_error->entity->getPathString().", " : "");
        $errors[] = "Ligne $_error->line : $_code - $_entity $_error->data";
      }
    }
    
    return $errors;
  }
  
  private function getDelimEscSeq($seq) {
    return $this->escapeCharacter.$seq.$this->escapeCharacter;
  }
  
  function initEscapeSequences() {
    if (!empty($this->escape_sequences)) {
      return;
    }
    
    $delimiters = array(
      $this->segmentTerminator     => "X0D",
      $this->fieldSeparator        => "F",
      $this->componentSeparator    => "S",
      $this->subcomponentSeparator => "T",
      $this->escapeCharacter       => "E",
      $this->repetitionSeparator   => "R",
    );
    $this->escape_sequences = array_map(array($this, "getDelimEscSeq"), $delimiters);
    
    $this->unescape_sequences = array_flip($this->escape_sequences);
  }
  
  function escape($str){
    //$str = str_replace("\r\n", "\n", $str);
    $this->initEscapeSequences();
    return strtr($str, $this->escape_sequences);
  }
  
  static function unichr($h) {
    return mb_convert_encoding("&#x$h;", 'UTF-8', 'HTML-ENTITIES');
  }
  
  function unescape($str) {
    $this->initEscapeSequences();
    
    /*if ($str === $this->nullValue) {
      return null; //"__NULL__";
    }*/
    
    $str = strtr($str, $this->unescape_sequences);
    
    $esc = preg_quote($this->escapeCharacter, "/");
    
    //  \Xxx\ => ascii char of xx
    $str = preg_replace("/{$esc}X(\d\d){$esc}/e", 'chr(hexdec("$1"))', $str);
    
    //  \Cxxyy\
    //$str = preg_replace("/{$esc}C([0-9A-F]{4}){$esc}/e", 'CHL7v2Message::unichr($1)', $str);
    
    //  \Mxxyyzz\
    $str = preg_replace("/{$esc}M([0-9A-F]{4}(?:[0-9A-F]{2})?){$esc}/", '&#x$1;', $str);
    
    return $str;
  }
  
  function format($str) {
    $esc = preg_quote($this->escapeCharacter, "/");
    $str = preg_replace("/{$esc}H{$esc}(.*){$esc}N{$esc}/", '<strong>$1</strong>', $str);
    
    $formats = array(
      ".br" => "<br />",
      // more
    );
    
    $format_sequences = array_flip(array_map(array($this, "getDelimEscSeq"), array_flip($formats)));
    $str = strtr($str, $format_sequences);
    
    return $str;
  }
  
  function encoding_characters() {
    return $this->fieldSeparator.$this->componentSeparator.$this->repetitionSeparator.$this->escapeCharacter.$this->subcomponentSeparator;
  }
  
  static function highlight_er7($msg){
    $msg = str_replace("\r", "\n", $msg);
    
    preg_match("/.*MSH(.)(.)(.)(.)(.)/", $msg, $matches);
    
    // highlight segment name
    $msg = preg_replace("/^([A-Z]{2}[A-Z0-9])/m", '<strong>$1</strong>', $msg);
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
  
  function __toString(){
    // Il y a des lignes vides a cause des goupes imbriqués
    $str = parent::__toString();
    $sep = preg_quote($this->getMessage()->segmentTerminator);
    $str = preg_replace("/$sep+/", $sep, $str);
    return $str;
  }
  
  function flatten($highlight = false){
    $old = self::$decorateToString;
    self::$decorateToString = $highlight;
    
    $str = $this->__toString();
    
    if ($highlight) {
      $str = "<pre class='er7'>$str</pre>";
    }
    
    self::$decorateToString = $old;
    return $str;
  }
}
