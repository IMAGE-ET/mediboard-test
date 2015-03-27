<?php 

/**
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPbloc" => array(
        "CPlageOp" => array(
          "original_owner" => "bool default|0"
        ),
        "mode_presentation" => array(
          "salles_count"   => "num default|4",
          "refresh_period" => "num default|30",
        ),
        "printing" => array(
          "format_print" => "enum localize list|standard|advanced default|standard"
        )
      )
    )
  )
);