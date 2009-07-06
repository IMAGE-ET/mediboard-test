<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
  
// Global hosting settings
$dPconfig["root_dir"] = "D:/Sites/Mediboard.org/demo";  // No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["company_name"] = "mediboard.org";
$dPconfig["page_title"] = "Mediboard SIH";
$dPconfig["base_url"] = "http://www.mediboard.org/demo";
$dPconfig["site_domain"] = "mediboard.org";
$dPconfig["offline"] = "0";
$dPconfig["mb_id"] = "";

$dPconfig["dbtype"] = "mysql";   // ONLY MySQL is supported at present

$dPconfig["date"] = "%d/%m/%Y";
$dPconfig["time"] = "%Hh%M";
$dPconfig["datetime"] = "%d/%m/%Y %Hh%M";
$dPconfig["longdate"] = "%A %d %B %Y";
$dPconfig["longtime"] = "%H heures %M minutes";

$dPconfig["graph_engine"] = "jpgraph";
$dPconfig["graph_svg"] = "non";

// Standard database config
$dPconfig["db"]["std"] = array(
  "dbtype" => "mysql",      // Change to use another dbms
  "dbhost" => "localhost",  // Change to connect to a distant Database
  "dbname" => "mediboard",  // Change to match your Mediboard Database Name
  "dbuser" => "mbadmin",    // Change to match your Username
  "dbpass" => "adminmb",    // Change to match your Password
);

// Other databases config

// CIM10
$dPconfig["db"]["cim10"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "cim10",
  "dbuser" => "CIM10Admin",
  "dbpass" => "AdminCIM10",
);

$dPconfig["db"]["ccamV2"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "ccamV2",
  "dbuser" => "CCAMAdmin",
  "dbpass" => "AdminCCAM",
);

// GHS
$dPconfig["db"]["GHS1010"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "ghs1010",
  "dbuser" => "GHSAdmin",
  "dbpass" => "AdminGHS",
);

// Codes INSEE
$dPconfig["db"]["INSEE"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "INSEE",
  "dbuser" => "INSEEAdmin",
  "dbpass" => "AdminINSEE",
);

// Transit
$dPconfig["db"]["Transit"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "Transit",
  "dbuser" => "TransitAdmin",
  "dbpass" => "AdminTransit",
);

// Sherpa
$dPconfig["db"]["sherpa"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "CPTransit",
  "dbuser" => "CPTAdmin",
  "dbpass" => "AdminCPT",
);
  
// BCB
$dPconfig["db"]["bcb1"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "bcb1",
  "dbuser" => "",
  "dbpass" => "",
);

$dPconfig["db"]["bcb2"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "bcb2",
  "dbuser" => "",
  "dbpass" => "",
);

// BCBGES
$dPconfig["db"]["bcbges"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "bcbges",
  "dbuser" => "",
  "dbpass" => "",
);

// you can add as much databases as you want by using
//$dPconfig["db"]["dsn"] = array(
//  "dbtype" => "dbtype",
//  "dbhost" => "dbhost",
//  "dbname" => "dbname",
//  "dbuser" => "dbuser",
//  "dbpass" => "dbpass",
//);

// Currency symbol (html entities allowed)
$dPconfig["currency_symbol"] = "&euro;";

// Enable if you want to hide confidentials fields
$dPconfig["hide_confidential"] = false;

// Enable if you want to have a demo version interface
$dPconfig["demo_version"] = false;

// Warn when a translation is not found (for developers and tranlators)
$dPconfig["locale_warn"] = false;

// The string appended to untranslated string or unfound keys
$dPconfig["locale_alert"] = "^";

// Set to true to help analyse errors
$dPconfig["debug"] = true;

// Use mediboard in a read-only mode
$dPconfig["readonly"] = false;

// Choose shared memory handler [none|eaccelerator|apc]
$dPconfig["shared_memory"] = "none";

// Object handlers
$dPconfig["object_handlers"] = array (
//  "CMyObjectHandler" => true,
);

// Compatibility mode
$dPconfig["interop"]["mode_compat"] = "default";
$dPconfig["interop"]["base_url"]    = "http://myserver/mypage";

