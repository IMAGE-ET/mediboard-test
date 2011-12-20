<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
  
$dPconfig["dPprescription"] = array (
  "CPrescription" => array (
    "show_unsigned_lines" => "1",
		"show_unsigned_med_msg"   => "0",
		"show_categories_plan_soins" => "1",
		"show_inscription" => "0",
    "add_element_category" => "0",
    "time_print_ordonnance" => "2",
    "time_alerte_modification" => "2",
		"prescription_suivi_soins" => "0",
		"max_time_modif_suivi_soins" => "12",
    "qte_obligatoire_inscription" => "0",
    "max_details_result" => "20",
    "nb_days_prescription_current" => "1",
    "display_cat_for_elt" => "0",
		"show_PCEA" => "0",
		"duration_in_hours" => "0",
    "show_ccam_bons" => "1",
		"prolongation_time" => "0",
		"nb_days_relative_end" => "3",
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
        "niv0"  => "1",
        "niv1"  => "1",
        "niv2"  => "1",
        "niv9"  => "1",
        "niv30" => "1",
        "niv39" => "1"
      ),
      "allergie" => "2",
      "IPC" => "2",
      "hors_livret" => "1"
    ),
	  "droits_infirmiers_med"      => "0",
		"droits_infirmiers_med_elt" => "0",
		"droits_infirmiers_anapath"  => "0",
		"droits_infirmiers_biologie" => "0",
		"droits_infirmiers_imagerie" => "0",
		"droits_infirmiers_consult"  => "0",
		"droits_infirmiers_kine"     => "0",
		"droits_infirmiers_soin"     => "0",
		"droits_infirmiers_dm"       => "0",
		"droits_infirmiers_dmi"      => "0",
		"droits_infirmiers_ds"      => "0",
		"show_chapter_med"      => "1",
		"show_chapter_med_elt" => "0",
    "show_chapter_anapath"  => "1",
    "show_chapter_biologie" => "1",
    "show_chapter_imagerie" => "1",
    "show_chapter_consult"  => "1",
    "show_chapter_kine"     => "1",
    "show_chapter_soin"     => "1",
    "show_chapter_dm"       => "1",
    "show_chapter_dmi"      => "1",
		"show_chapter_ds"       => "0",
		"preselect_livret"      => "1",
		"use_libelle_livret"    => "0",
		"manual_planif"         => "0",
		"role_propre"           => "0",
  ),
  
  "CCategoryPrescription" => array (
    "dmi" => array(
      "phrase"      => "Bon pour",
      "unite_prise" => "dispositif(s)",
			"fin_sejour"  => "0"
    ),
    "anapath" => array(
      "phrase"      => "Faire pratiquer",
      "unite_prise" => "examen(s)",
			"fin_sejour"  => "0"
    ),
    "biologie" => array(
      "phrase"      => "Faire pratiquer",
      "unite_prise" => "examen(s)",
			"fin_sejour"  => "0"
    ),
    "imagerie" => array(
      "phrase"      => "Faire pratiquer",
      "unite_prise" => "cliché(s)",
      "fin_sejour"  => "0"
    ),
    "consult" => array(
      "phrase"      => "",
      "unite_prise" => "consultation(s)",
      "fin_sejour"  => "0"
    ),
    "kine" => array(
      "phrase"      => "Faire pratiquer",
      "unite_prise" => "séance(s)",
      "fin_sejour"  => "0"
    ),
    "soin" => array(
      "phrase"      => "Pratiquer",
      "unite_prise" => "soin(s)",
      "fin_sejour"  => "0"
    ),
    "dm" => array(
      "phrase"      => "Délivrer",
      "unite_prise" => "dispositif(s)",
      "fin_sejour"  => "0"
    ),
		"ds" => array(
		  "phrase"      => "",
			"unite_prise" => "",
      "fin_sejour"  => "0"
		),
		"med_elt" => array(
		  "phrase"      => "",
      "unite_prise" => "",
      "fin_sejour"  => "0"
    ),
    "show_description" => "0",
    "show_header" => "0",
    "show_only_cible" => "0"
  ),
  
  "CMomentUnitaire" => array(
    "principaux" => "0",
		"poso_lite" => array(
		  "matin" => "1",
			"midi" => "1",
			"apres_midi" => "0",
			"soir" => "1",
			"coucher" => "0"
		)
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
?>