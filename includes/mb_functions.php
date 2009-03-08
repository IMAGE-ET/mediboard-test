<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Utility function to return a value from a named array or a specified default
 * array should always be passed by reference
 */
function dPgetParam(&$arr, $name, $def = null) {
  return isset( $arr[$name] ) ? $arr[$name] : $def;
}


/**
 * Returns the first arguments that do not evaluate to null (0, null, "")
 * @return mixed 
 */
function mbGetValue() {
  foreach(func_get_args() as $arg) {
    if ($arg) {
      return $arg;
    }
  }
}

/**
 * Returns the value of a variable retreived it from HTTP Get, with at least a
 * default value
 * @access public
 * @return any 
 **/
function mbGetValueFromCookie($valName, $valDefault = null) {
  return isset($_COOKIE[$valName]) ? $_COOKIE[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Get, with at least a
 * default value
 * @access public
 * @return any 
 **/
function mbGetValueFromGet($valName, $valDefault = null) {
  return isset($_GET[$valName]) ? $_GET[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Post, with at least a
 * default value
 * @access public
 * @return any 
 **/
function mbGetValueFromPost($valName, $valDefault = null) {
  return isset($_POST[$valName]) ? $_POST[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Post, with at least a
 * default value
 * @access public
 * @return any 
 **/
function mbGetValueFromRequest($valName, $valDefault = null) {
  return isset($_REQUEST[$valName]) ? $_REQUEST[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Get, then from the session
 * Stores it in _SESSION[$m] in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetValueFromGetOrSession($valName, $valDefault = null) {
  global $m;

  if (isset($_GET[$valName])) {
    $_SESSION[$m][$valName] = $_GET[$valName];
  }
  
  return dPgetParam($_SESSION[$m], $valName, $valDefault);
}

/**
 * Returns the value of a variable retreived it from HTTP Get, then from the session
 * Stores it in _SESSION in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetAbsValueFromGetOrSession($valName, $valDefault = null) {
  if (isset($_GET[$valName])) {
    $_SESSION[$valName] = $_GET[$valName];
  }
  
  return dPgetParam($_SESSION, $valName, $valDefault);
}

/**
 * Returns the value of a variable retreived it from HTTP Post, then from the session
 * Stores it in _SESSION[$m] in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetValueFromPostOrSession($valName, $valDefault = null) {
  global $m;

  if (isset($_POST[$valName])) {
    $_SESSION[$m][$valName] = $_POST[$valName];
  }
  
  return dPgetParam($_SESSION[$m], $valName, $valDefault);
}

/**
 * Returns the value of a variable retreived it from HTTP Post, then from the session
 * Stores it in _SESSION[$m] in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetAbsValueFromPostOrSession($key, $default = null) {
  if (isset($_POST[$key])) {
    $_SESSION[$key] = $_POST[$key];
  }
  return dPgetParam($_SESSION, $key, $default);
}

/**
 * Returns the value of a variable retreived it from the session
 * @access public
 * @return any 
 **/
function mbGetValueFromSession($key, $default = null) {
  global $m;
  return dPgetParam($_SESSION[$m], $key, $default);
}

/**
 * Returns the MbObject with given GET params keys
 * @param string $class_key
 * @param string $id_key
 * @param string $guid_key
 * @return CMbObject
 **/
function mbGetObjectFromGet($class_key, $id_key, $guid_key = null) {
 	$object_class = mbGetValueFromGet($class_key);
	$object_id    = mbGetValueFromGet($id_key);
	$object_guid  = "$object_class-$object_id";

	if ($guid_key) {
	  $object_guid = mbGetValueFromGet($guid_key, $object_guid);
	}

	$object = CMbObject::loadFromGuid($object_guid);

  // Redirection
  if (!$object || !$object->_id) {
	  global $AppUI, $ajax;
	  $AppUI->redirect("ajax=$ajax&suppressHeaders=1&m=system&a=object_not_found&object_guid=$object_guid");
	}
	
	return $object;
}


/**
 * Sets a value to the session[$m]. Very useful to nullify object ids after deletion
 * @access public
 * @return void
 **/
function mbSetValueToSession($key, $value = null) {
  global $m;
  $_SESSION[$m][$key] = $value;
}

/**
 * Sets a value to the session. Very useful to nullify object ids after deletion
 * @access public
 * @return void
 **/
function mbSetAbsValueToSession($key, $value = null) {
  $_SESSION[$key] = $value;
}

/**
 * Calculate the bank holidays in France
 * @return array: List of bank holidays
 **/

function mbBankHolidays($date = null) {
  if(!$date)
    $date = mbDate();
  $year = mbTransformTime("+0 DAY", $date, "%Y");

  // Calcul du Dimanche de Pâques : http://fr.wikipedia.org/wiki/Computus
  $n = $year - 1900;
  $a = $n % 19;
  $b = intval((7 * $a + 1) / 19);
  $c = ((11 * $a) - $b + 4) % 29;
  $d = intval($n / 4);
  $e = ($n - $c + $d + 31) % 7;
  $P = 25 - $c - $e;
  if($P > 0) {
    $P = "+".$P;
  }
  $paques = mbDate("$P DAYS", "$year-03-31");

  $freeDays = array(
    "$year-01-01",               // Jour de l'an
    mbDate("+1 DAY", $paques),   // Lundi de paques
    "$year-05-01",               // Fête du travail
    "$year-05-08",               // Victoire de 1945
    mbDate("+40 DAYS", $paques), // Jeudi de l'ascension
    mbDate("+50 DAYS", $paques), // Lundi de pentecôte
    "$year-07-14",               // Fête nationnale
    "$year-08-15",               // Assomption
    "$year-11-01",               // Toussaint
    "$year-11-11",               // Armistice 1918
    "$year-12-25"                // Noël
  );
  
  return $freeDays;
}

/**
 * Calculate the number of work days in the given month date
 * @return int: number of work days 
 **/

function mbWorkDaysInMonth($date = null) {
  $result = 0;
  if(!$date)
    $date = mbDate();
  $year = mbTransformTime("+0 DAY", $date, "%Y");
  $debut = $date;
  $rectif = mbTransformTime("+0 DAY", $debut, "%d")-1;
  $debut = mbDate("-$rectif DAYS", $debut);
  $fin   = $date;
  $rectif = mbTransformTime("+0 DAY", $fin, "%d")-1;
  $fin = mbDate("-$rectif DAYS", $fin);
  $fin = mbDate("+ 1 MONTH", $fin);
  $fin = mbDate("-1 DAY", $fin);
  $freeDays = mbBankHolidays($date);
  for($i = $debut; $i <= $fin; $i = mbDate("+1 DAY", $i)) {
    $day = mbTransformTime("+0 DAY", $i, "%u");
    if($day == 6 and !in_array($i, $freeDays))
      $result += 0.5;
    elseif($day != 7 and !in_array($i, $freeDays))
      $result += 1;
  }
  return $result;
}

/**
 * Transforms absolute or relative time into a given format
 * @return string: the transformed time 
 **/
function mbTransformTime($relative = null, $ref = null, $format) {
  if ($relative === "last sunday") {
    $relative .= " 12:00:00";
  }
  
  $timestamp = $ref ? strtotime($ref) : time();
  if ($relative) {
    /*
    global $AppUI;
    if($AppUI->user_id == 15) {
      trigger_error($relative, "relative", true);
    }
    */
  	$timestamp = strtotime($relative, $timestamp);
  } 
  return strftime($format, $timestamp);
}

/**
 * Transforms absolute or relative time into DB friendly DATETIME format
 * @param string $relative Modifies the time (eg '+1 DAY')
 * @param datetime The reference date time fo transforms
 * @return string: the transformed time 
 **/
function mbDateTime($relative = null, $ref = null) {
  return mbTransformTime($relative, $ref, "%Y-%m-%d %H:%M:%S");
}

/**
 * Converts an xs;duration XML duration into a DB friendly DATETIME
 * @param string $duration where format is P1Y2M3DT10H30M0S
 * @return string: the DATETIME, null if failed
 **/
function mbDateTimeFromXMLDuration($duration) {
  $regexp = "/P((\d+)Y)?((\d+)M)?((\d+)D)?T((\d+)H)?((\d+)M)?((\d+)S)?/";
  if (!preg_match($regexp, $duration, $matches)) {
    return null;
  }
  
  return sprintf("%d-%d-%d %d:%d:%d", 
    $matches[2], $matches[4], $matches[6], 
    $matches[8], $matches[10], $matches[12]);
}


/**
 * Transforms absolute or relative time into DB friendly DATE format
 * @return string: the transformed time 
 **/
function mbDate($relative = null, $ref = null) {
  return mbTransformTime($relative, $ref, "%Y-%m-%d");
}

/**
 * Transforms absolute or relative time into DB friendly TIME format
 * @return string: the transformed time 
 **/
function mbTime($relative = null, $ref = null) {
  return mbTransformTime($relative, $ref, "%H:%M:%S");
}

/**
 * Counts the number of intervals between reference and relative
 * @return int: number of intervals
 **/
function mbTimeCountIntervals($reference, $relative, $interval) {
	$zero = strtotime("0:00:00");
  $refStamp = strtotime($reference) - $zero;
  $relStamp = strtotime($relative ) - $zero;
  $intStamp = strtotime($interval ) - $zero;
  $diffStamp = $relStamp - $refStamp;
  $nbInterval = floatval($diffStamp / $intStamp);
  return intval($nbInterval);
  
}

function mbTimeGetNearestMinsWithInterval($reference, $mins_interval) {
  $min_reference = mbTransformTime(null, $reference, "%M");
  $div = intval($min_reference / $mins_interval);
  $borne_inf = $mins_interval * $div;
  $borne_sup = $mins_interval * ($div + 1);
  $mins_replace = ($min_reference - $borne_inf) < ($borne_sup - $min_reference) ? $borne_inf : $borne_sup;
  if($mins_replace == 60) {
    $reference = sprintf('%02d:00:00',   mbTransformTime(null, $reference, "%H")+1);
  } else {
    $reference = sprintf('%02d:%02d:00', mbTransformTime(null, $reference, "%H"), $mins_replace);
  }
  return $reference;
}

/**
 * Adds a relative time to a reference time
 * @return string: the resulting time */
function mbAddTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours   = isset($fragments[0]) ? $fragments[0] : '00';
  $minutes = isset($fragments[1]) ? $fragments[1] : '00';
  $seconds = isset($fragments[2]) ? $fragments[2] : '00';
  return mbTime("+$hours hours $minutes minutes $seconds seconds", $ref);
}

/**
 * Substract a relative time to a reference time
 * @return string: the resulting time */
function mbSubTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours   = isset($fragments[0]) ? $fragments[0] : '00';
  $minutes = isset($fragments[1]) ? $fragments[1] : '00';
  $seconds = isset($fragments[2]) ? $fragments[2] : '00';
  return mbTime("-$hours hours -$minutes minutes -$seconds seconds", $ref);
}

/**
 * Adds a relative time to a reference datetime
 * @return string: the resulting time */
function mbAddDateTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours   = isset($fragments[0]) ? $fragments[0] : '00';
  $minutes = isset($fragments[1]) ? $fragments[1] : '00';
  $seconds = isset($fragments[2]) ? $fragments[2] : '00';
  return mbDateTime("+$hours hours $minutes minutes $seconds seconds", $ref);
}

/**
 * Returns the difference between two dates in days
 * @return int: number of days
 **/
function mbDaysRelative($from, $to) {
  if (!$from || !$to) {
    return null;
  }
  $from = intval(strtotime($from) / 86400);
  $to   = intval(strtotime($to  ) / 86400);
  $days = intval($to - $from);
  return $days;
}

/**
 * Returns the time difference between two times in hours
 * @return string hh:mm:ss diff duration
 **/
function mbTimeRelative($from, $to, $format = '%02d:%02d:%02d') {
  $diff = strtotime($to) - strtotime($from); 
  $hours = intval($diff / 3600);
  $mins = intval(($diff % 3600) / 60);
  $secs = intval($diff % 60);
  return sprintf($format, $hours, $mins, $secs);
}

function mbHoursRelative($from, $to) {
  if (!$from || !$to) {
    return null;
  }
  $hours = (strtotime($to) / 3600) - (strtotime($from) / 3600);
  return $hours;
}

function mbMinutesRelative($from, $to) {
  if (!$from || !$to) {
    return null;
  }
  $from = intval(strtotime($from) / 60);
  $to   = intval(strtotime($to  ) / 60);
  $minutes = intval($to - $from);
  return $minutes;
}

/**
 * Date utility class
 */
class CMbDate {
  static $secs_per = array (
    "year"   => 31536000, // 60 * 60 * 24 * 365
    "month"  =>  2592000, // 60 * 60 * 24 * 30
    "week"   =>   604800, // 60 * 60 * 24 * 7
    "day"    =>    86400, // 60 * 60 * 24
    "hour"   =>     3600, // 60 * 60
    "minute" =>       60, // 60 
    "second" =>        1, // 1 
   );
    
  /**
   * Compute user friendly approximative duration between two date time
   * @param datetime $from Starting time
   * @param datetime $to Ending time, now if null
   * @param int $min_count The minimum count to reach the upper unit, 2 if undefined
   * @return array("unit" => string, "count" => int)
   */
  static function relative($from, $to = null, $min_count = 2) {
	  if (!$from) {
	    return null;
	  }
    
	  if (!$to) {
	    $to = mbDateTime();
	  }
	  
	  // Compute diff in seconds
	  $diff = strtotime($to) - strtotime($from);
	  
	  // Find the best unit
	  foreach (self::$secs_per as $unit => $secs) {
	    if (abs($diff / $secs) > $min_count) {
	    	break;
	    }
	  }

	  return array (
	    "unit" => $unit, 
	    "count" => intval($diff / $secs)
	  );
  }
}

/**
 * Return the std variance of an array
 * @return float: ecart-type
 **/

function mbMoyenne($array) {
  if (is_array($array)) {
    $moyenne = array_sum($array) / count($array);
    return $moyenne;
  } else {
    return false;
  }
}

/**
 * Return the std variance of an array
 * @return float: ecart-type
 **/

function mbEcartType($array) {
  if (is_array($array)) {
    $moyenne = mbMoyenne($array);
    $sigma = 0;
    foreach($array as $value) {
      $sigma += pow((floatval($value)-$moyenne),2);
    }
    $ecarttype = sqrt($sigma / count($array));
    return $ecarttype;
  } else {
    return false;
  }
}

/**
 * Remove accents from a string
 **/

function removeAccent($string) {
  $string = htmlentities($string);
  $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml|grave);/", "$1", $string);
  $string = html_entity_decode($string);
  return $string;
}

/**
 * Inserts a CSV file into a mysql table 
 * Not a generic function : used for import of specials files
 * in dPinterop
 * @todo : become a generic function
 **/

function mbInsertCSV( $fileName, $tableName, $oldid = false ) {
    $ds = CSQLDataSource::get("std");
    $file = fopen( $fileName, 'rw' );
    if(! $file) {
      echo "Fichier non trouvé<br>";
      return;
    }
    $k = 0;
    $reussite = 0;
    $echec = 0;
    $null = 0;
    
    //$contents = fread ($file, filesize ($fileName));
    //$content = str_replace(chr(10), " ", $content);
  
    while (! feof($file)){
        $k++;
        $line = str_replace("NULL", "\"NULL\"", fgets( $file, 1024));
        $size = strlen($line)-3;
        $test1 = $line[$size] != "\"";
        if(($fileName != "modules/dPinterop/doc_recus.txt") && ($fileName != "modules/dPinterop/chemin.txt"))
          $test2 = $line[$size-1] == "\\";
        else
          $test2 = 0;
        $test3 = (! feof( $file ));
        $test = ($test1 || (!$test1 && $test2)) && $test3;
        while($test) {
          $line .= str_replace("NULL", "\"NULL\"", fgets( $file, 1024));
          $size = strlen($line)-3;
          $test1 = $line[$size] != "\"";
          if($fileName != "modules/dPinterop/doc_recus.txt")
            $test2 = $line[$size-1] == "\\";
          else
            $test2 = 0;
          $test3 = (! feof( $file ));
          $test = ($test1 || (!$test1 && $test2)) && $test3;
        }

        if ( strlen( $line ) > 2 )
        {
            $line = addslashes( $line );
            $line = str_replace("\\\";\\\"", "', '", $line);
            $line = str_replace("\\\"", "", $line);
            $line = str_replace("'NULL'", "NULL", $line);
            if($oldid)
              $requete = 'INSERT INTO '.$tableName.' VALUES ( \''.$line.'\', \'\' ) ';
            else
              $requete = 'INSERT INTO '.$tableName.' VALUES ( \''.$line.'\' ) ';
            if ( ! $ds->exec ( $requete ) ) {
                echo 'Erreur Ligne '.$k.' : '.mysql_error().'<br>'.$requete.'<br>';
                $echec++;
            }  else {
                //echo 'Ligne '.$k.' valide.<br>'.$requete.'<br>';
                $reussite++;
            }
        } else {
            //echo 'Ligne '.$k.' ignorée.<br>';
            $null++;
        }
    }

    echo '<p>Insertion du fichier '.$fileName.' terminé.</p>';
    echo '<p>'.$k.' lignes trouvées, '.$reussite.' enregistrées, ';
    echo $echec.' non conformes, '.$null.' ignorées.</p><hr>';

    fclose( $file );
}

/** Commentaires conditionnels pour IE :
<!--[if IE]>Si IE<![endif]-->
<!--[if gte IE 5]> pour réserver le contenu à IE 5.0 et version plus récentes (actuellement E5.5, IE6.0 et IE7.0) <![endif]-->
<!--[if IE 5.0]> pour IE 5.0 <![endif]-->
<!--[if IE 5.5000]> pour IE 5.5 <![endif]-->
<!--[if IE 6]> pour IE 6.0 <![endif]-->
<!--[if gte IE 5.5000]> pour IE5.5 et supérieur <![endif]-->
<!--[if lt IE 6]> pour IE5.0 et IE5.5 <![endif]-->
<!--[if lt IE 7]> pour IE inférieur à IE7 <![endif]-->
<!--[if lte IE 6]> pour IE5.0, IE5.5 et IE6.0 mais pas IE7.0<![endif]-->
 */

/**
 * Loads a javascript with build version postfix to prevent nasty cache effects
 * while updating the system.
 */
function mbLoadScript($filepath, $modeReturn = 0, $conditionnalComments = '') {
  global $version;
  $build = $version["build"];
  $tag = "\n<script type='text/javascript' src='$filepath?build=$build'></script>";
  if ($conditionnalComments) {
    $tag = "\n<!--[if $conditionnalComments]>$tag\n<![endif]-->";
  }
  if ($modeReturn) {
    return $tag;
  }
}

/**
 * Links a style sheet with build version postfix to prevent nasty cache effects
 * Only to be called while in the HTML header.  */
function mbLinkStylesheet($filepath, $media = "all", $modeReturn = 0) {
  global $version;
  $build = $version["build"];
  $tag = "\n<link rel='stylesheet' type='text/css' href='$filepath?build=$build' media='$media' />";
  if ($modeReturn) { 
    return $tag;
  }
  
  echo $tag;
}

/**
 * Links a shotcut icon version postfix to prevent nasty cache effects 
 * Only to be called while in the HTML header.  */
function mbLinkShortcutIcon($filepath, $modeReturn = 0) {
  global $version;
  $build = $version["build"];
  $tag = "\n<link rel='shortcut icon' type='image/ico' href='$filepath?build=$build' />";
  if ($modeReturn) { 
    return $tag;
  }
  
  echo $tag;
}

/**
 * URL to the mediboard.org documentation page 
 * @return string: the link to mediboard.org  */
function mbPortalURL( $page="Accueil") {
  $url = "http://www.mediboard.org/public/";
  
  $url .= $page == "tracker" ?
    "/tiki-view_tracker.php?trackerId=4" :
    "tiki-index.php?page=$page";
    
  return $url;
}
/**
 * Loads all scripts
 */
function mbLoadScripts($modeReturn = 0) {
	$affichageScript = null;
    
  $affichageScript .= mbLoadScript("lib/jscalendar/calendar.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/jscalendar/lang/calendar-fr.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/jscalendar/calendar-setup.js",$modeReturn);
  
  $affichageScript .= mbLoadScript("lib/scriptaculous/lib/prototype.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/scriptaculous/src/scriptaculous.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/control_suite/control.tabs.js",$modeReturn);
  
  // Flotr
  $affichageScript .= mbLoadScript("lib/flotr/flotr.js", $modeReturn);
  $affichageScript .= mbLoadScript("lib/flotr/lib/excanvas.js", $modeReturn, 'IE'); // for IE
  $affichageScript .= mbLoadScript("lib/flotr/lib/base64.js", $modeReturn);
  $affichageScript .= mbLoadScript("lib/flotr/lib/canvas2image.js", $modeReturn);
  $affichageScript .= mbLoadScript("lib/flotr/lib/canvastext.js", $modeReturn);
  
  $affichageScript .= mbLoadScript("includes/javascript/prototypex.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/functions.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/tooltip.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/controls.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/cookies.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/url.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/forms.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/checkForms.js",$modeReturn);
  
  $affichageScript .= mbLoadScript("includes/javascript/printf.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/window.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/mbmail.js",$modeReturn);
  
  if (CAppUI::conf('debug')) {
    //$affichageScript .= mbLoadScript("http://getfirebug.com/releases/lite/1.2.1/firebug-lite-compressed.js", $modeReturn, 'IE');
  }
  
  if($modeReturn)
    return $affichageScript;
}

function mbLoadScriptsStorage($modeReturn){
  $affichageScript = null;
  
  $affichageScript .= mbLoadScript("lib/dojo/dojo.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/dojo/src/io/__package__.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/dojo/src/html/__package__.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/dojo/src/lfx/__package__.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/storage.js",$modeReturn);
  if($modeReturn)
    return $affichageScript;
}

/**
 * Converts an bytes number to the deca-binary equivalent
 * @return string Mediboard version */
function mbConvertDecaBinary($number) {
  $bytes = $number;
  $value = $number;
  $prefix = "";
  $unit = "o";

  $kbytes = $bytes / 1024;
  if ($kbytes >= 1) {
    $value = $kbytes;
    $prefix = "K";
  }

  $mbytes = $kbytes / 1024;
  if ($mbytes >= 1) {
    $value = $mbytes;
    $prefix = "M";
  }

  $gbytes = $mbytes / 1024;
  if ($gbytes >= 1) {
    $value = $gbytes;
    $prefix = "G";
  }
  
  // Value with 3 significant digits, thent the unit
  $value = round($value, $value > 99 ? 0 : $value >  9 ? 1 : 2);
  return "$value $prefix$unit";
}

/** Used to get bytes from a string like "64M" */
function toBytes($val) {
  $val = trim($val);
  $last = strtolower($val[strlen($val)-1]);
  switch($last) {
    case 't': $val *= 1024;     
    case 'g': $val *= 1024;
    case 'm': $val *= 1024;
    case 'k': $val *= 1024;
  }
  return $val;
}

/** Must be like "64M" */ 
function set_min_memory_limit($min) {
  $actual = toBytes(ini_get('memory_limit'));
  $new    = toBytes($min);
  if ($new > $actual) {
    ini_set('memory_limit', $min);
  }
}

function getChildClasses($parent = "CMbObject", $properties = array()) {
  $listClasses = get_declared_classes();
  foreach ($listClasses as $key => $class) {
    if ($parent and !is_subclass_of($class, $parent)) {
      unset($listClasses[$key]);
      continue;
    }

    foreach($properties as $prop) {
      if(!array_key_exists($prop, get_class_vars($class))) {
        unset($listClasses[$key]);
      }
    }
  }
  sort($listClasses);
  return $listClasses;
}

/**
 * DEPRECATED SEE getMbClassesEx();
 *
 * @param array $properties
 * @return array
 */
function getMbClasses($properties = array()) {
  global $AppUI;
  $AppUI->getAllClasses();
  $classes = getChildClasses("CMbObject", $properties);
  foreach ($classes as $key => $class) {
    // La classes est-elle instanciable ?
    if (!has_default_constructor($class)){
      unset($classes[$key]);
    	continue;
    }

    // Instanciation escapée au cas où cela génère des erreurs liées au DSN
    $object = @new $class;
    
    // On test si on a réussi à l'instancier
    if (!$object->_class_name) {
      unset($classes[$key]);
    	continue;
    }
    
    // On teste si son dns est standard
    if ($object->_spec->dsn != "std") {
      unset($classes[$key]);
    	continue;
    }
  }
  
  return $classes;
}


/**
 * Returns all CMbObject child classes
 *
 * @param array $properties
 * @return array
 */
function getMbClassesEx($properties = array()) {
  global $AppUI;
  $AppUI->getAllClasses();
  $classes = getChildClasses("CMbObject", $properties);
  foreach ($classes as $key => $class) {
    // Instanciation escapée au cas où cela génère des erreurs liées au DSN
    $object = @new $class;
    
    // On test si on a réussi à l'instancier
    if (!$object->_class_name) {
      unset($classes[$key]);
    	continue;
    }
  }
  
  return $classes;
}


function getInstalledClasses($properties = array()) {
  $classes = getMbClassesEx();
  foreach ($classes as $key => $class) {
    // Instanciation escapée au cas où cela génère des erreurs liées au DSN
    $object = @new $class;
    
    // On test si l'objet a bin un module de référence
    if ($object->_ref_module === null) {
      unset($classes[$key]);
    }
  }
  
  return $classes;
}

/**
 * Check if a class has a default constructor (ie with 0 paramater)
 * @return bool
 */
function has_default_constructor($classname) {
	$class = new ReflectionClass($classname);
	$constructor = $class->getConstructor();
	return ($constructor->getNumberOfParameters() == 0);
}

/**
 * Strip slashes recursively if value is an array
 * @param mixed $value
 * @param return stripped value
 **/
function stripslashes_deep($value) {
  return is_array($value) ?
    array_map("stripslashes_deep", $value) :
    stripslashes($value);
}

/**
 * Copy the hash array content into the object as properties
 * only existing properties of object are filled, when defined in hash
 * @param array $hash the input array
 * @param object $object to fill of any class
 **/
function bindHashToObject($hash, &$object) {

  // @TODO use property_exists() which is a bit faster
  // BUT requires PHP >= 5.1   
  
  $vars = get_object_vars($object);
  foreach ($hash as $k => $v) {
    if (array_key_exists($k, $vars)) {
      $object->$k = $hash[$k];
    }
  } 
}

/**
 * Convert a date from ISO to locale format
 * @param string $date Date in ISO format
 * @return string Date in locale format
 */
function mbDateToLocale($date) {
  return preg_replace("/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $date);
}


/**
 * Convert a date from locale to ISO format
 * @param string $date Date in locale format
 * @return string Date in ISO format
 */
function mbDateFromLocale($date) {
  return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3-$2-$1", $date);
}

/**
 * Check if given value is in given range or equals to limit
 * @param $value mixed The value to check
 * @param $min mixed The lesser limit
 * @param $max mixed The upper limit
 * @return bool 
 */
function in_range($value, $min, $max) {
  return $value <= $max && $value >= $min;
}

/**
 * Check if a number is a valid Luhn number
 * see http://en.wikipedia.org/wiki/Luhn
 * @param code string String representing a potential Luhn number
 * @return bool
 */
function luhn ($code) {
  $code = preg_replace('/\D|\s/', '', $code);
  $code_length = strlen($code);
  $sum = 0;
  
  $parity = $code_length % 2;
  
  for ($i = $code_length - 1; $i >= 0; $i--) {
    $digit = $code{$i};
    
    if ($i % 2 == $parity) {
      $digit *= 2;
      
      if ($digit > 9) {
        $digit -= 9;
      }
    }
    
    $sum += $digit;
  }
  
  return (($sum % 10) == 0);
}

/**
 * Check wether a URL exists (200 HTTP Header)
 * @param string $url URL to check
 * @return bool
 */
function url_exists($url) {
  $headers = @get_headers($url);
  return (preg_match("|200|", $headers[0])); 
}

/**
 * Build a url string based on components in an array
 * @param array $components Components, as of parse_url (see PHP documentation)
 * @return string
 */
function make_url($components) {
  $url = $components["scheme"] . "://";

  if (isset($components["user"])) {
    $url .= $components["user"] . ":" . $components["pass"] . "@";
  }
  
  $url .=  $components["host"];
  
  if (isset($components["port"])) {
    $url .=  ":" . $components["port"];
  }
  
  $url .=  $components["path"];
  
  if (isset($components["query"])) {
    $url .=  "?" . $components["query"];
  }
  
  if (isset($components["fragment"])) {
    $url .=  "#" . $components["fragment"];
  }
  
  return $url;
}

/**
 * Check wether a IP address is in intranet-like form
 * @param string $ip IP address to check
 * @return bool
 */
function is_intranet_ip($ip) {
  /*// Optimized version, must be tested
  
  $ip = explode('.', $ip);
  return 
    ($ip[0] == 127) ||
    ($ip[0] == 10)  ||
    ($ip[0] == 172 && $ip[1] >= 16 && $ip[1] < 32) ||
    ($ip[0] == 192 && $ip[1] == 168);*/
    
  $browserIP = explode(".", $ip);
  $ip0 = intval($browserIP[0]);
  $ip1 = intval($browserIP[1]);
  $ip2 = intval($browserIP[2]);
  $ip3 = intval($browserIP[3]);
  $is_local[1] = ($ip0 == 127 && $ip1 == 0 && $ip2 == 0 && $ip3 == 1);
  $is_local[2] = ($ip0 == 10);
  $is_local[3] = ($ip0 == 172 && $ip1 >= 16 && $ip1 < 32);
  $is_local[4] = ($ip0 == 192 && $ip1 == 168);
  return $is_local[1] || $is_local[2] || $is_local[3] || $is_local[4];
}
/**
 * Checks recursively if a value exists in an array
 * @return Returns TRUE if needle  is found in the array, FALSE otherwise. 
 * @param mixed $needle The searched value.
 * @param array $haystack The array.
 * @param bool $strict If the third parameter strict is set to TRUE then the in_array_recursive() function will also check the types of the needle in the haystack.
 */
function in_array_recursive($needle, $haystack, $strict = false) {
  if (in_array($needle, $haystack, $strict)) return true;
  foreach ($haystack as $v) {
    if (is_array($v) && in_array_recursive($needle, $v, $strict)) return true;
  }
  return false;
}

?>