// File parsers to return indexing information about uploaded files
$dPconfig["ft"] = array(
  "default" => "/usr/bin/strings",
  "application/msword" => "/usr/bin/strings",
  "text/html" => "/usr/bin/strings",
  "application/pdf" => "/usr/bin/pdftotext",
);

/********* 
 * Mediboard module-specific active configuration settings
 */

// HPRIM export FTP settings
$dPconfig["dPinterop"]["hprim_export"] = array (
  "validation"     => 1,
  "hostname"       => "",
  "username"       => "",
  "userpass"       => "",
  "fileprefix"     => "",
  "fileextension"  => "XML",
  "filenbroll"     => 2,
);

$dPconfig["dPplanningOp"]["COperation"] = array (
  "duree_deb"        => "0",
  "duree_fin"        => "10",
  "hour_urgence_deb" => "0",
  "hour_urgence_fin" => "23",
  "min_intervalle"   => "15",
  "locked"           => "0",
  "horaire_voulu"    => "0",
  "verif_cote"       => "0",
);
  
$dPconfig["dPplanningOp"]["CSejour"] = array (
  "patient_id"          => "1",
  "modif_SHS"           => "1",
  "heure_deb"           => "0",
  "heure_fin"           => "23",
  "min_intervalle"      => "15",
  "heure_entree_veille" => "17",
  "heure_entree_jour"   => "10",
  "heure_sortie_ambu"   => "18",
  "heure_sortie_autre"  => "8",
  "locked"              => "0",
  "tag_dossier"         => "",
  "service_id_notNull"  => "0",
);

$dPconfig["dPsalleOp"] = array(
  "mode_anesth" => "0",
  "max_add_minutes" => "10",
  "max_sub_minutes" => "30",
  "COperation" => array(
    "mode"        => "0",
    "modif_salle" => "0",
    "modif_actes" => "oneday",
  ),
  "CActeCCAM" => array(
    "contraste"       => "0",
    "alerte_asso"     => "1",
    "tarif"           => "0",
    "signature"       => "0",
    "openline"        => "0",
    "modifs_compacts" => "0",
    "commentaire"     => "1",
  ),
  "CDossierMedical" => array (
    "DAS" => "0",
  ),
  "CReveil"			=> array (
  	"multi_tabs_reveil"	  => "1",
  ),
  "CDailyCheckList" => array(
    "active" => "0"
  )
);
   
$dPconfig["dPhospi"]["pathologies"] = 1;

$dPconfig["dPcabinet"] = array(
  "keepchir" => 1,
  "CPlageconsult" => array (
	  "hours_start"      => "8",
	  "hours_stop"       => "20",
	  "minutes_interval" => "15",
	),
	"CConsultAnesth" => array(
	  "feuille_anesthesie" => "print_fiche",
	  /* Format des champs auto :
	   * %N - Nom praticien interv
	   * %P - Prénom praticien interv
	   * %S - Initiales praticien interv
	   * %L - Libellé intervention
	   * %I - Jour intervention
	   * %i - Heure intervention
	   * %E - Jour d'entrée
	   * %e - Heure d'entrée
	   * %T - Type de séjour (A, O, E...)
	   */
    "format_auto_motif" => "Pré-anésth. %I %L %S",
    "format_auto_rques" => "%T %E %e",
    "view_premedication" => "0"
	),
	"CPrescription" => array (
    "view_prescription" => "0"
  )
);

$dPconfig["dPbloc"]["CPlageOp"] = array (
  "hours_start"      => "8",
  "hours_stop"       => "20",
  "minutes_interval" => "15",
  "plage_vide"       => "0",
  "libelle_ccam"     => "1", 
  "locked"           => "1",
  "planning" => array (
  	"col1" => "interv",
		"col2" => "sejour",
		"col3" => "patient"
  ),
  "chambre_operation" => "0",
  "impression_suivi"  => "0",
  "id_salles_impression" => "",
);

$dPconfig["dPImeds"] = array (
  "url" => "http://localhost/mediboard/modules/dPImeds/demo/listedossiers.aspx.htm",
  "remote_url" => "",
  "soap_url" => "",
);

