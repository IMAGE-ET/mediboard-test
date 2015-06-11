<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPprescription
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

$types = array();
foreach (CSejour::$types as $_type) {
  $types[$_type] = "str default|$_type";
}

CConfiguration::register(
  array(
    //"CService CGroups.group_id"
    "CGroups" => array(
      "dPsante400" => array(
        "CIdSante400" => array(
          "add_ipp_nda_manually" => "bool default|0",
          "admit_ipp_nda_obligatory" => "bool default|0",
        ),
        "CIncrementer" => array(
          "type_sejour" => $types
        ),
        "CDomain" => array(
          "group_id_pour_sejour_facturable" => "num",
        )
      ),
    ),
  )
);