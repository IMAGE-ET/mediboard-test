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

$root_dir = dirname(__FILE__)."/../../..";
require "$root_dir/modules/hl7/classes/CMLLPServer.class.php";

$tmp_dir = CMLLPServer::getTmpDir();

function outln($str){
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, $str.PHP_EOL);
}

class ER7Message {
  /**
   * @return string An ORU message formatted in ER7
   */
  final static function ORU(){
    $date = strftime("%Y%m%d%H%M%S");
    $er7 = <<<EOT
MSH|^~\&|||||||ORU^R01|HP104220879017992|P|2.3||||||8859/1
PID|1||000038^^^&&^PI~323328^^^Mediboard&1.2.250.1.2.3.4&OX^RI||TEST^Obx^^^m^^L^A||19800101|M|||^^^^^^H|||||||12000041^^^&&^AN||||||||||||N||VALI|20120116161701||||||
PV1|1|I|UF1^^^&&^O|R|12000041^^^&&^RI||929997607^FOO^Bar^^^^^^&1.2.250.1.71.4.2.1&ISO^L^^^ADELI^^^^^^^^^^|||||||90||P|929997607^FOO^Bar^^^^^^&1.2.250.1.71.4.2.1&ISO^L^^^ADELI^^^^^^^^^^||321120^^^Mediboard&1.2.250.1.2.3.4&OX^RI||AMBU|N||||||||||||||4||||||||||||||||
OBR||||Mediboard test|||$date

EOT;
    
    $obx = array();
    $obx[] = "OBX||NM|0002-4b60^Tcore^MDIL|0|".(rand(350, 400)/10)."|0004-17a0^°C^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4bb8^SpO2^MDIL|0|".  rand(80, 100).     "|0004-0220^%^MDIL|||||F";
    $obx[] = "OBX||NM|0002-5000^Resp^MDIL|0|".  rand(20, 50).      "|0004-0ae0^rpm^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4182^HR^MDIL|0|".    rand(40, 90).      "|0004-0aa0^bpm^MDIL|||||F";
    
    $obx[] = "OBX||NM|0002-4a15^ABPs^MDIL|0|".    rand(90, 160).   "|0004-0f20^mmHg^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4a16^ABPd^MDIL|0|".    rand(30, 90).    "|0004-0f20^mmHg^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4a17^ABPm^MDIL|0|".    rand(80, 100).   "|0004-0f20^mmHg^MDIL|||||F";
    
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
