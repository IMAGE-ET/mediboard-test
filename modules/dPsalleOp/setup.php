<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPsalleOp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPsalleOp";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `acte_ccam` (" .
            "\n`acte_id` INT NOT NULL ," .
            "\n`code_activite` VARCHAR( 2 ) NOT NULL ," .
            "\n`code_phase` VARCHAR( 1 ) NOT NULL ," .
            "\n`execution` DATETIME NOT NULL ," .
            "\n`modificateurs` VARCHAR( 4 ) ," .
            "\n`montant_depassement` FLOAT," .
            "\n`commentaire` TEXT," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`executant_id` INT NOT NULL ," .
            "\nPRIMARY KEY ( `acte_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `acte_ccam` ADD `code_acte` CHAR( 7 ) NOT NULL AFTER `acte_id`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `acte_ccam` " .
            "ADD UNIQUE (" .
              "`code_acte` ," .
              "`code_activite` ," .
              "`code_phase` ," .
              "`operation_id`)";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql =  "ALTER TABLE `acte_ccam` CHANGE `acte_id` `acte_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql =  "ALTER TABLE `acte_ccam` DROP INDEX `code_acte`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `acte_ccam` " .
               "\nCHANGE `acte_id` `acte_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `executant_id` `executant_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `code_activite` `code_activite` tinyint(2) unsigned zerofill NOT NULL," .
               "\nCHANGE `code_phase` `code_phase` tinyint(1) unsigned zerofill NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `acte_ccam` CHANGE `code_acte` `code_acte` varchar(7) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `code_activite` `code_activite` TINYINT(4) NOT NULL," .
        "\nCHANGE `code_phase` `code_phase` TINYINT(4) NOT NULL;";
    $this->addQuery($sql);

    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `operation_id` `subject_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nADD `subject_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `acte_ccam` SET `subject_class` = 'COperation';";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `subject_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nCHANGE `subject_class` `object_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `code_association` TINYINT(4)";
    $this->addQuery($sql);
    
    $this->makerevision("0.20");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `regle` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makerevision("0.21");
    $sql = "ALTER TABLE `acte_ccam`
              ADD INDEX ( `code_acte` ),
              ADD INDEX ( `code_activite` ),
              ADD INDEX ( `code_phase` ),
              ADD INDEX ( `object_id` ),
              ADD INDEX ( `executant_id` ),
              ADD INDEX ( `object_class` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `montant_base` FLOAT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `signe` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `acte_ccam`
						ADD `rembourse` ENUM('0','1'), 
						CHANGE `object_class` `object_class` ENUM('COperation','CSejour','CConsultation') NOT NULL;";
    $this->addQuery($sql);
		
		$this->makeRevision("0.25");
		$sql = "CREATE TABLE `daily_check_item` (
						  `daily_check_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `list_id` INT (11) UNSIGNED NOT NULL,
						  `item_type_id` INT (11) UNSIGNED NOT NULL,
						  `checked` ENUM ('0','1') NOT NULL
						) TYPE=MYISAM;";
		$this->addQuery($sql);
		$sql = "ALTER TABLE `daily_check_item` 
						  ADD INDEX (`list_id`),
						  ADD INDEX (`item_type_id`);";
		$this->addQuery($sql);
		
		$sql = "CREATE TABLE `daily_check_item_type` (
						  `daily_check_item_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `title` VARCHAR (255) NOT NULL,
						  `desc` TEXT,
						  `active` ENUM ('0','1') NOT NULL,
						  `group_id` INT (11) UNSIGNED NOT NULL
						) TYPE=MYISAM;";
		$this->addQuery($sql);
    $sql = "ALTER TABLE `daily_check_item_type` ADD INDEX (`group_id`)";
		$this->addQuery($sql);

    $sql = "CREATE TABLE `daily_check_list` (
						  `daily_check_list_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `date` DATE NOT NULL,
						  `room_id` INT (11) UNSIGNED NOT NULL,
						  `validator_id` INT (11) UNSIGNED
						) TYPE=MYISAM;";
    $this->addQuery($sql);
		$sql = "ALTER TABLE `daily_check_list` 
						  ADD INDEX (`date`),
						  ADD INDEX (`room_id`),
						  ADD INDEX (`validator_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `daily_check_item_type` 
			ADD `category_id` INT (11) UNSIGNED NOT NULL, 
			ADD INDEX (`category_id`);";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `daily_check_item_category` (
              `daily_check_item_category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `title` VARCHAR (255) NOT NULL,
              `desc` TEXT
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `acte_ccam` 
			ADD `charges_sup` ENUM ('0','1')";
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `daily_check_list` 
              CHANGE `room_id` `object_id` INT (11) UNSIGNED NOT NULL,
              ADD `object_class` VARCHAR(80) NOT NULL DEFAULT 'CSalle'";
    $this->addQuery($query);
    $query = "ALTER TABLE `daily_check_item_category` 
              ADD `target_class` VARCHAR(80) NOT NULL DEFAULT 'CSalle'";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `daily_check_list` ADD `comments` TEXT";
    $this->addQuery($query);
    
    $this->makerevision("0.30");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `regle_dh` ENUM('0','1') DEFAULT 0 AFTER `regle`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.31");
    $sql = "ALTER TABLE `daily_check_item_category` 
              ADD `type` ENUM ('preanesth','preop','postop');";
    $this->addQuery($sql);
   
    $sql = "ALTER TABLE `daily_check_item` 
              CHANGE `checked` `checked` ENUM ('0','1');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `daily_check_item_type` 
              ADD `attribute` ENUM ('normal','notrecommended','notapplicable'),
              CHANGE `group_id` `group_id` INT(11) UNSIGNED NULL";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `daily_check_list` 
              ADD `type` ENUM ('preanesth','preop','postop'),
              CHANGE `object_class` `object_class` ENUM ('CSalle','CBlocOperatoire','COperation') NOT NULL";
    $this->addQuery($sql);
    
    
    
    // Liste des points de check liste spécifiés par la HAS
    $categories = array(
      '01' => array('preanesth', 'Identité du patient', 
        array(
          array('le patient a décliné son nom, sinon, par défaut, autre moyen de vérification de son identité', 'normal'),
        ),
      ),
      
      '02' => array('preanesth', 'L\'intervention et site opératoire sont confirmés', 
        array(
          array('idéalement par le patient et dans tous les cas, par le dossier ou procédure spécifique', 'normal'),
          array('la documentation clinique et para clinique nécessaire est disponible en salle', 'normal'),
        ),
      ),
      
      '03' => array('preanesth', null, 
        array(
          array('Le mode d\'installation est connu de l\'équipe en salle, cohérent avec le site/intervention et non dangereuse pour le patient', 'notapplicable'),
        ),
      ),
      
      '04' => array('preanesth', 'Le matériel nécessaire pour l\'intervention est vérifié', 
        array(
          array('pour la partie chirurgicale', 'normal'),
          array('pour la partie anesthésique', 'normal'),
        ),
      ),
      
      '05' => array('preanesth', 'Vérification croisée par l\'équipe de points critiques et des mesures adéquates à prendre', 
        array(
          array('allergie du patient', 'normal'),
          array('risque d\'inhalation, de difficulté d\'intubation ou de ventilation au masque', 'normal'),
          array('risque de saignement important', 'normal'),
        ),
      ),
      
      '06' => array('preop', 'Vérification « ultime » croisée au sein de l\'équipe', 
        array(
          array('identité patient correcte', 'normal'),
          array('intervention prévue confirmée', 'normal'),
          array('site opératoire correct', 'normal'),
          array('installation correcte', 'normal'),
          array('documents nécessaires disponibles', 'notapplicable'),
        ),
      ),
      
      '07' => array('preop', 'Partage des informations essentielles dans l\'équipe sur des éléments à risque / points critiques de l\'intervention', 
        array(
          array('sur le plan chirurgical (temps opératoire difficile, points spécifiques de l\'intervention, etc.)', 'normal'),
          array('sur le plan anesthésique (risques potentiels liés au terrain ou à des traitements éventuellement maintenus)', 'normal'),
        ),
      ),
      
      '08' => array('preop', null, 
        array(
          array('Antibioprophylaxie effectuée', 'notrecommended'),
        ),
      ),
      
      '09' => array('postop', 'Confirmation orale par le personnel auprès de l\'équipe', 
        array(
          array('de l\'intervention enregistrée', 'normal'),
          array('du compte final correct des compresses, aiguilles, instruments, etc.', 'notapplicable'),
          array('de l\'étiquetage des prélèvements, pièces opératoires, etc.', 'notapplicable'),
          array('du signalement de dysfonctionnements matériels et des événements indésirables', 'normal'),
        ),
      ),
      
      '10' => array('postop', null, 
        array(
          array('Les prescriptions pour les suites opératoires immédiates sont faites de manière conjointe', 'notrecommended'),
        ),
      ),
    );
    
    foreach($categories as $title => $cat) {
      $query = $this->ds->prepare("INSERT INTO `daily_check_item_category` (`title`, `desc`, `target_class`, `type`) VALUES 
                               (%1, %2, 'COperation', %3)", $title, $cat[1], $cat[0]);
      $this->addQuery($query);
      
      foreach($cat[2] as $type) {
        $query = $this->ds->prepare("INSERT INTO `daily_check_item_type` (`title`, `active`, `attribute`, `category_id`) VALUES
                    (%1, '1', %2, (SELECT `daily_check_item_category_id` FROM `daily_check_item_category` WHERE `title` = %3 AND `target_class` = 'COperation'))", 
                     $type[0], $type[1], $title);
        $this->addQuery($query);
      }
    }
    
    $this->makeRevision("0.32");
    $sql = "ALTER TABLE `acte_ccam` 
              ADD INDEX (`execution`);";
    $this->addQuery($sql);

    $this->makeRevision("0.33");
		$sql = "CREATE TABLE `anesth_perop` (
              `anesth_perop_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `operation_id` INT (11) UNSIGNED NOT NULL,
              `libelle` VARCHAR (255) NOT NULL,
              `datetime` DATETIME NOT NULL
             ) TYPE=MYISAM;";
		$this->addQuery($sql);
		
		$sql = "ALTER TABLE `anesth_perop` 
              ADD INDEX (`operation_id`),
              ADD INDEX (`datetime`);";
		$this->addQuery($sql);
		
    $this->mod_version = "0.34";
  }
}
?>