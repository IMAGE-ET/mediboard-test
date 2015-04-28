<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPurgences" => array(
        "CRPU" => array(
          "impose_degre_urgence"            => "bool default|0",
          "impose_diag_infirmier"           => "bool default|0",
          "impose_motif"                    => "bool default|0",
          "impose_create_sejour_mutation"   => "bool default|0",
          "provenance_domicile_pec_non_org" => "bool default|0",
          "gestion_motif_sfmu"              => "enum list|0|1|2 default|1 localize",
          "motif_sfmu_accueil"              => "bool default|0",
          "provenance_necessary"            => "bool default|0",
          "imagerie_etendue"                => "bool default|0",
          "display_motif_sfmu"              => "bool default|0",
          "defer_sfmu_diag_inf"             => "bool default|0",
          "diag_prat_view"                  => "bool default|0",
          "search_visit_days_count"         => "num default|1 min|0 max|15",
          "impose_lit_service_mutation"     => "bool default|0",
        ),
        "Display" => array(
          "check_cotation"  => "enum list|0|1 default|1 localize",
          "check_gemsa"     => "enum list|0|1|2  default|1 localize",
          "check_ccmu"      => "enum list|0|1|2  default|1 localize",
          "check_dp"        => "enum list|0|1|2  default|1 localize",
          "check_can_leave" => "enum list|0|1  default|1 localize",
        ),
        "send_RPU" => array(
          "max_patient" => "num",
          "totbox"      => "num min|0",
          "totdechoc"   => "num min|0",
          "totporte"    => "num min|0",
        ),
        "Print" => array(
          "gemsa" => "bool default|0",
        )
      ),
    ),
  )
);
