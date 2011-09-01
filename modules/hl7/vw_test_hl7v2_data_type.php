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
    "mb" => array(
      "20110829" => "2011-08-29",
      "201108"   => "2011-08-00",
      "2011"     => "2011-00-00",
      "2011082"  => null,
    ),
    "hl7" => array(
      "2011-08-29" => "20110829",
      "2011-08-00" => "201108",
      "2011-00-00" => "2011",
    ),
  ),
  
  "DateTime" => array(
    "mb" => array(
      "20110829140306.0052" => "2011-08-29 14:03:06",
      "20110829140306"      => "2011-08-29 14:03:06",
      "201108291403"        => "2011-08-29 14:03:00",
      "2011082914"          => "2011-08-29 14:00:00",
      "20110829"            => "2011-08-29 00:00:00",
      "201108"              => "2011-08-00 00:00:00",
      "2011"                => "2011-00-00 00:00:00",
      "20110829140360.0052" => null,
    ),
    "hl7" => array(
      "2011-08-29 14:03:06" => "20110829140306",
      "2011-08-29T14:03:06" => "20110829140306",
      "2011-08-29 14:03:00" => "20110829140300",
    ),
  ),
  
  "Time" => array(
    "mb" => array(
      "140306.0052" => "14:03:06",
      "140306"      => "14:03:06",
      "1403"        => "14:03:00",
      "14"          => "14:00:00",
    ),
    "hl7" => array(
      "14:03:06" => "140306",
      "14:03:00" => "140300",
      "14:00:00" => "140000",
      "24:00:00" => null,
    ),
  ),
  
  "Integer" => array(
    "mb" => array(
      "16512"   => 16512,
      "16512.5" => null,
      "009"     => 9,
      "foo"     => null,
    ),
    "hl7" => array(
      "16512"   => 16512,
      "16512.5" => null,
      "009"     => 9,
      "foo"     => null,
    ),
  ),
  
  "Double" => array(
    "mb" => array(
      "16512"   => 16512.0,
      "16512.5" => 16512.5,
      "16512,5" => null,
      "009"     => 9.0,
      "foo"     => null,
    ),
    "hl7" => array(
      "16512"   => 16512.0,
      "16512.5" => 16512.5,
      "16512,5" => null,
      "009"     => 9.0,
      "foo"     => null,
    ),
  ),
);

$results = array();

foreach($test_suite as $type => $systems) {
  echo "<h1>$type</h1>";
  $dt = CHL7v2DataType::load($type, "2.5");
  
  foreach($systems as $system => $tests) {
    echo "<h2>$system</h2>";
    
    foreach($tests as $from => $to) {
      $method = ($system == "mb" ? "toMB" : "toHL7");
      $result = null;
      
      try {
        $result = $dt->$method($from);
      }
      catch(CHL7v2Exception $e) {
        $result = $e;
      }
       
      echo "<pre style='text-indent: 3em; color:".(($result === $to || $result instanceof CHL7v2Exception && $to == null) ? 'green' : 'red')."'>'$from' => ".($result instanceof CHL7v2Exception ? $result->getMessage() : var_export($result, true))." (expected ".var_export($to,true).")</pre>\n";
    }
  }
}
