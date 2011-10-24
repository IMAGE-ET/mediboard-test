<?php

/**
 * HL7v2 Error
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Error 
 */
class CHL7v2Error extends CHL7v2Exception {
  const E_ERROR = 2;
  const E_WARNING = 1;
	
	static $errorMap = array(
    self::EMPTY_MESSAGE              => 100,
    self::INVALID_SEPARATOR          => 102,
    self::SEGMENT_INVALID_SYNTAX     => 102,
    self::TOO_FEW_SEGMENT_FIELDS     => 101,
    self::TOO_MANY_FIELDS            => 102,
    self::SPECS_FILE_MISSING         => 207,
    self::VERSION_UNKNOWN            => 203,
    self::INVALID_DATA_FORMAT        => 102,
    self::FIELD_EMPTY                => 101,
    self::TOO_MANY_FIELD_ITEMS       => 102,
    self::SEGMENT_MISSING            => 100,
    self::MSG_CODE_MISSING           => 201,
    self::UNKNOWN_AUTHORITY          => 207,
    self::UNEXPECTED_DATA_TYPE       => 207,
    self::DATA_TOO_LONG              => 102,
    self::UNKNOWN_TABLE_ENTRY        => 103,
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
	
	function getHL7Code(){
		return CValue::read(self::$errorMap, $this->code, 207);
	}
}

?>