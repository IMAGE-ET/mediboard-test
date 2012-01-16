<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// CLI or die
PHP_SAPI === "cli" or die;

global $root_dir, $tmp_dir;

$root_dir = dirname(__FILE__)."/../../..";
require "$root_dir/classes/CMbPath.class.php";
require "$root_dir/lib/phpsocket/SocketClient.php";
require "$root_dir/modules/hl7/classes/CMLLPSocketHandler.class.php";

$tmp_dir = "$root_dir/tmp/socket_server";
CMbPath::forceDir($tmp_dir);

function outln($str){
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, $str.PHP_EOL);
}

class ER7Message {
  /**
   * @return string An ORU message formatted in ER7
   */
  final static function ORU(){
    $er7 = <<<EOT
MSH|^~\&||GA0000||VAERS PROCESSOR|20010331||ORU^R01|20010422GA03|T|2.3.1|||AL|
PID|||1234^^^^SR~1234-12^^^^LR~00725^^^^MR||Doe^John^Fitzgerald^JR^^^L||20001007|M||2106-3^White^HL70005|123 Peachtree St^APT 3B^Atlanta^GA^30210^^M^^GA067||(678) 555-1212^^PRN|
NK1|1|Jones^Jane^Lee^^RN|VAB^Vaccine administered by (Name)^HL70063|
NK1|2|Jones^Jane^Lee^^RN|FVP^Form completed by (Name)-Vaccine provider^HL70063|101 Main Street^^Atlanta^GA^38765^^O^^GA121||(404) 554-9097^^WPN|
ORC|CN|||||||||||1234567^Welby^Marcus^J^Jr^Dr.^MD^L|||||||||Peachtree Clinic|101 Main Street^^Atlanta^GA^38765^^O^^GA121|(404) 554-9097^^WPN|101 Main Street^^Atlanta^GA^38765^^O^^GA121|
OBR|1|||^Mediboard test|||20010316|

EOT;
    
    $obx = array();
    
    // Tcore
    $obx[] = "OBX|1|NM|0002-4b60^Température corporelle^MDIL||".(rand(350, 400)/10)."|0004-17a0^°C^MDIL|";
    
    // HR
    $obx[] = "OBX|1|NM|0002-4182^Rythme cardiaque^MDIL||".rand(40, 90)."|0004-0aa0^bpm^MDIL|";
    
    // 
    //$obx[] = "OBX|1|NM|0002-4182^Rythme cardiaque^MDIL||".rand(40, 90)."|0004-0aa0^bpm^MDIL|";
    
    $er7 .= implode("\n", $obx);
    
    return $er7;
  }

  /**
   * @return array The list of available test messages
   */
  static function getList(){
    $reflection = new ReflectionClass('ER7Message');
    $list = $reflection->getMethods(ReflectionMethod::IS_FINAL);
    
    $types = array();
    foreach($list as $_method) {
      $types[] = $_method->name;
    }
    
    return $types;
  }
}
