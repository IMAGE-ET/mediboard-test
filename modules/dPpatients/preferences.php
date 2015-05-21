<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Préférences par Module
CPreferences::$modules["dPpatients"] = array (
  "VitaleVisionDir", 
  "LogicielLectureVitale",
  "vCardExport",
  "medecin_cps_pref",
  "sort_atc_by_date",
  'update_patient_from_vitale_behavior',
  'new_date_naissance_selector',
  'constantes_show_comments_tooltip',
  'constantes_show_view_tableau',
);
