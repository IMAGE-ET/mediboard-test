<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "admin" => array(
        "CBrisDeGlace" => array(
          "enable_bris_de_glace" => "bool default|0"
        ),
        "CLogAccessMedicalData" => array(
          "enable_log_access"      => "bool default|0",
          "round_datetime"         => "enum list|1m|10m|1h|1d default|1h localize",
        ),
      )
    )
  )
);