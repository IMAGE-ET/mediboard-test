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
class CHL7v2Error {
  const E_ERROR = 2;
  const E_WARNING = 1;
  
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
}

?>