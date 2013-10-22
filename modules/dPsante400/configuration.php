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
        "CIncrementer" => array(
          "type_sejour" => $types
        ),
      ),
    ),
  )
);