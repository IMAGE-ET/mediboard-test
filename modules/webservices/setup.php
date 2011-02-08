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
   
   $this->makeRevision("0.11");
   $sql = "ALTER TABLE `echange_soap` 
             ADD `soapfault` ENUM ('0','1') DEFAULT '0',
             ADD INDEX (`date_echange`);";
   $this->addQuery($sql);
   
   $this->makeRevision("0.12");
   $sql = "ALTER TABLE `echange_soap` 
            ADD `purge` ENUM ('0','1') DEFAULT '0';";
   $this->addQuery($sql); 
   
   $this->makeRevision("0.13");
   $sql = "ALTER TABLE `echange_soap` 
            CHANGE `input` `input` TEXT;";
   $this->addQuery($sql);
   
   $this->makeRevision("0.14");
   $sql = "ALTER TABLE `echange_soap` 
             ADD `response_time` FLOAT,
             ADD INDEX (`type`);";
   $this->addQuery($sql);
   
   $this->makeRevision("0.15");
   $sql = "ALTER TABLE `echange_soap` 
             ADD INDEX (`web_service_name`),
             ADD INDEX (`function_name`);";
   $this->addQuery($sql);
   
   $this->makeRevision("0.16");
   $sql = "ALTER TABLE `echange_soap` 
            ADD `trace` ENUM ('0','1') DEFAULT '0',
            ADD `last_request_headers` TEXT,
            ADD `last_response_headers` TEXT,
            ADD `last_request` MEDIUMTEXT,
            ADD `last_response` MEDIUMTEXT;";
   $this->addQuery($sql);
   
   $query = "CREATE TABLE IF NOT EXISTS `source_soap` (
              `source_soap_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `wsdl_mode` ENUM ('0','1') DEFAULT '1',
              `name` VARCHAR (255) NOT NULL,
              `host` TEXT NOT NULL,
              `user` VARCHAR (255),
              `password` VARCHAR (50)
            ) TYPE=MYISAM;";
    $this->addQuery($query);
   
    $this->makeRevision("0.17");

    $query = "ALTER TABLE `source_soap` 
                ADD `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif';";
    $this->addQuery($query, true);
    
    $query = "ALTER TABLE `source_soap` 
                ADD `evenement_name` VARCHAR (255),
                ADD `type_echange` VARCHAR (255);";
    $this->addQuery($query, true); 

    $query = "ALTER TABLE `source_soap` 
                ADD `single_parameter` VARCHAR (255);";
    $this->addQuery($query, true);
    
    $this->mod_version = "0.18";
  }
}
?>