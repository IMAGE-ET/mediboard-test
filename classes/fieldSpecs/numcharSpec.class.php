<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");
CAppUI::requireSystemClass("fieldSpecs/numSpec");

class CNumcharSpec extends CNumSpec {
  
  function getSpecType() {
    return("numchar");
  }
  

  function getDBSpec(){
    $type_sql = "BIGINT ZEROFILL";
    
    if($this->maxLength || $this->length){
      $length = $this->maxLength ? $this->maxLength : $this->length;
      $valeur_max = pow(10,$length);
      $type_sql = "TINYINT";
      if ($valeur_max > pow(2,8)) {
        $type_sql = "MEDIUMINT";
      }
      if ($valeur_max > pow(2,16)) {
        $type_sql = "INT";
      }
      if ($valeur_max > pow(2,32)) {
        $type_sql = "BIGINT";
      }
      $type_sql .= "($length) UNSIGNED ZEROFILL";
    }
    return $type_sql;
  }
}

?>