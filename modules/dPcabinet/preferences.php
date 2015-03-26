<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Préférences par Module
CPreferences::$modules["dPcabinet"] = array (
  "AFFCONSULT",
  "MODCONSULT",
  "AUTOADDSIGN",
  "DefaultPeriod",
  "viewWeeklyConsultCalendar",
  "DossierCabinet",
  "simpleCabinet",
  "ccam_consultation",
  "view_traitement",
  "autoCloseConsult",
  "resumeCompta",
  "showDatesAntecedents",
  "dPcabinet_show_program",
  "pratOnlyForConsult",
  "displayDocsConsult",
  "displayPremedConsult",
  "displayResultsConsult",
  "choosePatientAfterDate",
  "viewFunctionPrats",
  "viewAutreResult",
  "empty_form_atcd",
  "new_semainier",
  "height_calendar",
  "order_mode_grille",
  "create_dossier_anesth",
  "showIntervPlanning",
  "NbConsultMultiple",
  "use_acte_date_now",
  "multi_popups_resume",
  "allow_plage_holiday",
  "show_plage_holiday",
  "today_ref_consult_multiple",
  "dPcabinet_displayFirstTab",
  "show_replication_duplicate",

  // take consultation for :
  "take_consult_for_chirurgien",    // 1
  "take_consult_for_anesthesiste",  // 1
  "take_consult_for_medecin",       // 1
  "take_consult_for_infirmiere",    // le reste non
  "take_consult_for_reeducateur",
  "take_consult_for_sage_femme",
  "take_consult_for_dentiste",
  "take_consult_for_dieteticien"
);