<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["dPhospi"] = array (
  "pathologies" => 0,
  "tag_service" => "",
  "stats_for_all" => 0,
  "nb_hours_trans" => 1,
  "hour_limit" => "16:00:00",
  "default_service_types_sejour" => array(
    "comp"       => "",
    "ambu"       => "",
    "exte"       => "",
    "seances"    => "",
    "ssr"        => "",
    "psy"        => "",
    "urg"        => "",
    "consult"    => ""
  ),
  
  "show_age_patient" => "0",
  "max_affectations_view" => "480",
  "use_vue_topologique" => "0",
  "nb_colonnes_vue_topologique" => 10,
  "hide_alertes_temporel" => "0",
  "show_age_sexe_mvt"     => "0",
  "show_hour_anesth_mvt"  => "0",
  "show_retour_mvt"       => "0",
  "show_collation_mvt"    => "0",
  "show_sortie_mvt"       => "0",
  "show_uf"               => "1",
  "nb_days_prolongation"  => "30",
  "show_realise"          => "1",
  "show_souhait_placement" => "0",

  "CLit" => array(
    "prefixe"         => "",
    "show_in_tableau" => 0,
    "alt_icons_sortants" => 0,
    "tag"     => ""
  ),
  "CChambre" => array(
    "prefixe" => "",
    "tag"     => ""
  ),
  "CMovement" => array(
    "tag" => ""
  ),

  "colors" => array(
    "comp"      => "fff",
    "ambu"      => "faa",
    "exte"      => "afa",
    "seances"   => "68f",
    "ssr"       => "ffcc66",
    "psy"       => "ff66ff",
    "urg"       => "ff6666",
    "consult"   => "cfdfff",
    "default"   => "cccccc",
    "recuse"    => "ffff66",
  )
);

