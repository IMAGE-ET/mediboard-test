<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPgestionCab";
$config["mod_version"]     = "0.14";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig( $config );
}

class CSetupdPgestionCab {

  function configure() {
    global $AppUI;
    $AppUI->redirect( "m=dPgestionCab&a=configure" );
    return true;
  }

  function remove() {
    db_exec( "DROP TABLE `gestioncab`;" ); db_error();
    db_exec( "DROP TABLE `rubrique_gestioncab`;" ); db_error();
    db_exec( "DROP TABLE `mode_paiement`;" ); db_error();
    db_exec( "DROP TABLE `params_paie`;" ); db_error();
    db_exec( "DROP TABLE `fiche_paie`;" ); db_error();
    return null;
  }

  function upgrade( $old_version ) {
    switch ( $old_version ) {
      case "all":
        $sql = "CREATE TABLE `gestioncab` (
                  `gestioncab_id` INT NOT NULL AUTO_INCREMENT ,
                  `function_id` INT NOT NULL ,
                  `libelle` VARCHAR( 50 ) DEFAULT 'inconnu' NOT NULL ,
                  `date` DATE NOT NULL ,
                  `rubrique_id` INT DEFAULT '0' NOT NULL ,
                  `montant` FLOAT DEFAULT '0' NOT NULL ,
                  `mode_paiement_id` INT DEFAULT '0' NOT NULL ,
                  `num_facture` INT NOT NULL ,
                  `rques` TEXT,
                  PRIMARY KEY ( `gestioncab_id` ) ,
                  INDEX ( `function_id` , `rubrique_id` , `mode_paiement_id` )
                ) TYPE=MyISAM COMMENT = 'Table des lignes de la comptabilité de cabinet';";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `rubrique_gestioncab` (
                  `rubrique_id` INT NOT NULL AUTO_INCREMENT ,
                  `function_id` INT DEFAULT '0' NOT NULL ,
                  `nom` VARCHAR( 30 ) DEFAULT 'divers' NOT NULL ,
                  PRIMARY KEY ( `rubrique_id` ) ,
                  INDEX ( `function_id` )
                ) TYPE=MyISAM COMMENT = 'Table des rubriques pour la gestion comptable de cabinet';";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `rubrique_gestioncab` ( `rubrique_id` , `function_id` , `nom` )
                VALUES ('', '0', 'divers');";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `mode_paiement` (
                  `mode_paiement_id` INT NOT NULL AUTO_INCREMENT ,
                  `function_id` INT DEFAULT '0' NOT NULL ,
                  `nom` VARCHAR( 30 ) DEFAULT 'inconnu' NOT NULL ,
                  PRIMARY KEY ( `mode_paiement_id` ) ,
                  INDEX ( `function_id` )
                ) TYPE=MyISAM COMMENT = 'Table des modes de règlement';";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
                VALUES ('', '0', 'Chèque');";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
                VALUES ('', '0', 'CB');";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
                VALUES ('', '0', 'Virement');";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
                VALUES ('', '0', 'Prélèvement');";
        db_exec( $sql ); db_error();
        $sql = "INSERT INTO `mode_paiement` ( `mode_paiement_id` , `function_id` , `nom` )
                VALUES ('', '0', 'TIP');";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `params_paie` (
                  `params_paie_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `user_id` BIGINT NOT NULL ,
                  `smic` FLOAT NOT NULL ,
                  `csgds` FLOAT NOT NULL ,
                  `csgnds` FLOAT NOT NULL ,
                  `ssms` FLOAT NOT NULL ,
                  `ssmp` FLOAT NOT NULL ,
                  `ssvs` FLOAT NOT NULL ,
                  `ssvp` FLOAT NOT NULL ,
                  `rcs` FLOAT NOT NULL ,
                  `rcp` FLOAT NOT NULL ,
                  `agffs` FLOAT NOT NULL ,
                  `agffp` FLOAT NOT NULL ,
                  `aps` FLOAT NOT NULL ,
                  `app` FLOAT NOT NULL ,
                  `acs` FLOAT NOT NULL ,
                  `acp` FLOAT NOT NULL ,
                  `aatp` FLOAT NOT NULL ,
                  `nom` VARCHAR(100) NOT NULL ,
                  `adresse` VARCHAR(50) NOT NULL ,
                  `cp` VARCHAR(5) NOT NULL ,
                  `ville` VARCHAR(50) NOT NULL ,
                  `siret` VARCHAR(14) NOT NULL ,
                  `ape` VARCHAR(4) NOT NULL ,
                  PRIMARY KEY ( `params_paie_id` ) ,
                  INDEX ( `user_id` )
                ) TYPE=MyISAM COMMENT = 'Paramètres fiscaux pour les fiches de paie';";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `fiche_paie` (
                  `fiche_paie_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `params_paie_id` BIGINT NOT NULL ,
                  `debut` DATE NOT NULL ,
                  `fin` DATE NOT NULL ,
                  `salaire` FLOAT NOT NULL ,
                  `heures` SMALLINT NOT NULL ,
                  `heures_sup` SMALLINT NOT NULL ,
                  `mutuelle` FLOAT NOT NULL ,
                  `precarite` FLOAT NOT NULL ,
                  `anciennete` FLOAT NOT NULL ,
                  PRIMARY KEY ( `fiche_paie_id` ) ,
                  INDEX ( `params_paie_id` )
                ) TYPE=MyISAM COMMENT = 'Table des fiches de paie';";
        db_exec( $sql ); db_error();
      case "0.1":
        $sql = "ALTER TABLE fiche_paie" .
            "\nADD `conges_payes` FLOAT NOT NULL;";
        db_exec( $sql ); db_error();
  
      case "0.11":
        $sql = "ALTER TABLE fiche_paie" .
            "\nADD `prime_speciale` FLOAT NOT NULL";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE params_paie" .
            "\nADD `matricule` VARCHAR(15)";
        db_exec( $sql ); db_error();
      case "0.12":
        $module = @CModule::getInstalled("mediusers");
        if (!$module || $module->mod_version < "0.1") {
          return "0.12";
        }
        $sql = "CREATE TABLE `employecab` (" .
            "\n`employecab_id` INT NOT NULL AUTO_INCREMENT," .
            "\n`function_id` INT NOT NULL DEFAULT '0'," .
            "\n`nom` VARCHAR( 50 ) NOT NULL DEFAULT ''," .
            "\n`prenom` VARCHAR( 50 ) NOT NULL DEFAULT ''," .
            "\n`function` VARCHAR( 50 ) NOT NULL DEFAULT ''," .
            "\n`adresse` VARCHAR( 50 )," .
            "\n`cp` VARCHAR( 5 )," .
            "\n`ville` VARCHAR( 50 )," .
            "PRIMARY KEY ( `employecab_id` ) ," .
            "INDEX ( `function_id` )" .
            ") TYPE=MyISAM COMMENT = 'Table des employes';";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `params_paie` CHANGE `user_id` `employecab_id` INT NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        $param = new CParamsPaie;
        $params = $param->loadList();
        foreach($params as $key => $curr_param) {
          $user = new CMediusers;
          $user->load($params[$key]->employecab_id);
          $employe = new CEmployeCab;
          $employe->function_id = $user->function_id;
          $employe->nom         = $user->_user_last_name;
          $employe->prenom      = $user->_user_first_name;
          $employe->function    = $user->_user_type;
          $employe->adresse     = $user->_user_adresse;
          $employe->cp          = $user->_user_cp;
          $employe->ville       = $user->_user_ville;
          $employe->store();
          $params[$key]->employecab_id = $employe->employecab_id;
          $params[$key]->store();
        }
      case "0.13":
        $sql = "ALTER TABLE `employecab` " .
               "\nCHANGE `employecab_id` `employecab_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
               "\nCHANGE `function` `function` varchar(255) NOT NULL," .
               "\nCHANGE `adresse` `adresse` varchar(255) NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `fiche_paie` " .
               "\nCHANGE `fiche_paie_id` `fiche_paie_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `params_paie_id` `params_paie_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `heures` `heures` tinyint(4) NOT NULL DEFAULT '0'," .
               "\nCHANGE `heures_sup` `heures_sup` tinyint(4) NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `gestioncab` " .
               "\nCHANGE `gestioncab_id` `gestioncab_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `libelle` `libelle` varchar(255) NOT NULL DEFAULT 'inconnu'," .
               "\nCHANGE `rubrique_id` `rubrique_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `mode_paiement_id` `mode_paiement_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `mode_paiement` " .
               "\nCHANGE `mode_paiement_id` `mode_paiement_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL DEFAULT 'inconnu';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `params_paie` " .
               "\nCHANGE `params_paie_id` `params_paie_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `employecab_id` `employecab_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `adresse` `adresse` varchar(255) NOT NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NOT NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NOT NULL," .
               "\nCHANGE `siret` `siret` bigint(14) unsigned zerofill NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `rubrique_gestioncab` " .
               "\nCHANGE `rubrique_id` `rubrique_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL DEFAULT 'divers';";
        db_exec( $sql ); db_error();
      case "0.14":
        return "0.14";
    }
    return false;
  }
}

?>