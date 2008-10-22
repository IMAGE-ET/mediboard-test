<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Thomas Despoix
 */


/********* 
 * Mediboard active configuration settings
 */
  
// Global hosting settings
$dPconfig["root_dir"] = "D:/Sites/Mediboard.org/demo";  // No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["company_name"] = "mediboard.org";
$dPconfig["page_title"] = "Mediboard SIH";
$dPconfig["base_url"] = "http://www.mediboard.org/demo";
$dPconfig["site_domain"] = "mediboard.org";
$dPconfig["offline"] = "0";

$dPconfig["dbtype"] = "mysql";   // ONLY MySQL is supported at present

$dPconfig["date"] = "%d/%m/%Y";
$dPconfig["time"] = "%Hh%M";
$dPconfig["datetime"] = "%d/%m/%Y %Hh%M";

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
$dPconfig["db"]["bcb"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "bcb",
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

// Choose shared memory handler [none|eaccelerator] up to now
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
  "horaire_voulu"    => "0"
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
);

$dPconfig["dPsalleOp"] = array(
  "COperation" => array(
    "mode"        => "0",
    "modif_salle" => "0",
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
  "max_add_minutes" => "10",
  "max_sub_minutes" => "30",
  "CReveil"			=> array (
  	"multi_tabs_reveil"	  => "1",
  ),
);
   
$dPconfig["dPhospi"]["pathologies"] = 1;

$dPconfig["dPcabinet"]["keepchir"]   = 1;
$dPconfig["dPcabinet"]["addictions"] = 0;
$dPconfig["dPcabinet"]["CPlageconsult"] = array (
  "hours_start"      => "8",
  "hours_stop"       => "20",
  "minutes_interval" => "15",
);
$dPconfig["dPcabinet"]["CConsultAnesth"]["feuille_anesthesie"] = "print_fiche";

$dPconfig["dPcabinet"]["CPrescription"] = array (
  "view_prescription" => "0"
);

$dPconfig["dPbloc"]["CPlageOp"] = array (
  "hours_start"      => "8",
  "hours_stop"       => "20",
  "minutes_interval" => "15",
  "plage_vide"       => "0",
  "libelle_ccam"     => "1", 
  "locked"          => "1",
  "planning" => array (
  	"col1" => "interv",
	"col2" => "sejour",
	"col3" => "patient"
  ),
  "chambre_operation" => "0",
);

$dPconfig["dPImeds"]["url"] = "http://localhost/listedossiers.aspx";

$dPconfig["dPfiles"] = array (
  "nb_upload_files" => "1",
  "upload_max_filesize" => "2M"
);

$dPconfig["graph_engine"] = "jpgraph";

$dPconfig["graph_svg"] = "non";


$dPconfig["dPpatients"] = array (
  "CPatient" => array (
    "tag_ipp" => "",
    "date_naissance" => "0",
    "merge_only_admin" => "0",
  ),
  
  "CAntecedent" => array (
    "types" => "med|alle|trans|obst|chir|fam|anesth|gyn",
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
  ),
  "CPrescriptionLabo" => array (
    "url_ftp_prescription" => "",
    "url_ws_id_prescription" => "",
    "pass_ws_id_prescription" => "",
    "login_ftp_prescription" => "",
    "pass_ftp_prescription" => "",
  ),
);

$dPconfig["dPmedicament"] = array (
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
    "add_element_category" => "0",
    "time_print_ordonnance" => "2",
    "infirmiere_borne_start" => "20",
    "infirmiere_borne_stop"  => "08"
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
    "heures_prise" => "02|06|08|12|14|18|22|24",
    "heures" => array(
      "tous_les" => "14",
      "fois_par" => array(
        "1" => "08",
        "2" => "08|14",
        "3" => "08|14|18",
        "4" => "08|10|14|18",
        "5" => "08|10|12|14|16",
        "6" => "08|10|12|14|16|18"
      )
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
  "old_rpu" => "1",
);

$dPconfig["dPstock"] = array (
  "CProductOrder" => array(
    "order_number_format" => "%y%m%d%H%M%S%id",
  ),
);

$dPconfig["dPpmsi"] = array(
  "systeme_facturation" => "",
); 

$dPconfig["bloodSalvage"] = array (
    "inLivretTherapeutique" => "1",
    "AntiCoagulantList" => "",
);
  
?>