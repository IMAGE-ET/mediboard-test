<?php 
/**
 * Setup EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

 /**
 * Class CSetupeai
 * Setup EAI
 */

class CSetupeai extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "eai";
    $this->makeRevision("all");
    $this->makeRevision("0.01");
    
    $sql = "CREATE TABLE `message_supported` (
              `message_supported_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` VARCHAR (80) NOT NULL,
              `message` VARCHAR (255) NOT NULL,
              `active` ENUM ('0','1') DEFAULT '0'
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `message_supported` 
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.02");
    
    $sql = "CREATE TABLE `echange_any` (
              `echange_any_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `date_production` DATETIME NOT NULL,
              `emetteur_id` INT (11) NOT NULL,
              `destinataire_id` INT (11) NOT NULL,
              `type` CHAR (100),
              `sous_type` CHAR (100),
              `date_echange` DATETIME,
              `message_content_id` INT (11) UNSIGNED,
              `acquittement_content_id` INT (11) UNSIGNED,
              `statut_acquittement` CHAR (20),
              `message_valide` ENUM ('0','1') DEFAULT '0',
              `acquittement_valide` ENUM ('0','1') DEFAULT '0',
              `id_permanent` CHAR (50),
              `object_id` INT (11) UNSIGNED,
              `object_class` CHAR (80)
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_any` 
              ADD INDEX (`group_id`),
              ADD INDEX (`date_production`),
              ADD INDEX (`date_echange`),
              ADD INDEX (`message_content_id`),
              ADD INDEX (`acquittement_content_id`),
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `echange_any` 
                CHANGE `destinataire_id` `receiver_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.04");
    
    $query = "ALTER TABLE `echange_any` 
                CHANGE `emetteur_id` `sender_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    
    $query = "ALTER TABLE `echange_any` 
                ADD `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderMLLP');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `echange_any` 
                ADD INDEX (`sender_id`),
                ADD INDEX (`receiver_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.06");
    
    $query = "ALTER TABLE `echange_any` 
                CHANGE `sender_class` `sender_class` VARCHAR (80);";
    $this->addQuery($query);
    
    $this->makeRevision("0.07");
    
    $query = "ALTER TABLE `echange_any` 
                ADD `reprocess` TINYINT (4) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);
    
    $this->mod_version = "0.08";
  }
}

?>