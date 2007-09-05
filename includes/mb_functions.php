<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision$
 * @author Thomas Despoix
 */

/**
 * Utility function to return a value from a named array or a specified default
 */
function dPgetParam( &$arr, $name, $def=null ) {
  return isset( $arr[$name] ) ? $arr[$name] : $def;
}


/**
 * Returns the first arguments that do not evaluate to false (0, null, "")
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
function mbGetAbsValueFromPostOrSession($valName, $valDefault = null) {

  if (isset($_POST[$valName])) {
    $_SESSION[$valName] = $_POST[$valName];
  }
  
  return dPgetParam($_SESSION, $valName, $valDefault);
}

/**
 * Sets a value to the session[$m]. Very useful to nullify object ids after deletion
 * @access public
 * @return void
 **/
function mbSetValueToSession($valName, $value = null) {
  global $m;

  $_SESSION[$m][$valName] = $value;
}

/**
 * Sets a value to the session. Very useful to nullify object ids after deletion
 * @access public
 * @return void
 **/
function mbSetAbsValueToSession($valName, $value = null) {

  $_SESSION[$valName] = $value;
}

/**
 * Calculate the bank holidays in France
 * @return array: List of bank holidays
 **/

function mbBankHolidays($date = null) {
  if(!$date)
    $date = mbDate();
  $year = mbTranformTime("+0 DAY", $date, "%Y");

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
  $year = mbTranformTime("+0 DAY", $date, "%Y");
  $debut = $date;
  $rectif = mbTranformTime("+0 DAY", $debut, "%d")-1;
  $debut = mbDate("-$rectif DAYS", $debut);
  $fin   = $date;
  $rectif = mbTranformTime("+0 DAY", $fin, "%d")-1;
  $fin = mbDate("-$rectif DAYS", $fin);
  $fin = mbDate("+ 1 MONTH", $fin);
  $fin = mbDate("-1 DAY", $fin);
  $freeDays = mbBankHolidays($date);
  for($i = $debut; $i <= $fin; $i = mbDate("+1 DAY", $i)) {
    $day = mbTranformTime("+0 DAY", $i, "%u");
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
function mbTranformTime($relative = null, $ref = null, $format) {
  if ($relative == "last sunday") {
    $relative .= " 12:00:00";
  }
  
  $timestamp = $ref ? strtotime($ref) : time();
  if ($relative) {
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
  return mbTranformTime($relative, $ref, "%Y-%m-%d %H:%M:%S");
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
  return mbTranformTime($relative, $ref, "%Y-%m-%d");
}

/**
 * Transforms absolute or relative time into DB friendly TIME format
 * @return string: the transformed time 
 **/
function mbTime($relative = null, $ref = null) {
  return mbTranformTime($relative, $ref, "%H:%M:%S");
}

/**
 * Counts the number of intervals between reference and relative
 * @return int: number of intervals
 **/
function mbTimeCountIntervals($reference, $relative, $interval) {
  $refStamp = strtotime($reference) - strtotime("0:00:00");
  $relStamp = strtotime($relative ) - strtotime("0:00:00");
  $intStamp = strtotime($interval ) - strtotime("0:00:00");
  $diffStamp = $relStamp - $refStamp;
  $nbInterval = floatval($diffStamp / $intStamp);
  return intval($nbInterval);
  
}

/**
 * Adds a relative time to a reference time
 * @return string: the resulting time */
function mbAddTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours = @$fragments[0];
  $minutes = @$fragments[1];
  $seconds = @$fragments[2];
  return mbTime("+$hours hours $minutes minutes $seconds seconds", $ref);
}

/**
 * Adds a relative time to a reference datetime
 * @return string: the resulting time */
function mbAddDateTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours = @$fragments[0];
  $minutes = @$fragments[1];
  $seconds = @$fragments[2];
  return mbDateTime("+$hours hours $minutes minutes $seconds seconds", $ref);
}

function mbSubTime($relative, $ref = null) {
  $fragments = explode(":", $relative);
  $hours = @$fragments[0];
  $minutes = @$fragments[1];
  $seconds = @$fragments[2];
  return mbTime("-$hours hours -$minutes minutes -$seconds seconds", $ref);
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
 * Returns the difference between two dates in days
 * @return int: number of days
 **/
function mbTimeRelative($from, $to) {
  $diff = strtotime($to) - strtotime($from); 
  $hours = intval($diff / 3600);
  $mins = intval(($diff % 3600) / 60);
  $secs = intval($diff % 60);
  return "$hours:$mins:$secs";
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
 * 
 */
 
function array_neighbor($arr, $key){
  $keys = array_keys($arr);
  $keyIndexes = array_flip($keys);
  
  $return = array();
  if (isset($keys[$keyIndexes[$key]-1])) {
    $return["prev"] = $keys[$keyIndexes[$key]-1];
  }else{
    $return["prev"] = null;
  }
  
  if(isset($keys[$keyIndexes[$key]+1])) {
    $return["next"] = $keys[$keyIndexes[$key]+1];
  }else{
    $return["next"] = null;
  }
  
  return $return;
}

if (!function_exists('array_diff_key')) {
  function array_diff_key() {
    $argCount  = func_num_args();
    $argValues  = func_get_args();
    $valuesDiff = array();
    if ($argCount < 2) return false;
    foreach ($argValues as $argParam) {
      if (!is_array($argParam)) return false;
    }
    foreach ($argValues[0] as $valueKey => $valueData) {
      for ($i = 1; $i < $argCount; $i++) {
        if (isset($argValues[$i][$valueKey])) continue 2;
      }
      $valuesDiff[$valueKey] = $valueData;
    }
    return $valuesDiff;
  }
}

/**
 * Inserts a CSV file into a mysql table 
 * Not a generic function : used for import of specials files
 * in dPinterop
 * @todo : become a generic function
 **/

function mbInsertCSV( $fileName, $tableName, $oldid = false )
{
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
  
    while ( ! feof( $file ) )
    {
        $k++;
        $line = str_replace("NULL", "\"NULL\"", fgets( $file, 1024));
        $size = strlen($line)-3;
        $test1 = $line[$size] != "\"";
        if(($fileName != "modules/dPinterop/doc_recus.txt") && ($fileName != "modules/dPinterop/chemin_courrier.txt"))
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
            $line = str_replace ( "\\\";\\\"", "', '", $line );
            $line = str_replace ( "\\\"", "", $line );
            if($oldid)
              $requete = 'INSERT INTO '.$tableName.' VALUES ( \''.$line.'\', \'\' ) ';
            else
              $requete = 'INSERT INTO '.$tableName.' VALUES ( \''.$line.'\' ) ';
            if ( ! $this->_spec->ds->exec ( $requete ) ) {
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

/**
 * Loads a javascript with build version postfix to prevent nasty cache effects
 * while updating the system.  
 */
function mbLoadScript($filepath, $modeReturn = 0) {
  global $version;
  $build = $version["build"];
  $tag = "\n<script type='text/javascript' src='$filepath?build=$build'></script>";
  if ($modeReturn) { 
    return $tag;
  }
  
  echo $tag;
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
  
  // Gosu doit etre définit avant Prototype
  // Car Prototype redéfinit les méthodes de Array
  $affichageScript .= mbLoadScript("includes/javascript/gosu/array.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/cookie.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/debug.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/ie5.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/keyboard.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/string.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/gosu/validate.js",$modeReturn);
  
  // Prototype doit être définit après Gosu (cf ci-dessus)
  $affichageScript .= mbLoadScript("lib/scriptaculous/lib/prototype.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/scriptaculous/src/scriptaculous.js",$modeReturn);
  $affichageScript .= mbLoadScript("lib/rico/rico.js",$modeReturn);
  
  $affichageScript .= mbLoadScript("includes/javascript/prototypex.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/ricoex.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/prototype_hack.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/functions.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/input_mask.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/cjl_cookie.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/url.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/forms.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/checkForms.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/printf.js",$modeReturn);
  $affichageScript .= mbLoadScript("includes/javascript/browser.js",$modeReturn);  
  $affichageScript .= mbLoadScript("includes/javascript/window.js",$modeReturn);
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
 * Reomve accents and some strange characters
 * @return string: string w/o accents  */
function mbRemoveAccents( $str ) {
  return strtr($str,
    "()!$'?: ,&+-/.ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ",
    "--------------SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
}

/**
 * Remove values in an array
 * @return void */
function mbRemoveValuesInArray($needle, &$haystack) {
  while(($key = array_search($needle,  $haystack)) !== false) {
    unset($haystack[$key]);
  }
}

function mbArrayMergeRecursive($paArray1, $paArray2) {
  if (!is_array($paArray1) or !is_array($paArray2)) { 
     return $paArray2;
  }

  foreach ($paArray2 AS $sKey2 => $sValue2) {
    $paArray1[$sKey2] = mbArrayMergeRecursive(@$paArray1[$sKey2], $sValue2);
  }
   
  return $paArray1;
}

function mbArrayFromString($str) {
  $array = array();
  for ($i = 0; $i < strlen($str); $i++) {
    $array[] = $str[$i];
  }
  return $array;
}

function mbArrayCreateRange($min, $max, $cleasvalue = false, $step = 1){
  if($min>=$max){
    return false;
  }
  $aTemp = array();
  while($min<=$max){
    if($cleasvalue){
      $aTemp[$min] = $min;
    }else{
      $aTemp[] = $min;
    }
    $min += $step;
  }
  return $aTemp;
}

/**
 * Ensures a directory path exists. Creates it if needed.
 * @return boolean jobdone-value */
function mbForceDirectory($dir, $mode = 0755) {
  if (!$dir) {
    return false;
  }
  
  if (is_dir($dir) || $dir === "/") {
    return true;
  }
  
  if (mbForceDirectory(dirname($dir))){
    return mkdir($dir, $mode);
  }
  
  return false;
}


/**
 * Clears out any file or sub-directory from target path
 * @return boolean jobdone-value */
function mbClearPath($dir) {
  if (!($dir = dir($dir))) {
    return false;
  }
  
  while (false !== $item = $dir->read()) {
    if ($item != '.' && $item != '..' && !mbRemovePath($dir->path . DIRECTORY_SEPARATOR . $item)) {
      $dir->close();
      return false;
    }
  }
  
  $dir->close();
  return true;
}

/**
 * Recursively removes target path
 * @return boolean jobdone-value */
function mbGetFileExtension($path) {
  $fragments = explode(".", basename($path));
  if (count($fragments) < 2) {
    return "";
  }
  
  return $fragments[count($fragments) - 1];
}

/**
 * Recursively removes target path
 * @return boolean jobdone-value */
function mbRemovePath($dir) {
  if (is_dir ($dir)) {
    if (mbClearPath($dir)) {
      return rmdir ($dir);
    }
    return false;
  }
  return unlink ($dir);
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

function getInstalledClasses($properties = array()) {
  global $AppUI;
  $AppUI->getAllClasses();
  $listClasses = getChildClasses("CMbObject", $properties);
  foreach ($listClasses as $key => $class) {
    if(!has_default_constructor($class)) {
    	unset($listClasses[$key]);
    	continue;
    }
    $object = new $class;
    if ($object->_ref_module === null) {
      unset($listClasses[$key]);
    }
  }
  
  return $listClasses;
}

/**
 * Check if a class inherits froma an ancestor
 * @return bool
 */
function class_inherits_from($class, $ancestor) {
	if ($class == $ancestor) {
		return true;
	}
	
	$parent = get_parent_class($class);
	return $parent ? class_inherits_from($parent, $ancestor) : false;
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
 * Retourne un tableau des classes du module
 * @param
 * @return array 
 **/
function mbGetClassByModule($module) {
	// Liste des Class
	$listClass = getInstalledClasses();
	
	$tabClass = array();
	foreach($listClass as $class) {
  		$object = new $class;
  		if(!$object->_ref_module) {
  			continue;
  		}
  		if($object->_ref_module->mod_name == $module) {
  			$tabClass[] = $object->_class_name;
  		}
  	}
  	return $tabClass;
}


/**
 * Strip slashes recursively if value is an array
 * @param mixed $value
 * @param mixed stripped value
 **/
function stripslashes_deep($value) {
  return is_array($value) ?
    array_map("stripslashes_deep", $value) :
    stripslashes($value);
}

/**
 * Copy the hash array content into the object as properties
 * only existing properties of object are filled. when defined in hash
 * @param array $hash the input array
 * @param object $object to fill of any class
 * @param boolean $doStripSlashes
 **/
function bindHashToObject($hash, &$object, $doStripSlashes = true) {
  foreach (get_object_vars($object) as $k => $v) {
    if (isset($hash[$k])) {
      $object->$k = $doStripSlashes ? stripslashes_deep($hash[$k]) : $hash[$k];
    } 
  }
}

function mbDateToLocale($date) {
  $parts = split("-", $date);
  
  $day   = $parts[2];
  $month = $parts[1];
  $year  = $parts[0];
  
  return sprintf("%02d/%02d/%04d", $day, $month, $year);
}

function mbDateFromLocale($date) {
  $parts = split("/", $date);
  
  $day   = $parts[0];
  $month = $parts[1];
  $year  = $parts[2];
  
  return sprintf("%04d-%02d-%02d", $year, $month, $day);
}

/**
 * @todo A SUPPRIMER APRES VERIFICATION
 */
function unix2dateTime( $time ) {
  // converts a unix time stamp to the default date format
  return $time > 0 ? date("Y-m-d H:i:s", $time) : null;
}

/**
 * @todo A SUPPRIMER APRES VERIFICATION
 */
function dateTime2unix( $time ) {
  if ($time == "0000-00-00 00:00:00") {
	return -1;
  }
  if( ! preg_match( "/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})(.?)$/", $time, $a ) ) {
	return -1;
  } else {
    return mktime( $a[4], $a[5], $a[6], $a[2], $a[3], $a[1] );
  }
}

?>