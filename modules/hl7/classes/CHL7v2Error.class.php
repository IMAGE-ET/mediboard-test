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
 * Class CHL7v2Error 
 */
class CHL7v2Error {
  const E_ERROR = 2;
  const E_WARNING = 1;
  
  static $errorMap = array(
    CHL7v2Exception::EMPTY_MESSAGE              => 100,
    CHL7v2Exception::INVALID_SEPARATOR          => 102,
    CHL7v2Exception::SEGMENT_INVALID_SYNTAX     => 102,
    CHL7v2Exception::TOO_MANY_FIELDS            => 102,
    CHL7v2Exception::SPECS_FILE_MISSING         => 207,
    CHL7v2Exception::VERSION_UNKNOWN            => 203,
    CHL7v2Exception::INVALID_DATA_FORMAT        => 102,
    CHL7v2Exception::FIELD_EMPTY                => 101,
    CHL7v2Exception::TOO_MANY_FIELD_ITEMS       => 102,
    CHL7v2Exception::SEGMENT_MISSING            => 100,
    CHL7v2Exception::MSG_CODE_MISSING           => 201,
    CHL7v2Exception::UNKNOWN_AUTHORITY          => 207,
    CHL7v2Exception::UNEXPECTED_DATA_TYPE       => 207,
    CHL7v2Exception::DATA_TOO_LONG              => 102,
    CHL7v2Exception::UNKNOWN_TABLE_ENTRY        => 103,
  );
  
  /**
   * @var integer
   */
  public $line;
  
  /**
   * @var CHL7v2Entity
   */
  public $entity;
  
  /**
   * @var integer
   */
  public $code;
  
  /**
   * @var string
   */
  public $data;
  
  /**
   * @var string
   */
  public $level = self::E_WARNING;
  
  function getLocation(){
    $entity = $this->entity;
    
    if (!$entity) return null;
    
    $path = array();
    
    $segment = $entity->getSegment();
    if ($segment) {
      $path[] = $segment->name;
      $path[] = null;
      
      CHL7v2FieldItem::$_get_path_full = true;
      $path = array_merge($path, $entity->getPath());
      CHL7v2FieldItem::$_get_path_full = false;
    }
    
    return $path;
  }
  
  function getHL7Code() {
    return CValue::read(self::$errorMap, $this->code, 207);
  }
  
  function getCodeLocation() {
    return array (
      $this->getLocation(),
      $this->getHL7Code()
    );
  }
}

?>