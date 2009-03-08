<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Romain Ollvier
 */

class CSetupmessagerie extends CSetup {

    function __construct() {
        parent::__construct();

        $this->mod_name = "messagerie";
        $this->makeRevision("all");

        $sql = "CREATE TABLE `mbmail` (
                  `mbmail_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                  `from` INT (11) UNSIGNED NOT NULL,
                  `to` INT (11) UNSIGNED NOT NULL,
                  `subject` VARCHAR (255) NOT NULL,
                  `source` MEDIUMTEXT NOT NULL,
                  `date_sent` DATETIME,
                  `date_read` DATETIME,
                  `date_archived` DATETIME,
                  `starred` ENUM ('0','1') NOT NULL DEFAULT '0'
                ) TYPE=MYISAM;";
        $this->addQuery($sql);
        
        $sql = "ALTER TABLE `mbmail` 
                  ADD INDEX (`from`),
                  ADD INDEX (`to`),
                  ADD INDEX (`date_sent`),
                  ADD INDEX (`date_read`),
                  ADD INDEX (`date_archived`)
                ;";
        $this->addQuery($sql);
        
        $this->mod_version = "0.10";
    }
}
?>
