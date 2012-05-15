<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPplanningOp"]= array(
  "COperation" => array (
	  "easy_horaire_voulu" => "1",
	  "easy_materiel"      => "0",
	  "easy_remarques"     => "0",
	  "easy_regime"        => "1",
    "easy_accident"      => "0",
    "easy_assurances"    => "0",
	  "duree_deb"          => "0",
	  "duree_fin"          => "10",
	  "hour_urgence_deb"   => "0",
	  "hour_urgence_fin"   => "23",
	  "min_intervalle"     => "15",
	  "locked"             => "0",
	  "horaire_voulu"      => "0",
    "use_ccam"           => "1",
	  "verif_cote"         => "0",
	  "delete_only_admin"  => "1",
    "duree_preop_adulte" => "45",
    "duree_preop_enfant" => "15",
	),
  "CSejour" => array (
    "check_collisions"    => "date",
    "easy_cim10"          => "0",
    "easy_service"        => "0",
	  "easy_chambre_simple" => "1",
	  "easy_ald_cmu"        => "0",
	  "patient_id"          => "1",
	  "entree_modifiee"           => "1",
	  "heure_deb"           => "0",
	  "heure_fin"           => "23",
	  "min_intervalle"      => "15",
	  "heure_entree_veille" => "17",
	  "heure_entree_jour"   => "10",
	  "heure_sortie_ambu"   => "18",
	  "heure_sortie_autre"  => "8",
	  "locked"              => "0",
	  "tag_dossier"         => "",
	  "tag_dossier_group_idex"=> "",
	  "tag_dossier_pa"      => "pa_",
	  "tag_dossier_cancel"  => "cancel_",
	  "tag_dossier_trash"   => "trash_",
    "blocage_occupation"  => "0",
	  "service_id_notNull"  => "0",
    "consult_accomp"      => "0",
    "accident"            => "0",
    "assurances"          => "0",
	  "fix_doc_edit"        => "0",
	  "sortie_prevue"       => array(
	    "comp"    => "24",
	    "ambu"    => "04",
	    "exte"    => "04",
	    "seances" => "04",
	    "ssr"     => "24",
	    "psy"     => "24",
	    "urg"     => "24",
	    "consult" => "04",
	  ),
	  "delete_only_admin"   => "1",
	  "max_cancel_time"     => "0",
	  "hours_sejour_proche" => "48",
	  "show_modal_identifiant"    => "0",
	  "show_discipline_tarifaire" => "0",
    "show_type_pec" => "0"
	),
);

?>