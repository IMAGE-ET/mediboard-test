<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * Description
 */
class CDicomPDUItemFactory {

  static $item_types = array(
    "10" => "CDicomPDUItemApplicationContext",
    "20" => "CDicomPDUItemPresentationContext",
    "30" => "CDicomPDUItemAbstractSyntax",
    "40" => "CDicomPDUItemTransferSyntax",
    "05" => "CDicomPDUAReleaseRQ",
    "06" => "CDicomPDUAReleaseRP",
    "07" => "CDicomPDUAAbort",
    "00" => "CDicomPDUItem"
  );
  
  static $next_item = null;
  
  static function decodeItem(CDicomStreamReader $stream_reader) {
    $item_type = self::getItemType($stream_reader);
    
    $item = new $item_type();
    $item->decodeItem($stream_reader);
    
    return $item;
  }
  
  static function decodeItems(CDicomStreamReader $stream_reader, $type_wanted) {
    $items = array();
    $item_type = self::getItemType($stream_reader);
    
    $type_wanted = self::$item_types[$type_wanted];

    while ($item_type == $type_wanted) {
      $item = new $item_type();
      $item->decodeItem($stream_reader);
      $items[] = $item;
      
      $item_type = self::getItemType($stream_reader);
    }
    self::$next_item = $item_type;
    
    return $items;
  }
  
  static function decodeItems(CDicomStreamReader $stream_reader, $length) {
    
  }
  
  static function encodeItem($type) {
    
  }
  
  static function getItemType(CDicomStreamReader $stream_reader) {
    $item_type = null;
    if (!self::$next_item) {
      $item_type = self::$item_types[$stream_reader->readHexByte()];
    } else {
      $item_type = self::$next_item;
      self::$next_item = null;
    }
    return $item_type;
  }
}
?>