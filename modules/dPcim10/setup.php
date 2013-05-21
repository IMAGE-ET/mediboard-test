<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

class CSetupdPcim10 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcim10";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `cim10favoris` (
      `favoris_id` bigint(20) NOT NULL auto_increment,
      `favoris_user` int(11) NOT NULL default '0',
      `favoris_code` varchar(16) NOT NULL default '',
      PRIMARY KEY  (`favoris_id`)
      ) /*! ENGINE=MyISAM */ COMMENT='table des favoris cim10'";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `cim10favoris` 
			CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `cim10favoris` 
              ADD INDEX (`favoris_user`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $this->addPrefQuery("new_search_cim10", "1");
    
    $this->mod_version = "0.13";
    
    // Data source query
    $query = "SELECT * 
			FROM `master` 
			WHERE `SID` = '19550'";
    $this->addDatasource("cim10", $query);
    
  }
}
