<?php 

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 8980 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbvCardExport {
  var $version = "2.1";
  var $elements = array();
  var $name = 'no_name';
  
  function addTitle($title){
    $this->addElement("TITLE", $title);
  }

  function addAddress($address, $city, $postal, $country, $type){
    $this->addElement("ADR;$type",";;$address;$city;;$postal;$country");
  }
  
  function addName($first_name, $last_name, $prefix){
    $this->name = str_replace(' ', '_', $first_name.'_'.$last_name);
    $this->addElement('N',$last_name.';'.$first_name.';;'.$prefix );
    $this->addElement('FN',"$prefix $first_name $last_name");
  }
  
  function addEmail($address){
    $this->addElement('EMAIL;INTERNET', $address);
  }
  
  function addPhoneNumber($number, $type){
    $this->addElement("TEL;$type", $number);
  }
  
  function addBirthDate($date){
    $this->addElement('BDAY',$date);
  }
  
  function addPicture($picture) {
    $type = str_replace('image/', '', $picture->file_type);
    $file = base64_encode($picture->getContent());
    $this->addElement("PHOTO;ENCODING=BASE64;TYPE=$type:", $file);
  }
  
  function addBegin() {
    $this->addElement("BEGIN", "VCARD");
  }
  
  function addVersion() {
    $this->addElement("VERSION", $this->version);
  }
  
  function addEnd() {
    $this->addElement("END", "VCARD");
  }

  function addElement($name, $value){
    $this->elements[$name] = $value;
  }
  
  function toString($object){
    if(!method_exists($object, "toVcard")) {
    	return false;
    }
    $this->addBegin();
    $this->addVersion();
    $object->toVcard($this);
    $this->addEnd();
    
    $o = "";
    foreach($this->elements as $key=>$value){
      $o .= $key. ':'. $value."\n";
    }
    
    return $o;
  }
   
  function saveVCard($object){
    if(!$content = $this->toString($object)) {
    	return false;
    }
    header("Content-Disposition: attachment; filename={$this->name}.vcf");
    header("Content-Type: text/x-vcard; charset=".CApp::$encoding);
    header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
    header( "Cache-Control: post-check=0, pre-check=0", false );
    header("Content-Length: ".strlen($content));
    echo $content;
  }
}
?>