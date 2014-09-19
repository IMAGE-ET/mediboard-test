<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision:$
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPpmsi" => array(
        "display" => array(
          "see_recept_dossier" => "bool default|0",
        )
      )
    )
  )
);