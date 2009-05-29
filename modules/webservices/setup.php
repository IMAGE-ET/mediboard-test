<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupwebservices extends CSetup {
  
  function __construct() {
      parent::__construct();
    
      $this->mod_name = "webservices";
      $this->makeRevision("all");
      
      $this->makeRevision("0.10");
     
      $sql = "CREATE TABLE `echange_soap` (
                `echange_soap_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `emetteur` VARCHAR (255),
                `destinataire` VARCHAR (255),
                `type` VARCHAR (255),
                `date_echange` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `web_service_name` VARCHAR (255),
                `function_name` VARCHAR (255) NOT NULL,
                `input` TEXT NOT NULL,
                `output` TEXT
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
     
      $this->mod_version = "0.11";
  }
}
?>