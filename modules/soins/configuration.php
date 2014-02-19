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
        "bon_radio" => array(
          "manage_orm" => "bool default|0",
        ),
        "bilan" => array(
          "hour_before" => "num min|0 default|12",
          "hour_after"  => "num min|0 default|24"
        ),
        "offline_sejour" => array(
          "period" => "num min|0 default|72"
        ),
        "plan_soins" => array(
          "period" => "num min|1 default|7"
        ),
      )
    )
  )
);