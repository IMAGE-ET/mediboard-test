<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * The PDUItem Factory, who matches the type of item and the corresponding PHP class
 */
class CDicomPDUItemFactory {

  /**
   * Make the link between the code types and the PDU items classes
   * 
   * @var array
   */
  static $item_types = array(
    "10" => "CDicomPDUItemApplicationContext",
    "20" => "CDicomPDUItemPresentationContext",
    "30" => "CDicomPDUItemAbstractSyntax",
    "40" => "CDicomPDUItemTransferSyntax",
    "50" => "CDicomPDUItemUserInfo",
    "51" => "CDicomPDUItemMaximumLength",
    "52" => "CDicomPDUItemImplementationClassUID",
    "55" => "CDicomPDUItemImplementationVersionName"
  );
  
  /**
   * Used by the decodeItems function
   * 
   * @var string
   */
  static $next_item = null;
  
  /**
   * Get the type of the Item, and create the corresponding CDicomPDUItem
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return CDicomPDUItem The PDU item
   */
  static function decodeItem(CDicomStreamReader $stream_reader) {
    $item_type = self::getItemType($stream_reader);
    
    $item = new $item_type();
    $item->decodeItem($stream_reader);
    
    return $item;
  }
  
  /**
   * Decodes consecutive items of the given type
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @param hexadecimal        $wanted_type   The code of the wanted type
   * 
   * @return array of CDicomPDUItem The PDU items
   */
  static function decodeConsecutiveItemsByType(CDicomStreamReader $stream_reader, $wanted_type) {
    $items = array();
    $item_type = self::getItemType($stream_reader);
    
    $wanted_type = self::$item_types[$wanted_type];

    while ($item_type == $wanted_type) {
      $item = new $item_type();
      $item->decodeItem($stream_reader);
      $items[] = $item;
      
      $item_type = self::getItemType($stream_reader);
    }
    self::$next_item = $item_type;
    
    return $items;
  }
  
  /**
   * Decodes consecutive items until the given length is reached
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @param integer            $length        The code of the wanted type
   * 
   * @return array of CDicomPDUItem The PDU items
   */
  static function decodeConsecutiveItemsByLength(CDicomStreamReader $stream_reader, $length) {
    $items = array();
    
    $pos = $stream_reader->getPos();
    $endOfItem = $pos + $length;
    echo "End of Item : $endOfItem<br>";
    $item_type = self::getItemType($stream_reader);
    
    while ($item_type && $stream_reader->getPos() < $endOfItem) {
      if (!$item_type) {
        break;
      }  
      $item = new $item_type;
      
      $item->decodeItem($stream_reader);
      $items[] = $item;
      echo "Pos : {$stream_reader->getPos()}";
      $item_type = self::getItemType($stream_reader);
    }
    
    return $items;
  }
  
  /**
   * Create an item of the given type 
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @param string             $type          The type of the PDU you want to create
   * 
   * @return CDicomPDUItem The item
   */
  static function encodeItem(CDicomStreamWriter $stream_writer, $type) {
    
  }
  
  /**
   * Read the type of an item. If a item type has been read but not decoded, it returns this type.
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return string The name of the item class
   */
  static function getItemType(CDicomStreamReader $stream_reader) {
    $item_type = null;
    if (!self::$next_item) {
      $tmp = $stream_reader->readHexByte();
      if (!$tmp) {
        return false;
      }
      $item_type = self::$item_types[$tmp];//$stream_reader->readHexByte()];
    }
    else {
      $item_type = self::$next_item;
      self::$next_item = null;
    }
    return $item_type;
  }
}
?>