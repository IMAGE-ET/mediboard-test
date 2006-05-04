<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dPplanningOp';
$config['mod_version'] = '0.35';
$config['mod_directory'] = 'dPplanningOp';
$config['mod_setup_class'] = 'CSetupdPplanningOp';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Planning Chir.';
$config['mod_ui_icon'] = 'dPplanningOp.png';
$config['mod_description'] = 'Gestion des plannings opératoires des chirurgiens';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupdPplanningOp {

	function configure() {
	global $AppUI;
		$AppUI->redirect( 'm=dPplanningOp&a=configure' );
  		return true;
	}

	function remove() {
		db_exec( "DROP TABLE operations;" );
		db_exec( "DELETE FROM sysval WHERE  sysval_title = 'AnesthType';" );
		return null;
	}

	function upgrade( $old_version ) {
	  switch ($old_version) 		{
        case "all":
          $sql = "INSERT INTO sysvals" .
            "\nVALUES ('', '1', 'AnesthType', '1|Rachi\n2|Rachi + bloc\n3|Anesthésie loco-régionnale\n4|Anesthésie locale\n5|Neurolept\n6|Anesthésie générale\n7|Anesthesie generale + bloc\n8|Anesthesie peribulbaire\n0|Non définie')";
          db_exec( $sql ); db_error();
		
		case "0.1":
          $sql = "ALTER TABLE operations " .
            "\nADD entree_bloc TIME AFTER temp_operation ," .
            "\nADD sortie_bloc TIME AFTER entree_bloc ," .
            "\nADD saisie ENUM( 'n', 'o' ) DEFAULT 'n' NOT NULL ," .
            "\nCHANGE plageop_id plageop_id BIGINT( 20 ) UNSIGNED";
          db_exec( $sql ); db_error();
        
        case "0.2":
      	  $sql = "ALTER TABLE `operations` " .
            "\nADD `convalescence` TEXT AFTER `materiel` ;";
          db_exec( $sql ); db_error();
        
        case "0.21":
          $sql = "ALTER TABLE `operations` " .
            "\nADD `depassement` INT( 4 );";
          db_exec( $sql ); db_error();

        case "0.22":
          $sql = "ALTER TABLE `operations` " .
            "\nADD `CCAM_code2` VARCHAR( 7 ) AFTER `CCAM_code`," .
            "\nADD INDEX ( `CCAM_code2` )," .
            "\nADD INDEX ( `CCAM_code` )," .
            "\nADD INDEX ( `pat_id` )," .
            "\nADD INDEX ( `chir_id` )," .
            "\nADD INDEX ( `plageop_id` );";
          db_exec( $sql ); db_error();

        case "0.23" :
          $sql = "ALTER TABLE `operations` " .
           "\nADD `modifiee` TINYINT DEFAULT '0' NOT NULL AFTER `saisie`," .
           "\nADD `annulee` TINYINT DEFAULT '0' NOT NULL ;";
          db_exec( $sql ); db_error();

        case "0.24" :
     	    $sql = "ALTER TABLE `operations` " .
            "\nADD `compte_rendu` TEXT," .
            "\nADD `cr_valide` TINYINT( 4 ) DEFAULT '0' NOT NULL ;";
          db_exec( $sql ); db_error();
      
        case "0.25" :
          $sql = "ALTER TABLE `operations` " .
            "\nADD `pathologie` VARCHAR( 8 ) DEFAULT NULL," .
            "\nADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
          db_exec($sql); db_error();
 
        case "0.26":
          $sql = "ALTER TABLE `operations` " .
            "\nADD `libelle` TEXT DEFAULT NULL AFTER `CCAM_code2` ;";
          db_exec($sql); db_error();
    
        case "0.27":
          $document_types = array (
            array ("name" => "compte_rendu", "valide" => "cr_valide"));
            
          set_time_limit( 1800 );
          
          foreach ($document_types as $document_type) {
            $document_name = $document_type["name"];
            $document_valide = $document_type["valide"];
    
            $sql = "SELECT *" .
                "\nFROM `operations`" .
                "\nWHERE `$document_name` IS NOT NULL" .
                "\nAND `$document_name` != ''";
            $res = db_exec( $sql );
    
            while ($obj = db_fetch_object($res)) {
              $document = new CCompteRendu;
              $document->type = "operation";
              $document->nom = $document_name;
              $document->object_id = $obj->operation_id;
              $document->source = $obj->$document_name;
              $document->valide = $obj->$document_valide;
              $document->store();
            }
          }
          
        case "0.28":
          $sql = "ALTER TABLE `operations` " .
            "\nADD `codes_ccam` VARCHAR( 160 ) AFTER `CIM10_code`";
          db_exec($sql); db_error();

          $sql = "ALTER TABLE `operations` " .
            "\nADD INDEX ( `codes_ccam` )";
          db_exec($sql); db_error();

        case "0.29":
          $sql = "SELECT `operation_id` , `CCAM_code` , `CCAM_code2`" .
            "\nFROM `operations`";
          
          $res = db_exec( $sql );
  
          while ($obj = db_fetch_object($res)) {
            $obj->codes_ccam = $obj->CCAM_code;
            if ($obj->CCAM_code2) {
            	$obj->codes_ccam .= "|$obj->CCAM_code2";
            }
              
            $sql2 = "UPDATE `operations` " .
              "\nSET `codes_ccam` = '$obj->codes_ccam' " .
              "\nWHERE `operation_id` = $obj->operation_id";
            db_exec($sql2); db_error();
          };
          
        case "0.30":
          $sql = "ALTER TABLE `operations`
                  ADD `pose_garrot` TIME AFTER `entree_bloc` ,
                  ADD `debut_op` TIME AFTER `pose_garrot` ,
                  ADD `fin_op` TIME AFTER `debut_op` ,
                  ADD `retrait_garrot` TIME AFTER `fin_op` ;";
          db_exec($sql); db_error();
        
        case "0.31":
          $sql = "ALTER TABLE `operations`" .
              "\nADD `salle_id` BIGINT AFTER `plageop_id` ," .
              "\nADD `date` DATE AFTER `salle_id` ;";
          db_exec($sql); db_error();
        
        case "0.32":
          $sql = "ALTER TABLE `operations`" .
              "ADD `venue_SHS` VARCHAR( 8 ) AFTER `chambre`;";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `operations` ADD INDEX ( `venue_SHS` );";
          db_exec( $sql ); db_error();
        
        case "0.33":
          $sql = "ALTER TABLE `operations`" .
              "ADD `code_uf` VARCHAR( 3 ) AFTER `venue_SHS`;";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `operations`" .
              "ADD `libelle_uf` VARCHAR( 40 ) AFTER `code_uf`;";
          db_exec( $sql ); db_error();

        case "0.34":
          $sql = "ALTER TABLE `operations`
                  ADD `entree_reveil` TIME AFTER `sortie_bloc` ,
                  ADD `sortie_reveil` TIME AFTER `entree_reveil` ;";
          db_exec($sql); db_error();
        case "0.35":
          return true;
	  }
      return false;
	}

	function install() {
		$sql = "CREATE TABLE operations ( " .
			"  operation_id bigint(20) unsigned NOT NULL auto_increment" .
			", pat_id bigint(20) unsigned NOT NULL default '0'" .
			", chir_id bigint(20) unsigned NOT NULL default '0'" .
			", plageop_id bigint(20) unsigned NOT NULL default '0'" .
			", CIM10_code varchar(5) default NULL" .
			", CCAM_code varchar(7) default NULL" .
			", cote enum('droit','gauche','bilatéral','total') NOT NULL default 'total'" .
			", temp_operation time NOT NULL default '00:00:00'" .
			", time_operation time NOT NULL default '00:00:00'" .
			", examen text" .
		  ", materiel text" .
      ", commande_mat enum('o', 'n') NOT NULL default 'n'" .
      ", info enum('o','n') NOT NULL default 'n'" .
      ", date_anesth date NOT NULL default '0000-00-00'" .
      ", time_anesth time NOT NULL default '00:00:00'" .
      ", type_anesth tinyint(4) default NULL" .
      ", date_adm date NOT NULL default '0000-00-00'" .
      ", time_adm time NOT NULL default '00:00:00'" .
      ", duree_hospi tinyint(4) unsigned NOT NULL default '0'" .
      ", type_adm enum('comp','ambu','exte') default 'comp'" .
      ", chambre enum('o','n') NOT NULL default 'o'" .
      ", ATNC enum('o','n') NOT NULL default 'n'" .
      ", rques text" .
      ", rank tinyint(4) NOT NULL default '0'" .
      ", admis enum('n','o') NOT NULL default 'n'" .
      ", PRIMARY KEY  (operation_id)" .
      ", UNIQUE KEY operation_id (operation_id)" .
      ") TYPE=MyISAM;";
    db_exec( $sql ); db_error();
    
    $this->upgrade("all");
    return null;
  }
}

?>