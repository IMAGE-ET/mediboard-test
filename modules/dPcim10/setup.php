<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPcim10 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcim10";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `cim10favoris` (
      `favoris_id` bigint(20) NOT NULL auto_increment,
      `favoris_user` int(11) NOT NULL default '0',
      `favoris_code` varchar(16) NOT NULL default '',
      PRIMARY KEY  (`favoris_id`)
      ) TYPE=MyISAM COMMENT='table des favoris cim10'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `cim10favoris` 
			CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);

    $this->mod_version = "0.11";

    // Data source query
    $query = "SELECT * 
			FROM `master` 
			WHERE `SID` = '19550'";
    $this->addDatasource("cim10", $query);
  }
}
?>