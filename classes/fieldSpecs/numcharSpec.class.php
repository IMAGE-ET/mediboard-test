<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");
require_once("./classes/fieldSpecs/numSpec.class.php");

class CNumcharSpec extends CNumSpec {
  
  function getSpecType() {
    return("numchar");
  }

  function getDBSpec(){
    $type_sql = "bigint zerofill";
    
    if($this->maxLength || $this->length){
      $length = $this->maxLength ? $this->maxLength : $this->length;
      $valeur_max = pow(10,$length);
      $type_sql = "tinyint";
      if ($valeur_max > pow(2,8)) {
        $type_sql = "mediumint";
      }
      if ($valeur_max > pow(2,16)) {
        $type_sql = "int";
      }
      if ($valeur_max > pow(2,32)) {
        $type_sql = "bigint";
      }
      $type_sql .= "($length) unsigned zerofill";
    }
    return $type_sql;
  }
}

?>