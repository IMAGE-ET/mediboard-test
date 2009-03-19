<?php /* $Id: */
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsip extends CSetup {
  
  function __construct() {
      parent::__construct();
    
      $this->mod_name = "sip";
      $this->makeRevision("all");
      
      $this->makeRevision("0.11");
      
      $sql = "CREATE TABLE `cip` (
                `cip_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `client_id` VARCHAR (255) NOT NULL,
                `tag` VARCHAR (255) NOT NULL,
                `url` VARCHAR (255) NOT NULL,
                `login` VARCHAR (255) NOT NULL,
                `password` VARCHAR (255) NOT NULL
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
     
      $this->mod_version = "0.12";
  }
}
?>