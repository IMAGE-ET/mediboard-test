<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage classes
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('dPcompteRendu', 'aidesaisie') );

function htmlReplace($find, $replace, &$source) {
  $matches = array();
  $nbFound = preg_match_all("/$find/", $source, $matches);
  $source = preg_replace("/$find/", $replace, $source);
  return $nbFound;
}

function purgeHtmlText($regexps, &$source) {
  $total = 0;
  foreach ($regexps as $find => $replace) {
    $total += htmlReplace($find, $replace, $source); 
  }

//  echo "<h1>Total found: $total<h1><hr />";
  
  return $total;
}

/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */
class CMbObject extends CDpObject {
  
  var $_aides = array();
  /*
   * Properties  specification
   */
  var $_props = array();
  var $_enums = array();

  /**
   *  Generic check method
   *
   *  Can be overloaded/supplemented by the child class
   *  @return null if the object is ok a message if not
   */
  function check() {
    global $dPconfig;
    $msg = null;
    $properties = get_object_vars($this);
    $class = get_class($this);
    
    foreach ($this->_props as $propName => $propSpec) {
      if(!array_key_exists($propName, $properties)) {
        trigger_error("La spécification cible la propriété '$propName' inexistante dans la classe '$class'", E_USER_WARNING);
      } else {
        $propValue =& $this->$propName;
        if ($propValue !== null) {
          $msgProp = $this->checkProperty($propName);
          $debugInfo = $dPconfig["debug"] ? "(val:'$propValue', spec:'$propSpec')" : "";
          $msg .= $msgProp ? "<br/> => $propName : $msgProp" : null;
        }
      }
    }
    
    return $msg;
  }
  
  function buildEnums() {
    foreach ($this->_props as $propName => $propSpec) {
      $specFragments = explode("|", $propSpec);
      if ($this->lookupSpec("enum", $specFragments)) {
        $this->lookupSpec("confidential", $specFragments);
        $this->lookupSpec("notNull", $specFragments);
        $this->_enums[$propName] = $specFragments;
      }
    }
  }
  
  function lookupSpec($specFragment, &$specFragments) {
    $fragmentPosition = array_search($specFragment, $specFragments);

    if ($fragmentPosition !== false) {
      array_splice($specFragments, $fragmentPosition, 1);
    }
    
    return $fragmentPosition !== false;
  }
  
