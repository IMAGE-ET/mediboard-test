<?php
/**
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "soins" => array(
        "dossier_soins" => array(
          "show_bouton_plan_soins" => "bool default|1",
        ),
        "bilan" => array(
          "hour_before" => "num min|0 default|12",
          "hour_after"  => "num min|0 default|24"
        ),
        "offline_sejour" => array(
          "period" => "num min|0 default|72"
        ),
        "plan_soins" => array(
          "period"     => "num min|1 default|7",
          "hour_matin" => "num min|0 max|23 default|8",
          "hour_midi"  => "num min|0 max|23 default|12",
          "hour_soir"  => "num min|0 max|23 default|18",
          "hour_nuit"  => "num min|0 max|23 default|22"
        ),
        "synthese" => array(
          "transmission_date_limit" => "bool default|0"
        ),
        "suivi" => array(
          "hide_old_line"   => "bool default|0"
        ),
        "CLit"  => array(
          "align_right"     => "bool default|1",
        ),
        "CConstantesMedicales"  => array(
          "constantes_show" => "bool default|1",
        ),
        "Pancarte"  => array(
          "transmissions_hours"           => "enum list|12|24|36|48 default|24",
          "soin_refresh_pancarte_service" => "enum localize list|none|10|20|30 default|20",
        ),
        "Transmissions"  => array(
          "cible_mandatory_trans" => "bool default|0",
          "trans_compact"         => "bool default|0",
        ),
        "Sejour"  => array(
          "refresh_vw_sejours_frequency" => "enum localize list|disabled|600|1200|1800 default|disabled",
        ),
        "Other"  => array(
          "show_charge_soins"           => "bool default|0",
          "max_time_modif_suivi_soins"  => "num min|0 max|23 default|12",
          "show_only_lit_bilan"         => "bool default|0",
          "ignore_allergies"            => "str default|aucun|ras|r.a.s.|0",
          "vue_condensee_dossier_soins" => "bool default|0",
        ),
      )
    )
  )
);