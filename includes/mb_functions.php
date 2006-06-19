<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision$
 * @author Thomas Despoix
 */

require_once("mb_version.php");

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
function mbGetValueFromGet($valName, $valDefault = NULL) {
  return isset($_GET[$valName]) ? $_GET[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Post, with at least a
 * default value
 * @access public
 * @return any 
 **/
function mbGetValueFromPost($valName, $valDefault = NULL) {
  return isset($_POST[$valName]) ? $_POST[$valName] : $valDefault;
}

/**
 * Returns the value of a variable retreived it from HTTP Get, then from the session
 * Stores it in _SESSION in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetValueFromGetOrSession($valName, $valDefault = NULL) {
  global $m;

  if (isset($_GET[$valName])) {
    $_SESSION[$m][$valName] = $_GET[$valName];
  }
  
  return dPgetParam($_SESSION[$m], $valName, $valDefault);
}

/**
 * Returns the value of a variable retreived it from HTTP Post, then from the session
 * Stores it in _SESSION in all cases, with at least a default value
 * @access public
 * @return any 
 **/
function mbGetValueFromPostOrSession($valName, $valDefault = NULL) {
  global $m;

  if (isset($_POST[$valName])) {
    $_SESSION[$m][$valName] = $_POST[$valName];
  }
  
  return dPgetParam($_SESSION[$m], $valName, $valDefault);
}

/**
 * Sets a value to the session. Very useful to nullify object ids after deletion
 * @access public
 * @return void
 **/
function mbSetValueToSession($valName, $value = NULL) {
  global $m;

  $_SESSION[$m][$valName] = $value;
}

/**
 * Traces variable using preformated text et varibale export
 * @return void 
 **/
function mbTrace($var, $label = null, $die = false, $error = false) {
  $export = print_r($var, true); 
  $export = htmlspecialchars($export);
  if($error)
    trigger_error (html_entity_decode("\n$label: $export\n"));
  else
  echo "<pre>$label: $export</pre>";

  if ($die) {
    die();
  }
}

/**
 * Calculate the bank holidays in France
 * @return array: List of bank holidays
 **/

function mbBankHolidays($date = null) {
  if(!$date)
    $date = mbDate();
  $year = mbTranformTime("+0 DAY", $date, "%Y");

  // Calculdu dimanche de paques : http://fr.wikipedia.org/wiki/Computus
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
  if (!$relative) {
    $relative = "+ 0 days";
  } elseif ($relative == "last sunday") {
    $relative .= " 12:00:00";
  }
  
  $timestamp = $ref ? strtotime($ref) : time();
  $transtime = strtotime($relative, $timestamp);
  return strftime($format, $transtime);
}

/**
 * Transforms absolute or relative time into DB friendly DATETIME format
 * @return string: the transformed time 
 **/
function mbDateTime($relative = null, $ref = null) {
  return mbTranformTime($relative, $ref, "%Y-%m-%d %H:%M:%S");
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
function mbTime($relative, $ref = null) {
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

/**
 * Returns the difference between two dates in days
 * @return int: number of days
 **/
function mbDaysRelative($from, $to) {
  $from = intval(strtotime($from) / 86400);
  $to   = intval(strtotime($to  ) / 86400);
  $days = intval($to - $from);
  return $days;
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
            if ( ! db_exec ( $requete ) ) {
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
 * while updating the system.  */
function mbLoadScript ($filepath) {
  global $mb_version_build;
  echo "\n<script type='text/javascript' src='$filepath?build=$mb_version_build'></script>";
}


/**
 * Links a style sheet with build version postfix to prevent nasty cache effects
 * Only to be called while in the HTML header.  */
function mbLinkStylesheet ($filepath, $media = "all") {
  global $mb_version_build;
  echo "\n<link rel='stylesheet' type='text/css' href='$filepath?build=$mb_version_build' media='$media' />";
}

/**
 * Links a shotcut icon version postfix to prevent nasty cache effects 
 * Only to be called while in the HTML header.  */
function mbLinkShortcutIcon($filepath) {
  global $mb_version_build;
  echo "\n<link rel='shortcut icon' type='image/ico' href='$filepath?build=$mb_version_build' />";
}

/**
 * Link to the mediboard.org documentation page 
 * @return string: the link to mediboard.org  */
function mbPortalLink( $page="Accueil", $title="Portail Mediboard" ) {
  global $AppUI;
  if($page == "bugTracker")
    $url = "\"http://www.mediboard.org/public/tiki-view_tracker.php?trackerId=4\"";
  else
    $url = "\"http://www.mediboard.org/public/tiki-index.php?page=$page\"";
  return "\n<a href=$url target=\"_blank\">$title</a>";
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
    array_splice($haystack, $key, 1);
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
 * Clears out any file a sub-directory from target path
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
 * Gets Mediboard version string
 * @return string Mediboard version */
function mbVersion() {
  // Manual numbering
  $mb_version_major = 0;
  $mb_version_minor = 3;
  $mb_version_patch = 3;
  
  // Automated numbering (should be incremented at each commit)
  $mb_version_build = 21;
  
  return "v$mb_version_major.$mb_version_minor.$mb_version_patch b$mb_version_build";
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

?>