  function checkMoreThan($propValue, $specFragments) {
    if ($fragment = @$specFragments[1]) {
  
      switch ($fragment) {
        case "moreThan":
        $targetPropName = $specFragments[2];
        $targetPropValue = $this->$targetPropName;
    
        if (!isset($targetPropValue)) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", $targetPropName);
        }

        if ($propValue <= $targetPropValue) {
          return "'$propValue' n'est pas strictement supérieur à '$targetPropValue'";
        }
  
        break;
             
        case "moreEquals":
        $targetPropName = $specFragments[2];
        $targetPropValue = $this->$targetPropName;
    
        if (!isset($targetPropValue)) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", $targetPropName);
        }

        if ($propValue < $targetPropValue) {
          return "'$propValue' n'est pas supérieur ou égal à '$targetPropValue'";
        }
  
        break;
      }
    };
  
    return null;
  }
  
  function checkProperty($propName) {
    $propValue =& $this->$propName;
    $propSpec =& $this->_props[$propName];
    $specFragments = explode("|", $propSpec);
    
    // remove confidential status
    $confidential = array_search("confidential", $specFragments);
    if ($confidential !== false) {
      array_splice($specFragments, $confidential, 1);
    }

    // notNull
    $notNull = array_search("notNull", $specFragments);
    if ($notNull !== false) {
      array_splice($specFragments, $notNull, 1);
    }

    if ($propValue == "") {
      return $notNull ? "Ne pas peut pas avoir une valeur nulle" : null;
    }
    
    switch ($specFragments[0]) {
      // Reference to another object
			case "ref":
        if (!is_numeric($propValue)) {
          return "N'est pas une référence (format non numérique)";
        }

        $propValue = intval($propValue);
        
        if ($propValue == 0 and $notNull) {
          return "ne peut pas être une référence nulle";
        }

        if ($propValue < 0) {
          return "N'est pas une référence (entier négatif)";
        }
				
				break;
        
      // regular string
      case "str":
        switch (@$specFragments[1]) {
          case null:
            break;
            
          case "length":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur invalide (longueur = $length)";
            }
            
            if (strlen($propValue) != $length) {
              return "N'a pas la bonne longueur (longueur souhaitée : $length)'";
            }
            
            break;
            
          case "minLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) < $length) {
              return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
            }
            
            break;
            
          case "maxLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) > $length) {
              return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
            }
            
            break;
        
          default:
            return "Spécification de chaîne de caractères invalide";
        }
        
        break;

      // numerical string
      case "num":
        if (!is_numeric($propValue)) {
          return "N'est pas une chaîne numérique";
        }
      
        switch (@$specFragments[1]) {
          case null:
            break;
            
          case "length":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur invalide (longueur = $length)";
            }
            
            if (strlen($propValue) != $length) {
              return "N'a pas la bonne longueur (longueur souhaité : $length)'";
            }
            
            break;
            
          case "minLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) < $length) {
              return "N'a pas la bonne longueur (longueur minimale souhaitée : $length)'";
            }
            
            break;
            
          case "maxLength":
            $length = intval(@$specFragments[2]);
            
            if ($length < 1 or $length > 255) {
              return "Spécification de longueur minimale invalide (longueur = $length)";
            }
            
            if (strlen($propValue) > $length) {
              return "N'a pas la bonne longueur (longueur maximale souhaitée : $length)'";
            }
            
            break;
        
          default:
            return "Spécification de chaîne numérique invalide";
        }
        
        break;
      
      // Enumeration
      case "enum":
        array_shift($specFragments);
        if (!in_array($propValue, $specFragments)) {
          return "N'a pas une valeur possible";
        }

        break;
    
      // Date
      case "date":
        if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue)) {
          return "format de date invalide";
        }
        
        break;
    
      // Time
      case "time":
        if (!preg_match ("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $propValue)) {
          return "format de time invalide";
        }
        
        break;
    
      // DateTime
      case "dateTime":
        if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})[ \+]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/", $propValue)) {
          return "format de dateTime invalide";
        }
        
        break;
    
      // Currrency format
      case "currency":
        if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
          return "n'est pas une valeur monétaire (utilisez le . pour la virgule)";
        }
        
        break;
    
      // Percentage with two digits after coma
      case "pct":
        if (!preg_match ("/^([0-9]+)(\.[0-9]{0,2}){0,1}$/", $propValue)) {
          return "n'est pas un pourcentage (utilisez le . pour la virgule)";
        }
        
        break;
        
      // Text free format
      case "text":
        break;
        
      // HTML Text
      case "html":
        // @todo Should validate against XHTML DTD
        
        // Purges empty spans
        $regexps = array (
          "<span[^>]*>[\s]*<\/span>" => " ",
          "<font[^>]*>[\s]*<\/font>" => " ",
          "<span class=\"field\">([^\[].*)<\/span>" => "$1"
          );
        