$dPconfig["dPfiles"] = array (
  "nb_upload_files" => "1",
  "upload_max_filesize" => "2M",
  "system_sender" => "",
  "rooturl" => "",
  "CFilesCategory" => array(
    "show_empty" => "1",
  ),
);

$dPconfig["dPpatients"] = array (
  "CPatient" => array (
    "tag_ipp" => "",
    "identitovigilence" => "nodate",
    "merge_only_admin" => "0",
  ),
  
  "CAntecedent" => array (
    "types" => "med|alle|trans|obst|chir|fam|anesth|gyn",
    "appareils" => "cardiovasculaire|digestif|endocrinien|neuro_psychiatrique|pulmonaire|uro_nephrologique"
  ),
  
  "CTraitement" => array (
    "enabled" => "1",
  ),
  
  "intermax" => array (
    "auto_watch" => "0",
  ),
  
  "CDossierMedical" => array (
    "diags_static_cim" => "1"
  ),
);

$dPconfig["dPsante400"] = array (
  "nb_rows" => "5",
  "mark_row" => "0",
  "cache_hours" => "1",
  "dsn" => "",
  "user" => "",
  "pass" => "",
  "group_id" => "",
);

$dPconfig["dPlabo"] = array (
  "CCatalogueLabo" => array (
    "remote_name" => "LABO",
    "remote_url" => "http://localhost/mediboard/modules/dPlabo/remote/catalogue.xml",
  ),
  
  "CPackExamensLabo" => array (
    "remote_url" => "http://localhost/mediboard/modules/dPlabo/remote/pack.xml",
  )
  ,
  "CPrescriptionLabo" => array (
    "url_ftp_prescription" => "",
    "url_ws_id_prescription" => "",
    "pass_ws_id_prescription" => "",
    "login_ftp_prescription" => "",
    "pass_ftp_prescription" => "",
  ),
);

$dPconfig["dPmedicament"] = array (
  "CBcbObject" =>  array (
    "dsn" => "bcb",
  ),
  
  "CBcbProduit" => array (
    "use_cache" => "0",
  ),
  
  "CBcbClasseATC" => array (
    "niveauATC" => "2",
  ),
  
  "CBcbClasseTherapeutique" => array (
    "niveauBCB" => "2",
  ),
  
  "CBcbProduitLivretTherapeutique" => array(
    "product_category_id" => "",
  ),
);


$dPconfig["dPprescription"] = array (
  "CPrescription" => array (
    "show_unsigned_lines" => "1",
    "add_element_category" => "0",
    "time_print_ordonnance" => "2",
    "time_alerte_modification" => "2",
    "scores" => array(
      "interaction" => array(
        "niv1" => "1",
        "niv2" => "1",
        "niv3" => "2",
        "niv4" => "2"
      ),
      "posoqte" => array(
        "niv10" => "0",
        "niv11" => "1",
        "niv12" => "2",
      ),
      "posoduree" => array(
        "niv20" => "0",
        "niv21" => "1",
        "niv22" => "2",
      ),
      "profil" => array( 
        "niv0" => "1",
        "niv1" => "1",
        "niv2" => "1",
        "niv9" => "1",
        "niv30" => "1",
        "niv39" => "1"
      ),
      "allergie" => "2",
      "IPC" => "2",
      "hors_livret" => "1"
    ),
  ),
  
  "CCategoryPrescription" => array (
    "dmi" => array(
      "phrase" => "Bon pour",
      "unite_prise" => "dispositif(s)"
    ),
    "anapath" => array(
      "phrase" => "Faire pratiquer",
      "unite_prise" => "examen(s)"
    ),
    "biologie" => array(
      "phrase" => "Faire pratiquer",
      "unite_prise" => "examen(s)"
    ),
    "imagerie" => array(
      "phrase" => "Faire pratiquer",
      "unite_prise" => "cliché(s)"
    ),
    "consult" => array(
      "phrase" => "",
      "unite_prise" => "consultation(s)"
    ),
    "kine" => array(
      "phrase" => "Faire pratiquer",
      "unite_prise" => "séance(s)"
    ),
    "soin" => array(
      "phrase" => "Pratiquer",
      "unite_prise" => "soin(s)"
    ),
    "dm" => array(
      "phrase" => "Délivrer",
      "unite_prise" => "dispositif(s)"
    )
  ),
  
  "CMomentUnitaire" => array(
    "principaux" => "0"
  ),
    
  "CPrisePosologie" => array(
    "select_poso_bcb" => "1",
    "show_poso_bcb" => "0",
    "heures" => array(
      "tous_les" => "14",
      "fois_par" => array(
        "1" => "08",
        "2" => "08|14",
        "3" => "08|14|18",
        "4" => "08|10|14|18",
        "5" => "08|10|12|14|16",
        "6" => "08|10|12|14|16|18"
      ),
      "matin" => array(
        "min" => "06",
        "max" => "13"
      ),
      "soir" => array(
        "min" => "14",
        "max" => "21"
      ),
      "nuit" => array(
        "min" => "22",
        "max" => "05"
      )
    ),
    "semaine" => array(
      "1" => "lundi",
      "2" => "lundi|mercredi",
      "3" => "lundi|mercredi|vendredi",
      "4" => "lundi|mercredi|vendredi|samedi"
    )
  ),
  
  "CAdministration" => array(
    "hors_plage" => "0"
  )
);

