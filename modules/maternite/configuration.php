<?php 

/**
 * $Id$
 *  
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "maternite" => array(
        "CGrossesse" => array(
          "min_check_terme"  => "num default|7",
          "max_check_terme"  => "num default|21",
          "lock_partogramme" => "bool default|0",
        ),
        "CNaissance" => array(
          "num_naissance"   => "num default|1"
        )
      )
    )
  )
);