//         while (purgeHtmlText($regexps, $propValue));

        break;
        
      // Special Codes
      case "code":
        switch (@$specFragments[1]) {
          case "ccam":
            if (!preg_match ("/^([a-z0-9]){0,7}$/i", $propValue)) {
              return "Code CCAM incorrect, doit contenir 4 lettres et trois chiffres";
            }
            
            break;

          case "cim10":
            if (!preg_match ("/^([a-z0-9]){0,5}$/i", $propValue)) {
              return "Code CCAM incorrect, doit contenir 5 lettres maximum";
            }
            
            break;

          case "adeli":
            if (!preg_match ("/^([0-9]){9}$/i", $propValue)) {
              return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
            }
            
            break;

          case "insee":
            if (!preg_match ("/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i", $propValue, $matches)) {
              return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
            }
          
            $code = $matches[1];
            $cle = $matches[2];
            
            // Use bcmod since standard modulus can't work on numbers exceedind the 2^32 limit
            if (function_exists("bcmod")) {
              if (97 - bcmod($code, 97) != $cle) {
                return "Matricule incorrect, la clé n'est pas valide";
              }
            }
          
            break;

          default:
            return "Spécification de code invalide";
        }

        break;

      default:
        return "Spécification invalide";
		}

    if ($checkMessage = $this->checkMoreThan($propValue, $specFragments)) {
      return $checkMessage;
    }
    
    return null;
  }
  
  function load( $oid=null , $strip = true) {
    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval( $oid );
    }
    $oid = $this->$k;
    if ($oid === null) {
      return false;
    }
    $sql = "SELECT * FROM $this->_tbl WHERE $this->_tbl_key=$oid";
    $object = db_loadObject( $sql, $this, false, $strip );
    $this->checkConfidential();
    
    $this->updateFormFields();
    if($object)
      return $this;
    else
      return false;
  }
  
  function checkConfidential($props = null) {
    global $dPconfig;
    if($dPconfig["hide_confidential"]) {
      if($props == null)
        $props = $this->_props;
      foreach ($props as $propName => $propSpec) {
        $propValue =& $this->$propName;
        if ($propValue !== null) {
          $this->codeProperty($propValue, $propSpec);
        }
      }
    }
  }
  
  function randomString($array, $length) {
    $key = "";
    $count = count($array) - 1;
    srand((double)microtime()*1000000);
    for($i = 0; $i < $length; $i++) $key .= $array[rand(0, $count)];
    return($key);
  }

  function codeProperty(&$propValue, &$propSpec) {
    $chars = array(
      "a","b","c","d","e","f","g","h","i","j","k","l","m",
      "n","o","p","q","r","s","t","u","v","w","x","y","z");
    $nums = array("0","1","2","3","4","5","6","7","8","9");
    $days = array();
    for($i = 1; $i < 29; $i++) {
      if($i < 10)
        $days[] = "0".$i;
      else
        $days[] = $i;
    }
    $monthes = array(
      "01","02","03","04","05","06","07","08","09", "10", "11", "12");
    $hours = array();
    for($i = 9; $i < 18; $i++) {
      if($i < 10)
        $hours[] = "0".$i;
      else
        $hours[] = $i;
    }
    $mins = array();
    for($i = 0; $i < 60; $i++) {
      if($i < 10)
        $mins[] = "0".$i;
      else
        $mins[] = $i;
    }
    
    $defaultLength = 6;

    $specFragments = explode("|", $propSpec);
    
    // test if it is confidential
    $confidential = array_search("confidential", $specFragments);
    if ($confidential !== false) {
      array_splice($specFragments, $confidential);
    }

    if ($confidential) {
      // test if notNull and remove this fragment
      $notNull = array_search("notNull", $specFragments);
      if ($notNull !== false) {
        array_splice($specFragments, $notNull);
      }
      
      switch ($specFragments[0]) {
        // Reference to another object : do nothing
        case "ref":
          break;
          
        // regular string
        case "text": 
          $propValue = $this->randomString($chars, 40);
          break;
          
        // regular string
        case "str":
          switch (@$specFragments[1]) {
            case null:
              $propValue = $this->randomString($chars, $defaultLength);
              break;
              
            case "length":
              $length = intval(@$specFragments[2]);
              $propValue = $this->randomString($chars, $length);
              break;
              
            case "minLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength < $length)
                $propValue = $this->randomString($chars, $length);
              else
                $propValue = $this->randomString($chars, $defaultLength);
              break;
              
            case "maxLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength > $length)
                $propValue = $this->randomString($chars, $length);
              else
                $propValue = $this->randomString($chars, $defaultLength);
              break;
          
            default:
              $propValue = null;
          }
          
          break;
  
        // numerical string
        case "num":
          switch (@$specFragments[1]) {
            case null:
              $propValue = $this->randomString($nums, $defaultLength);
              break;
              
            case "length":
              $length = intval(@$specFragments[2]);
              $propValue = $this->randomString($nums, $length);
              break;
              
            case "minLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength < $length)
                $propValue = $this->randomString($nums, $length);
              else
                $propValue = $this->randomString($nums, $defaultLength);
              break;
              
            case "maxLength":
              $length = intval(@$specFragments[2]);
              if($defaultLength > $length)
                $propValue = $this->randomString($nums, $length);
              else
                $propValue = $this->randomString($nums, $defaultLength);
              break;
          
            default:
              $propValue = null;
          }
          
          break;
        
        // Enumeration
        case "enum":
          array_shift($specFragments);
          $propValue = $this->randomString($specFragments, 1);
          break;
      
        // Date
        case "date":
          $propValue = "19".$this->randomString($nums, 2)."-".$this->randomString($monthes, 1)."-".$this->randomString($days, 1);
          break;
      
        // Time
        case "time":
          $propValue = $this->randomString($hours, 1).":".$this->randomString($mins, 1).":".$this->randomString($mins, 1);
          break;
      
        // DateTime
        case "dateTime":
          $propValue = "19".$this->randomString($nums, 2)."-".$this->randomString($monthes, 1)."-".$this->randomString($days, 1);
          $propValue .= " ".$this->randomString($hours, 1).":".$this->randomString($mins, 1).":".$this->randomString($mins, 1);
          break;
      
        // Format monétaire
        case "currency":
          $propValue = $this->randomString($nums, 2).".".$this->randomString($nums, 2);
          break;
          
        // HTML Text
        case "html":
          $propValue = "Document confidentiel";
          break;
  
        default:
          return "Spécification invalide";
      }
    }
    return null;
  }

