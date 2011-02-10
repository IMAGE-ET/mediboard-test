<?php 
/**
 * Setup FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupftp extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "ftp";
    $this->makeRevision("all");
      
    $query = "CREATE TABLE IF NOT EXISTS `source_ftp` (
                `source_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `port` INT (11) DEFAULT '21',
                `timeout` INT (11) DEFAULT '90',
                `pasv` ENUM ('0','1') DEFAULT '0',
                `mode` ENUM ('FTP_ASCII','FTP_BINARY') DEFAULT 'FTP_ASCII',
                `fileprefix` VARCHAR (255),
                `fileextension` VARCHAR (255),
                `filenbroll` ENUM ('1','2','3','4'),
                `fileextension_write_end` VARCHAR (255),
                `counter` VARCHAR (255),
                `name` VARCHAR (255) NOT NULL,
                `host` TEXT NOT NULL,
                `user` VARCHAR (255),
                `password` VARCHAR (50)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
  
    $query = "ALTER TABLE `source_ftp` 
               ADD `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif';";
    $this->addQuery($query, true);
    
    $query = "ALTER TABLE `source_ftp` 
               ADD `type_echange` VARCHAR (255);";
    $this->addQuery($query, true);
    
    $this->makeRevision("0.01");
    
    $query = "CREATE TABLE `sender_ftp` (
                `sender_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sender_ftp` 
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
              
    $this->mod_version = "0.02";
  }
}
?>