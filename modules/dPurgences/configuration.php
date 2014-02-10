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
          "impose_degre_urgence" => "bool default|0",
          "impose_diag_infirmier" => "bool default|0",
          "impose_motif" => "bool default|0",
          "impose_create_sejour_mutation" => "bool default|0",
          "provenance_domicile_pec_non_org" => "bool default|0",
        ),
        "Display" => array(
          "check_cotation" => "enum list|0|1|2 default|1 localize",
          "check_gemsa" => "enum list|0|1|2  default|1 localize",
          "check_ccmu" => "enum list|0|1|2  default|1 localize",
          "check_dp" => "enum list|0|1|2  default|1 localize",
          "check_can_leave" => "enum list|0|1|2  default|1",
        )
      ),
    ),
  )
);
