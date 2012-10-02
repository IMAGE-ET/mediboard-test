<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$test_suite = array(
  "Date" => array(
    "MB" => array(
      "20110829" => "2011-08-29",
      "201108"   => "2011-08-00",
      "2011"     => "2011-00-00",
      "2011082"  => null,
    ),
    "HL7" => array(
      "2011-08-29" => "20110829",
      "2011-08-00" => "201108",
      "2011-00-00" => "2011",
    ),
  ),
  
  "DateTime" => array(
    "MB" => array(
      "20110829140306.0052" => "2011-08-29 14:03:06",
      "20110829140306"      => "2011-08-29 14:03:06",
      "201108291403"        => "2011-08-29 14:03:00",
      "2011082914"          => "2011-08-29 14:00:00",
      "20110829"            => "2011-08-29 00:00:00",
      "201108"              => "2011-08-00 00:00:00",
      "2011"                => "2011-00-00 00:00:00",
      "20110829140360.0052" => null,
    ),
    "HL7" => array(
      "2011-08-29 14:03:06" => "20110829140306",
      "2011-08-29T14:03:06" => "20110829140306",
      "2011-08-29 14:03:00" => "20110829140300",
    ),
  ),
  
  "Time" => array(
    "MB" => array(
      "140306.0052" => "14:03:06",
      "140306"      => "14:03:06",
      "1403"        => "14:03:00",
      "14"          => "14:00:00",
    ),
    "HL7" => array(
      "14:03:06" => "140306",
      "14:03:00" => "140300",
      "14:00:00" => "140000",
      "24:00:00" => null,
    ),
  ),
  
  "Integer" => array(
    "MB" => array(
      "16512"   => 16512,
      "16512.5" => null,
      "009"     => 9,
      "foo"     => null,
    ),
    "HL7" => array(
      "16512"   => 16512,
      "16512.5" => null,
      "009"     => 9,
      "foo"     => null,
    ),
  ),
  
  "Double" => array(
    "MB" => array(
      "16512"   => 16512.0,
      "16512.5" => 16512.5,
      "16512,5" => null,
      "009"     => 9.0,
      "foo"     => null,
    ),
    "HL7" => array(
      "16512"   => 16512.0,
      "16512.5" => 16512.5,
      "16512,5" => null,
      "009"     => 9.0,
      "foo"     => null,
    ),
  ),
);

$results = array();
$dummy_message = new CHL7v2Message;
$dummy_segment = new CHL7v2Segment($dummy_message);
$dummy_field = new CHL7v2Field($dummy_segment, new CHL7v2SimpleXMLElement('<?xml version="1.0" ?><root/>'));

foreach($test_suite as $type => $systems) {
  echo "<h1>$type</h1>";
  $dt = CHL7v2DataType::load($type, "2.5", "none");
  
  foreach($systems as $system => $tests) {
    echo "<h2>vers $system</h2>";
    
    foreach($tests as $from => $to) {
      $method = ($system == "MB" ? "toMB" : "toHL7");
      $result = null;
      
      try {
        $result = $dt->$method($from, $dummy_field);
      }
      catch(Exception $e) {
        $result = $e;
      }
       
      echo "<pre style='text-indent: 3em; color:".(($result === $to || $result instanceof Exception && $to == null) ? 'green' : 'red')."'>'$from' => ".($result instanceof Exception ? $result->getMessage() : var_export($result, true))." (expected ".var_export($to,true).")</pre>\n";
    }
  }
}

function pre($str) {
  return "<pre>$str</pre>";
}

$message = new CHL7v2Message;
$message->initEscapeSequences();

$escaped = "debut \\F\\ \\S\\ \\T\\ \\E\\ \\R\\ fin";
$unescaped = $message->unescape($escaped);

mbExport($escaped, "escaped");
mbExport($unescaped, "escaped unescaped");
mbExport($message->escape($unescaped), "escaped unescaped escaped");

$unicode = "coeur unicode \\M2764\\";
echo pre($unicode);
echo pre($message->unescape($unicode));

$format = "test \H\I'm strong\N\ test \.br\ new line";
echo pre($format);
echo pre($message->format($format));

