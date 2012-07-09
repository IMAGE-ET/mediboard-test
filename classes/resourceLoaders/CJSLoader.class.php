<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CJSLoader extends CHTMLResourceLoader {
  static $files = array();
  
  /**
   * Loads a javascript file
   */
  static function loadFile($file, $cc = null, $build = null, $type = "text/javascript") {
    $tag = self::getTag("script", array(
      "type" => $type ? $type : "text/javascript",
      "src"  => "$file?".self::getBuild($build),
    ), null, false);
    return self::conditionalComments($tag, $cc);
  }
  
  static function loadFiles($compress = false, $type = "text/javascript") {
    $result = "";
    $compress = CAppUI::conf("minify_javascript");
    
    /** 
     * There is a speed boost on the page load when using compression 
     * between the top of the head and the dom:loaded event of about 25%.
     * This is because of parse time that is reduced (compare the global __pageLoad variable)
     * The number of requests from a regular page goes down from 100 to 70.
     * The total size of the JS goes down from 300kB to 230kB (gzipped).
     */
    if ($compress) {
      $compress = 1; // force Normal compression
      
      $files = self::$files;
      $excluded = array();
      $uptodate = false;
      
      // We exclude files already in the tmp dir
      foreach($files as $index => $file) {
        if (strpos($file, "tmp/") === 0) {
          $excluded[] = $file;
          unset($files[$index]);
        }
      }
      
      $hash = self::getHash(implode("", $files)."-level-$compress");
      $cachefile = "tmp/$hash.js";
      
      // If it exists, we check if it is up to date
      if (file_exists($cachefile)) {
        $uptodate = true;
        $last_update = self::getLastChange($cachefile);
        foreach($files as $file) {
          if (self::getLastChange($file) > $last_update) {
            $uptodate = false;
            break;
          }
        }
      }
      
      if (!$uptodate) {
        $all_scripts = "";
        foreach($files as $file) {
          $_script = file_get_contents($file);
          if (substr($_script, 0, 3) == chr(0xEF).chr(0xBB).chr(0xBF)) {
            $_script = substr($_script, 3);
          }
          $all_scripts .= $_script."\n";
        }
        
        if($compress == 2) {
          $all_scripts = self::minify($all_scripts);
        }
        
        file_put_contents($cachefile, $all_scripts);
        $last_update = time();
      }
      
      foreach($excluded as $file) {
        $result .= self::loadFile($file, null, self::getLastChange($file), $type)."\n";
      }
      
      $result .= self::loadFile($cachefile, null, $last_update, $type)."\n";
    }
    else {
      foreach(self::$files as $file) {
        $result .= self::loadFile($file, null, self::getLastChange($file), $type)."\n";
      }
    }
    
    return $result;
  }
  
  static function minify($js) {
    return JSMin::minify($js);
  }
  
  static function writeLocaleFile($language = null, $locales = null, $label = null) {
    global $version;
    
    // It will update all the locale files
    if (!$language) {
      $languages = array();
      foreach (glob("./locales/*", GLOB_ONLYDIR) as $lng)
        $languages[] = basename($lng);
    }
    else {
      $languages = array($language);
    }
    if (!$locales) {
      foreach($languages as $language) {
        $localeFiles = array_merge(
          glob("./locales/$language/*.php"), 
          glob("./modules/*/locales/$language.php")
        );
      }
      
      foreach ($localeFiles as $localeFile) {
        if (basename($localeFile) != "meta.php") {
          require $localeFile;
        }
      }
    }
    
    foreach($languages as $language) {
      $path = self::getLocaleFilePath($language, $label);
      if ($fp = fopen($path, 'w')) {
        // The callback will filter on empty strings (without it, "0" will be removed too).
        $locales = array_filter($locales, "stringNotEmpty");
        // TODO: change the invalid keys (with accents) of the locales to simplify this
        $keys   = array_map('utf8_encode', array_keys($locales));
        $values = array_map('utf8_encode', array_values($locales));
        
        foreach($values as &$_value) {
          $_value = CMbString::unslash($_value);
        }
        
        $compress = false;
        
        if ($compress) {
          $delim = "/([\.-])/";
          $arr = new stdClass;
          foreach($keys as $_pos => $_key) {
            $parts = preg_split($delim, $_key, null, PREG_SPLIT_DELIM_CAPTURE);
            
            $_arr = $arr;
            $last_key = count($parts)-1;
            
            foreach($parts as $i => $_token) {
              $last = ($i == $last_key);
              if ($_token === "") {
                $_token = '_$_';
              }
              
              if ($last) {
                $_arr->{$_token} = (object)array('$' => $values[$_pos]);
                break;
              }
              elseif (!isset($_arr->{$_token})) {
                $_arr->{$_token} = new stdClass;
              }
              
              $_arr = $_arr->{$_token};
            }
            
            //unset($_arr);
          }
          
          self::clearLocalesKeys($arr);
          $json = $arr;
        }
        else {
          $json = array_combine($keys, $values);
        }
        
        $script = '//'.$version['build']."\nwindow.locales=".json_encode($json).";";
        fwrite($fp, $script);
        fclose($fp);
      }
    }
  }
  
  static function clearLocalesKeys($object) {
    foreach($object as $key => &$value) {
      if (!is_object($value)) continue;
      
      $keys = get_object_vars($value);
      if (count($keys) === 1 && isset($keys['$'])) {
        $object->$key = $keys['$'];
      }
      else {
        self::clearLocalesKeys($object->$key);
      }
    }
  }

  static function getLocaleFile($locales = null, $label = null) {
    $language = CAppUI::pref("LOCALE");
    
    $path = self::getLocaleFilePath($language, $label);
    
    if (!is_file($path)) {
      self::writeLocaleFile($language, $locales, $label);
    }
    
    return $path;
  }
  
  static function getLocaleFilePath($language, $label = null) {
    return "tmp/locales".($label ? ".$label" : "")."-$language.js";
  }
}
