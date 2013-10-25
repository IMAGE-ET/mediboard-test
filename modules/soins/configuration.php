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
        "bilan"=> array(
          "hour_before" => "num min|0 default|12",
          "hour_after"  => "num min|0 default|24"
        ),
        "offline_sejour" => array(
          "period" => "num min|0 default|72"
        ),
      )
    )
  )
);