$dPconfig["system"] = array(
  "type_telephone" => "france",
  "reverse_proxy"  => "0.0.0.0",
);

$dPconfig["sherpa"] = array (
  "import_segment" => "100",
  "import_id_min" => "",
  "import_id_max" => "",
);

$dPconfig["dPqualite"] = array (
  "CDocGed" => array(
    "_reference_doc" => 0,
  ),
  "CChapitreDoc" => array(
    "profondeur" => 1,
  ),
);

$dPconfig["admin"] = array (
  "CUser" => array(
    "strong_password" => "0",
    "max_login_attempts" => "5",
  ),
);

$dPconfig["dPccam"] = array (
  "CCodeCCAM" => array(
    "use_cache" => "1",
  ),
  "CCodable" => array(
    "use_getMaxCodagesActes" => "1",
  ),
);

$dPconfig["hprim21"] = array (
  "CHprim21Reader" => array(
    "hostname"      => "",
    "username"      => "",
    "userpass"      => "",
    "fileextension" => "hpr",
  ),
);

$dPconfig["dPurgences"] = array (
  "old_rpu"          => "1",
  "rpu_warning_time" => "00:20:00",
  "rpu_alert_time"   => "01:00:00",
);

$dPconfig["dPstock"] = array (
  "CProductOrder" => array(
    "order_number_format" => "%y%m%d%H%M%S%id",
  ),
  "CProductStockGroup" => array(
    "infinite_quantity" => 0
  ),
  "CProductStockService" => array(
    "infinite_quantity" => 0
  )
);

$dPconfig["dPpmsi"] = array(
  "systeme_facturation" => "",
); 

$dPconfig["bloodSalvage"] = array (
    "inLivretTherapeutique" => "1",
    "AntiCoagulantList" => "",
);

$dPconfig["ecap"] = array (
  "dhe" => array (
    "rooturl" => "",
  ),
  "soap" => array (
    "rooturl" => "",
    "user" => "",
    "pass" => "",
  ),
);

$dPconfig["pharmacie"] = array (
  "dispensation_schedule" => "024"
);

$dPconfig["dmi"] = array (
  "CDMI" => array(
    "product_category_id" => "",
    "active" => 0
  ),
  "CDM" => array(
    "product_category_id" => "",
    "active" => 0
  )
);

$dPconfig["sip"] = array (
	"wsdl_mode" => 1,
  "server" => 0,
  "export_segment" => "100",
  "export_id_min" => "",
  "export_id_max" => "",
  "batch_count" => "10",
);

$dPconfig["webservices"] = array (
  "connection_timeout" => 5,
);

// Inclusion des fichiers de config de chaque module
$config_files = glob("./modules/*/config.php");
foreach ($config_files as $file) {
  require_once($file);
}

?>