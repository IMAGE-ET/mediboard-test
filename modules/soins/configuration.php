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
          "hide_old_line" => "bool default|0"
        )
      )
    )
  )
);