<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPcabinet"] = array(
  "keepchir" => 1,
  "display_nb_consult" => "cab",
  "CPlageconsult" => array (
    "hours_start"      => "8",
    "hours_stop"       => "20",
    "minutes_interval" => "15",
  ),
  "CConsultation" => array(
    "use_last_consult"      => 0,
    "show_examen"           => 1,
    "show_histoire_maladie" => 0,
    "show_conclusion"       => 0,
    "attach_consult_sejour" => 0,
    "create_consult_sejour" => 0,
    "minutes_before_consult_sejour" => 1,
    "hours_after_changing_prat"     => 0,
    "fix_doc_edit"                  => 0,
    "search_sejour_all_groups"      => 0,
    "consult_readonly"             => 0,
    "consult_facture"              => 0,
    "surbooking_readonly"   => 1
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
    "format_auto_motif"  => "Pré-anésth. %I %L %S",
    "format_auto_rques"  => "%T %E %e",
    "view_premedication" => "0",
    "show_facteurs_risque" => "0",
    "show_mallampati" => "0"
  ),
  "CPrescription" => array (
    "view_prescription" => "0"
  )
);

?>