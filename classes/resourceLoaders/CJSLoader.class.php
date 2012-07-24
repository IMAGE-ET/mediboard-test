<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Utility class to handle Javascript loading in an HTML document
 */
abstract class CJSLoader extends CHTMLResourceLoader {
  static $files = array();
  
  /**
   * Creates an HTML script tag to load a Javascript file
   * 
   * @param string $file  The Javascript file name
   * @param string $cc    An IE conditional comment
   * @param string $build A build number
   * @param string $type  The mime type to put in the HTML tag
   * 
   * @return string The HTML script tag
   */
  static function loadFile($file, $cc = null, $build = null, $type = "text/javascript") {
    $tag = self::getTag(
      "script", 
      array(
        "type" => $type ? $type : "text/javascript",
        "src"  => "$file?".self::getBuild($build),
      ), 
      null, 
      false
    );
    
    return self::conditionalComments($tag, $cc);
  }
  
  /**
   * Loads a list of Javascript files, with or without minification
   * 
   * @param boolean $compress True to minify the Javascript files
   * @param string  $type     The mime type to use to include the Javascript files
   * 
   * @return string A list or a single HTML script tag
   */
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
      foreach ($files as $index => $file) {
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
        
        foreach ($files as $file) {
          if (self::getLastChange($file) > $last_update) {
            $uptodate = false;
            break;
          }
        }
      }
      
      if (!$uptodate) {
        $all_scripts = "";
        foreach ($files as $file) {
          $_script = file_get_contents($file);
          if (substr($_script, 0, 3) == chr(0xEF).chr(0xBB).chr(0xBF)) {
            $_script = substr($_script, 3);
          }
          $all_scripts .= $_script."\n";
        }
        
        if ($compress == 2) {
          $all_scripts = self::minify($all_scripts);
        }
        
        file_put_contents($cachefile, $all_scripts);
        $last_update = time();
      }
      
      foreach ($excluded as $file) {
        $result .= self::loadFile($file, null, self::getLastChange($file), $type)."\n";
      }
      
      $result .= self::loadFile($cachefile, null, $last_update, $type)."\n";
    }
    else {
      foreach (self::$files as $file) {
        $result .= self::loadFile($file, null, self::getLastChange($file), $type)."\n";
      }
    }
    
    return $result;
  }
  
  /**
   * Minify a Javascript script
   * 
   * @param string $js Javascript source code
   * 
   * @return string The minified Javascript source code
   */
  static function minify($js) {
    return JSMin::minify($js);
  }
  
  /**
   * Writes a locale file
   * 
   * @param string $language The language code (fr, en, ...)
   * @param array  $locales  The locales
   * @param string $label    A code to istinguish different locales listes
   * 
   * @return void
   */
  static function writeLocaleFile($language = null, $locales = null, $label = null) {
    global $version;
    
    // It will update all the locale files
    if (!$language) {
      $languages = array();
      foreach (glob("./locales/*", GLOB_ONLYDIR) as $lng) {
        $languages[] = basename($lng);
      }
    }
    else {
      $languages = array($language);
    }
    
    if (!$locales) {
      foreach ($languages as $language) {
        $localeFiles = array_merge(
          glob("./locales/$language/*.php"), 
          glob("./modules/*/locales/$language.php")
        );
      }
      
      foreach ($localeFiles as $localeFile) {
        if (basename($localeFile) != "meta.php") {
          include $localeFile;
        }
      }
    }
    
    foreach ($languages as $language) {
      $path = self::getLocaleFilePath($language, $label);
      
      if ($fp = fopen($path, 'w')) {
        // The callback will filter on empty strings (without it, "0" will be removed too).
        $locales = array_filter($locales, "stringNotEmpty");
        // TODO: change the invalid keys (with accents) of the locales to simplify this
        $keys   = array_map('utf8_encode', array_keys($locales));
        $values = array_map('utf8_encode', array_values($locales));
        
        foreach ($values as &$_value) {
          $_value = CMbString::unslash($_value);
        }
        
        $compress = false;
        
        if ($compress) {
          $delim = "/([\.-])/";
          $arr = new stdClass;
          foreach ($keys as $_pos => $_key) {
            $parts = preg_split($delim, $_key, null, PREG_SPLIT_DELIM_CAPTURE);
            
            $_arr = $arr;
            $last_key = count($parts)-1;
            
            foreach ($parts as $i => $_token) {
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
  
  /**
   * Recursive function to reduce locales keys
   * 
   * @param object $object An array of locales
   * 
   * @return void
   */
  static function clearLocalesKeys($object) {
    foreach ($object as $key => &$value) {
      if (!is_object($value)) {
        continue;
      }
      
      $keys = get_object_vars($value);
      
      if (count($keys) === 1 && isset($keys['$'])) {
        $object->$key = $keys['$'];
      }
      else {
        self::clearLocalesKeys($object->$key);
      }
    }
  }

  /**
   * Creates a JSON locales file
   * 
   * @param array  $locales The locales array
   * @param string $label   The locales label
   * 
   * @return string The path to the JSON locales file
   */
  static function getLocaleFile($locales = null, $label = null) {
    $language = CAppUI::pref("LOCALE");
    
    $path = self::getLocaleFilePath($language, $label);
    
    if (!is_file($path)) {
      self::writeLocaleFile($language, $locales, $label);
    }
    
    return $path;
  }
  
  /**
   * Returns the JSON locales file path
   * 
   * @param string $language The language code (fr, en, ...)
   * @param string $label    The locales label
   * 
   * @return string The JSON file path
   */
  static function getLocaleFilePath($language, $label = null) {
    return "tmp/locales".($label ? ".$label" : "")."-$language.js";
  }
}
