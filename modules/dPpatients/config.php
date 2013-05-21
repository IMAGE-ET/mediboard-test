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

$dPconfig["dPpatients"] = array (
  "CPatient"    => array (
    "tag_ipp"           => "",
    "tag_ipp_group_idex"=> "",
    "tag_ipp_trash"     => "trash_",
    "tag_conflict_ipp"  => "conflict_",
    "identitovigilence" => "nodate",
    "multi_group"       => "limited",
    "merge_only_admin"  => "0",
    "extended_print"    => "0",
    "adult_age"         => "15",
    "limit_char_search" => "0",
    "check_code_insee"  => "1",
    "show_patient_link" => "0"
  ),

  "CAntecedent" => array (
    "types"     => "med|alle|trans|obst|chir|fam|anesth|gyn",
    "appareils" => "cardiovasculaire|digestif|endocrinien|neuro_psychiatrique|pulmonaire|uro_nephrologique",
  ),

  "CTraitement" => array (
    "enabled" => "1",
  ),

  "CDossierMedical" => array (
  ),

  "CConstantesMedicales" => array(
    "unite_ta" => "cmHg",
  ),

  "CMedecin" => array(
   "medecin_strict" => "0",
  ),

  "INSEE" => array(
   "france" => "1",
   "suisse" => "0",
  ),
);
