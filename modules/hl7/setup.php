<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphl7 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hl7";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");
       
    function checkHL7v2Tables() {
      $dshl7 = CSQLDataSource::get("hl7v2", true);
    
      if (!$dshl7 || !$dshl7->loadTable("table_entry")) {
        CAppUI::setMsg("CHL7v2Tables-missing", UI_MSG_ERROR);
        return false;
      }
      
      return true;
    }
    $this->addFunction("checkHL7v2Tables");
       
    $this->makeRevision("0.02");
  
    $sql = "ALTER TABLE `table_description` 
              ADD `user` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql, false, "hl7v2");
    
    // Gestion du mode de placement en psychiatrie
    $query = "INSERT INTO `hl7v2`.`table_description` (
              `table_description_id`, `number`, `description`, `user`
              ) VALUES (
                NULL , '9000', 'Admit Reason (Psychiatrie)', '1'
              );";
    $this->addQuery($query, false, "hl7v2");
    
    $this->mod_version = "0.03";
  }
  
  
}

?>