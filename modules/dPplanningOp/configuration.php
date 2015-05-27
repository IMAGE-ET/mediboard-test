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
      "dPplanningOp" => array(
        "CSejour" => array(
          "pass_to_confirm"               => "bool default|0",
          'entree_pre_op_ambu'            => 'bool default|0',
          'use_charge_price_indicator'    => 'enum list|no|opt|obl localize default|no',
          "required_destination"          => "bool default|0",
          "required_from_when_transfert"  => "bool default|0",
          "required_mode_entree"          => "bool default|0",
          "required_uf_soins"             => "enum list|no|opt|obl localize default|no",
          "ssr_not_collides"              => "bool default|0",
        ),
        "COperation" => array(
          "multiple_label"                => "bool default|0",
        )
      )
    )
  )
);