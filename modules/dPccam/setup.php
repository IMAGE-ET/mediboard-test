<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPccam extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPccam";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `ccamfavoris` (
      `favoris_id` bigint(20) NOT NULL auto_increment,
      `favoris_user` int(11) NOT NULL default '0',
      `favoris_code` varchar(7) NOT NULL default '',
      PRIMARY KEY  (`favoris_id`)
      ) TYPE=MyISAM COMMENT='table des favoris'";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `ccamfavoris` 
			CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `ccamfavoris`
			ADD `object_class` VARCHAR(25) NOT NULL DEFAULT 'COperation';";
    $this->addQuery($query);
    
    $this->mod_version = "0.12";    

    // Data source query
    $query = "SELECT *
			FROM `codes_ngap`
			WHERE code = 'FSD' 
			AND tarif = '35'";
    $this->addDatasource("ccamV2", $query);
  }
}
?>