<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Setup du module
**/
class CSetupdPfacturation extends CSetup {
  
  /**
   * Construct
  **/
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPfacturation";
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `facture` (
       `facture_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
       `date` DATE NOT NULL, 
       `sejour_id` INT(11) UNSIGNED NOT NULL, 
      PRIMARY KEY (`facture_id`)) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);
      
     $query = "CREATE TABLE `factureitem` (
         `factureitem_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
         `facture_id` INT(11) UNSIGNED NOT NULL, 
         `libelle` TEXT NOT NULL, 
         `prix_ht` FLOAT NOT NULL, 
         `taxe` FLOAT, 
      PRIMARY KEY (`factureitem_id`)) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);   
     
     $this->makeRevision("0.10");
     $query = "ALTER TABLE `facture` ADD `prix` FLOAT NOT NULL";
     $this->addQuery($query);
     
     $this->makeRevision("0.11");
     $query = "ALTER TABLE `facture` 
              ADD INDEX (`date`),
              ADD INDEX (`sejour_id`);";
     $this->addQuery($query);
     $query = "ALTER TABLE `factureitem` 
              ADD INDEX (`facture_id`);";
     $this->addQuery($query);
  
     $this->makeRevision("0.12");
     $query = "CREATE TABLE `facturecatalogueitem` (
              `facturecatalogueitem_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `libelle` TEXT NOT NULL,
              `prix_ht` DECIMAL (10,3) NOT NULL,
              `taxe` FLOAT NOT NULL,
              `type` ENUM ('produit','service')
     ) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);
     
     $this->makeRevision("0.13");
     $query = "ALTER TABLE `factureitem` 
                ADD `facture_catalogue_item_id` INT (11) UNSIGNED NOT NULL,
                ADD `reduction` DECIMAL (10,3);";
     $this->addQuery($query);
     
     $query = "ALTER TABLE `factureitem` 
                 ADD INDEX (`facture_catalogue_item_id`);";
     $this->addQuery($query);
     
     $this->makeRevision("0.14");
     
    $query = "CREATE TABLE `facture_etablissement` (
              `facture_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `dialyse` ENUM ('0','1') DEFAULT '0',
              `rques_assurance_maladie` TEXT,
              `rques_assurance_accident` TEXT,
              `patient_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `praticien_id` INT (11) UNSIGNED,
              `remise` DECIMAL (10,3) DEFAULT '0',
              `ouverture` DATE NOT NULL,
              `cloture` DATE,
              `du_patient` DECIMAL (10,3) NOT NULL DEFAULT '0',
              `du_tiers` DECIMAL (10,3) NOT NULL DEFAULT '0',
              `type_facture` ENUM ('maladie','accident') NOT NULL DEFAULT 'maladie',
              `patient_date_reglement` DATE,
              `tiers_date_reglement` DATE,
              `npq` ENUM ('0','1') NOT NULL DEFAULT '0',
              `cession_creance` ENUM ('0','1') NOT NULL DEFAULT '0',
              `assurance_maladie` INT (11) UNSIGNED,
              `assurance_accident` INT (11) UNSIGNED,
              `send_assur_base` ENUM ('0','1') DEFAULT '0',
              `send_assur_compl` ENUM ('0','1') DEFAULT '0',
              `facture` ENUM ('-1','0','1') NOT NULL DEFAULT '0',
              `ref_accident` TEXT,
              `statut_pro` ENUM ('chomeur','etudiant','non_travailleur','independant','salarie','sans_emploi'),
              `num_reference` VARCHAR (27),
              `envoi_xml` ENUM ('0','1') DEFAULT '1'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `facture_etablissement` 
              ADD INDEX (`patient_id`),
              ADD INDEX (`praticien_id`),
              ADD INDEX (`ouverture`),
              ADD INDEX (`cloture`),
              ADD INDEX (`patient_date_reglement`),
              ADD INDEX (`tiers_date_reglement`),
              ADD INDEX (`assurance_maladie`),
              ADD INDEX (`assurance_accident`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    
    $query = "CREATE TABLE `facture_liaison` (
              `facture_liaison_id` INT (11) NOT NULL auto_increment PRIMARY KEY,
              `facture_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `facture_class` ENUM ('CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CFactureCabinet',
              `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `object_class` VARCHAR (80) NOT NULL DEFAULT 'CConsultation'
                ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `facture_liaison` 
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($query);
    $this->makeRevision("0.16");
    
    $query = "ALTER TABLE `factureitem` 
              CHANGE `facture_id` `object_id` INT (11) NOT NULL DEFAULT '0',
              CHANGE `prix_ht` `prix` DECIMAL (10,2) NOT NULL DEFAULT '0',
              ADD `object_class` VARCHAR (80) NOT NULL DEFAULT 'CFactureCabinet',
              ADD `date` DATE NOT NULL,
              ADD `code` TEXT NOT NULL,
              ADD `type` ENUM ('CActeNGAP','CFraisDivers','CActeCCAM','CActeTarmed','CActeCaisse') NOT NULL DEFAULT 'CActeCCAM',
              ADD `quantite` INT (11) NOT NULL DEFAULT '0',
              ADD `coeff` DECIMAL (10,2) NOT NULL DEFAULT '0',
              DROP `facture_catalogue_item_id`,
              DROP `taxe`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `factureitem` 
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `factureitem` 
              ADD INDEX (`date`);";
    $this->addQuery($query);
    $this->makeRevision("0.17");
    
    $query = "DROP TABLE facture;";
    $this->addQuery($query);
    
    $query = "DROP TABLE facturecatalogueitem;";
    $this->addQuery($query);
    $this->makeRevision("0.18");
    
    $query = "ALTER TABLE `facture_etablissement` 
              ADD `temporaire` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("0.19");
    
    $query = "ALTER TABLE `factureitem` 
              ADD `pm` DECIMAL (10,2),
              ADD `pt` DECIMAL (10,2),
              ADD `coeff_pm` DECIMAL (10,2),
              ADD `coeff_pt` DECIMAL (10,2);";
    $this->addQuery($query);
    $this->makeRevision("0.20");
    
    $query = "ALTER TABLE `factureitem` 
              ADD `use_tarmed_bill` ENUM ('0','1') DEFAULT '0',
              CHANGE `prix` `montant_base` DECIMAL (10,2) NOT NULL DEFAULT '0',
              ADD `montant_depassement` DECIMAL (10,2) DEFAULT '0' AFTER montant_base,
              ADD `code_ref` TEXT,
              ADD `code_caisse` TEXT;";
    $this->addQuery($query);

    /*
     * Pour créer automatiquement les droits des utilisateurs sur le module fse,
     * on a besoin de récupérer l'id du module Facturation dans la table modules.
     * Mais l'entrée correspondante n'est créée qu'à la fin du setup.
     * On doit donc faire le setup en 2 fois.
     */
    if (count($this->ds->loadList("SELECT * FROM modules WHERE mod_name = 'dPfacturation'")) == 0) {
      $this->mod_version = "0.21";
      return;
    }

    $this->makeRevision("0.21");

     //Ecrit les droits utilisateurs sur le module facturation
    $query = "INSERT INTO `perm_module` (`user_id`, `mod_id`, `permission`, `view`)
              SELECT u.user_id, m.mod_id, 2, 0
              FROM perm_module AS p, modules AS m, modules AS n, users AS u
              WHERE m.mod_name = 'dPfacturation'
              AND u.template = '1'
              AND n.mod_name = 'dPcabinet'
              AND p.mod_id = n.mod_id
              AND p.permission = '2'
              AND p.user_id = u.user_id
              AND NOT EXISTS (
                SELECT * FROM perm_module AS o
                WHERE o.user_id = u.user_id
                AND o.mod_id = m.mod_id
                AND p.permission = '2'
              );";
    $this->addQuery($query);
    $this->makeRevision("0.22");
    
    $query = "CREATE TABLE `facture_relance` (
              `relance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_class` ENUM ('CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CFactureCabinet',
              `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `date` DATE,
              `etat` ENUM ('emise','regle','renouvelle') NOT NULL DEFAULT 'emise',
              `du_patient` DECIMAL (10,2),
              `du_tiers` DECIMAL (10,2),
              `numero` TINYINT (4) UNSIGNED NOT NULL DEFAULT '1'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `facture_relance` 
              ADD INDEX (`object_id`),
              ADD INDEX (`date`);";
    $this->addQuery($query);
    $this->makeRevision("0.23");
    
    $query = "ALTER TABLE `facture_liaison` 
                CHANGE `facture_id` `facture_id` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `facture_liaison` 
                ADD INDEX (`facture_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.24");
    
    $query = "ALTER TABLE `facture_etablissement` 
                CHANGE `statut_pro` `statut_pro` ENUM ('chomeur','etudiant','non_travailleur','independant','invalide','militaire','retraite','salarie_fr','salarie_sw','sans_emploi');";
    $this->addQuery($query);
    $this->makeRevision("0.25");
    
    $query = "CREATE TABLE `retrocession` (
                `retrocession_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `praticien_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `nom` VARCHAR (255) NOT NULL,
                `type` ENUM ('montant','pct','autre') DEFAULT 'montant',
                `valeur` DECIMAL (10,2),
                `pct_pm` FLOAT DEFAULT '0',
                `pct_pt` FLOAT DEFAULT '0',
                `code_class` ENUM ('CActeCCAM','CActeNAGP','CActeTarmed','CActeCaisse') DEFAULT 'CActeCCAM',
                `code` VARCHAR (255)
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `retrocession` 
                ADD INDEX (`praticien_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.26");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `annule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `factureitem`
                ADD `seance` INT (11);";
    $this->addQuery($query);
    $this->makeRevision("0.27");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `definitive` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("0.28");

    $query = "ALTER TABLE `retrocession`
                ADD `use_pm` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("0.29");

    $query = "ALTER TABLE `facture_relance`
                ADD `statut` ENUM ('inactive','first','second','third','contentieux','poursuite'),
                ADD `poursuite` ENUM ('defaut','continuation','etranger','faillite','hors_pays','deces','inactive','saisie','introuvable');";
    $this->addQuery($query);
    $this->makeRevision("0.30");

    $query = "ALTER TABLE `retrocession`
                ADD `active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    $this->makeRevision("0.31");

    $query = "ALTER TABLE `facture_etablissement`
                CHANGE `type_facture` `type_facture` ENUM ('maladie','accident','esthetique') NOT NULL DEFAULT 'maladie';";
    $this->addQuery($query);
    $this->makeRevision("0.32");

    $query = "CREATE TABLE `debiteur` (
                `debiteur_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `numero` INT (11) NOT NULL DEFAULT '0',
                `nom` VARCHAR (50) NOT NULL,
                `description` VARCHAR (255)
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $this->makeRevision("0.33");

    $query = "ALTER TABLE `factureitem`
                ADD `forfait` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("0.34");

    $query = "ALTER TABLE `factureitem`
                DROP `forfait`;";
    $this->addQuery($query);
    $this->makeRevision("0.35");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `numero` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $this->makeRevision("0.36");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `du_tva` DECIMAL (10,2) DEFAULT '0',
                ADD `taux_tva` ENUM ('0', '19.6');";
    $this->addQuery($query);
    $this->makeRevision("0.37");

    $query = "CREATE TABLE `facture_journal` (
                `journal_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` TEXT NOT NULL,
                `type` ENUM ('paiement','debiteur','rappel','checklist'),
                `checklist_id` INT (11) UNSIGNED
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `facture_journal`
                ADD INDEX (`checklist_id`);";
    $this->addQuery($query);

    $query = "CREATE TABLE `facture_link_journal` (
                `journal_liaison_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_class` ENUM ('CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CFactureCabinet',
                `journal_id` INT (11) UNSIGNED NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `facture_link_journal`
                ADD INDEX (`object_id`),
                ADD INDEX (`journal_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.38");
    $this->addDependency("dPcabinet", "1.87");

    $query = "INSERT INTO `facture_liaison` (`facture_id`, `facture_class`, `object_id`, `object_class`)
                SELECT f.facture_id, 'CFactureCabinet', c.consultation_id, 'CConsultation'
                FROM `facture_cabinet` f, `consultation` c
                WHERE c.facture_id IS NOT NULL
                AND c.valide = '1'
                AND f.facture_id = c.facture_id
                AND NOT EXISTS (
                  SELECT * FROM facture_liaison AS fl
                  WHERE fl.facture_id = c.facture_id
                  AND fl.facture_class = 'CFactureCabinet'
                  AND fl.object_class = 'CConsultation'
                  AND fl.object_id = c.consultation_id
                )
                GROUP BY f.facture_id;";
    $this->addQuery($query);
    $this->makeRevision("0.39");

    $query = "CREATE TABLE `facture_echeance` (
                `echeance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_class` ENUM ('CFactureCabinet','CFactureEtablissement') NOT NULL DEFAULT 'CFactureCabinet',
                `date` DATE NOT NULL,
                `montant` DECIMAL (10,2) NOT NULL DEFAULT '0',
                `description` TEXT
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `facture_echeance`
                ADD INDEX (`object_id`),
                ADD INDEX (`date`);";
    $this->addQuery($query);
    $this->makeRevision("0.40");

    $query = "ALTER TABLE `facture_etablissement`
                CHANGE `taux_tva` `taux_tva` FLOAT DEFAULT '0';";
    $this->addQuery($query);
    $this->makeRevision("0.41");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `facture_etablissement`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.42");

    //Facture d'établissement de séjour
    $query = "UPDATE facture_etablissement, facture_liaison, sejour
          SET facture_etablissement.group_id = sejour.group_id
          WHERE facture_liaison.object_class = 'CSejour'
          AND facture_liaison.object_id = sejour.sejour_id
          AND facture_liaison.facture_id = facture_etablissement.facture_id
          AND facture_liaison.facture_class = 'CFactureEtablissement'";
    $this->addQuery($query);

    //Facture d'établissement de consultation de séjour
    $query = "UPDATE facture_etablissement, facture_liaison, sejour, consultation
          SET facture_etablissement.group_id = sejour.group_id
          WHERE facture_liaison.object_class = 'CConsultation'
          AND facture_liaison.object_id = consultation.consultation_id
          AND facture_liaison.facture_id = facture_etablissement.facture_id
          AND facture_liaison.facture_class = 'CFactureEtablissement'
          AND consultation.sejour_id = sejour.sejour_id";
    $this->addQuery($query);

    $query = "UPDATE facture_etablissement,users_mediboard, functions_mediboard
          SET facture_etablissement.group_id = functions_mediboard.group_id
          WHERE facture_etablissement.group_id = '0'
          AND facture_etablissement.praticien_id = users_mediboard.user_id
          AND functions_mediboard.function_id = users_mediboard.function_id";
    $this->addQuery($query);
    $this->makeRevision("0.43");

    $query = "ALTER TABLE `factureitem`
                ADD `cote` ENUM ('left','right');";
    $this->addQuery($query);
    $this->makeRevision("0.44");

    $query = "ALTER TABLE `facture_etablissement`
                ADD `date_cas` DATETIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `facture_etablissement`
                ADD INDEX (`date_cas`);";
    $this->addQuery($query);
    $this->mod_version = "0.45";
  }
}
