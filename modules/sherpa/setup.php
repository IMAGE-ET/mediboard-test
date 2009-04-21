<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author sherpa
 */

class CSetupsherpa extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "sherpa";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `t_malade` (" .
        "\n `malade_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT," .
        "\n `malnum` INT(6) UNSIGNED ZEROFILL," .
        "\n `malnom` CHAR(20)," .
        "\n `malpre` CHAR(10)," .
        "\n `datnai` INT(8) UNSIGNED ZEROFILL," .
        "\nPRIMARY KEY (`malade_id`)) TYPE=MYISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.10");
    $sql = "CREATE TABLE `sp_etablissement` (" .
        "\n `sp_etab_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT," .
        "\n `group_id` INT(11) UNSIGNED NOT NULL," .
        "\n `increment_year` TINYINT(1) UNSIGNED ZEROFILL," .
        "\n `increment_patient` INT," .
        "\nPRIMARY KEY (`sp_etab_id`)) TYPE=MYISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `t_malade` " .
        "\nDROP `malade_id`," .
        "\nCHANGE `malnum` `malnum` INT( 6 ) UNSIGNED ZEROFILL NOT NULL DEFAULT '0'," .
        "\nADD PRIMARY KEY ( `malnum` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `t_malade` " .
        "\nCHANGE `malnom` `malnom` VARCHAR(20), " .
        "\nCHANGE `malpre` `malpre` VARCHAR(10), " .
        "\nCHANGE `datnai` `datnai` CHAR(10);";
    $this->addQuery($sql);

    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `t_malade` ".
      "\nCHANGE `malnom` `malnom` VARCHAR(50),".
      "\nCHANGE `malpre` `malpre` VARCHAR(30),".
      "\nCHANGE `datnai` `datnai` VARCHAR(10),".
      "\nADD `datmaj` CHAR(19);";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `t_dossier` (".
      "\n`numdos` INT( 6 ) UNSIGNED ZEROFILL NOT NULL DEFAULT '0',".
      "\n`malnum` INT(6) UNSIGNED ZEROFILL,".
      "\n`anndos` VARCHAR(2),".
      "\n`datmaj` CHAR(19),".
      "PRIMARY KEY (`numdos`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `t_ouvdro` (".
      "\n`numdos` INT( 6 ) UNSIGNED ZEROFILL NOT NULL DEFAULT '0',".
      "\n`malnum` INT(6) UNSIGNED ZEROFILL,".
      "\n`datmaj` CHAR(19),".
      "\nPRIMARY KEY (`numdos`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `t_sejmed` (".
      "\n`numdos` INT( 6 ) UNSIGNED ZEROFILL NOT NULL DEFAULT '0',".
      "\n`malnum` INT(6) UNSIGNED ZEROFILL,".
      "\n`datent` CHAR(10),".
      "\n`litcod` CHAR(4),".
      "\n`sercod` CHAR(2),". 
      "\n`pracod` CHAR(3),". 
      "\n`datsor` CHAR(10),". 
      "\n`depart` ENUM('D','T','S','E','R','P'),".
      "\nPRIMARY KEY (`numdos`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `t_malade`".
			"CHANGE `malnum` `malnum` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
			"ADD `malpat` VARCHAR(50),".
			"ADD `vilnai` VARCHAR(30),".
			"ADD `nation` VARCHAR(03),".
			"ADD `sexe` CHAR(1),".
			"ADD `malnss` VARCHAR(13),". 
			"ADD `clenss` VARCHAR(02),".
			"ADD `parent` VARCHAR(02),".
			"ADD `malru1` VARCHAR(25),".
			"ADD `malru2` VARCHAR(25),".
			"ADD `malpos` VARCHAR(05),".
			"ADD `malvil` VARCHAR(25),".
			"ADD `maltel` VARCHAR(14),".
			"ADD `malpro` VARCHAR(30),".
			"ADD `perso1` VARCHAR(30),".
			"ADD `prvad1` VARCHAR(25),".
			"ADD `prvil1` VARCHAR(30),".
			"ADD `prtel1` VARCHAR(14),".
			"ADD `malie1` VARCHAR(20),".
			"ADD `perso2` VARCHAR(30),".
			"ADD `prvad2` VARCHAR(25),".
			"ADD `prvil2` VARCHAR(30),".
			"ADD `prtel2` VARCHAR(14),".
			"ADD `malie2` VARCHAR(20),".
			"ADD `assnss` VARCHAR(13),".
			"ADD `nsscle` VARCHAR(02),".
			"ADD `assnom` VARCHAR(50),".
			"ADD `asspre` VARCHAR(30),".
			"ADD `asspat` VARCHAR(50),".
			"ADD `assru1` VARCHAR(25),".
			"ADD `assru2` VARCHAR(25),".
			"ADD `asspos` VARCHAR(05),".
			"ADD `assvil` VARCHAR(25);";
		$this->addQuery($sql);
		
		$this->mod_version = "0.16";
  }
}
?>