/**
 *  Generic check for whether dependancies exist for this object in the db schema
 *
 *  Can be overloaded/supplemented by the child class
 *  @param string $msg Error message returned
 *  @param int Optional key index
 *  @param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
 *  @return true|false
 */
  function canDelete( &$msg, $oid=null, $joins=null ) {
    global $AppUI;
    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval( $oid );
    } else {
      $oid = $this->$k;
    }
    
    $msg = array();
    $select = "SELECT $this->_tbl.$k,";
    $from = "\nFROM $this->_tbl ";
    $where  = "\nWHERE $this->_tbl.$k = '$oid' GROUP BY $this->_tbl.$k";
    
    if (is_array( $joins )) {
      foreach( $joins as $table ) {
        $count = "\nCOUNT(DISTINCT {$table['name']}.{$table['idfield']}) AS number";
        $join = "\nLEFT JOIN {$table['name']} ON {$table['name']}.{$table['joinfield']} = $this->_tbl.$k";

        $sql = $select . $count . $from . $join . $where;
        
        $obj = null;
        if (!db_loadObject( $sql, $obj )) {
          $msg = db_error();
          return false;
        }

        if ($obj->number) {
          $msg[] = $obj->number. " " . $AppUI->_( $table['label'] );
        }
      }
    }

    if (count( $msg )) {
      $msg = $AppUI->_( "noDeleteRecord" ) . ": " . implode( ', ', $msg );
      return false;
    }

     return true;
  }
  
  function loadAides($user_id) {
    $class = substr(get_class($this), 1);
    
    // Initialisation to prevent understandable smarty notices
    foreach ($this->_props as $propName => $propSpec) {
      $specFragments = explode("|", $propSpec);
      if (array_search("text", $specFragments) !== false) {
        $this->_aides[$propName] = null;
      }
    }

    // Load appropriate Aides
    $where = array();
    $where["user_id"] = " = '$user_id'";
    $where["class"] = " = '$class'";
    
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where);
        
    // Aides mapping suitable for select options
    foreach ($aides as $aide) {
      $this->_aides[$aide->field][$aide->text] = $aide->name;  
    }
    
  }
}
?>