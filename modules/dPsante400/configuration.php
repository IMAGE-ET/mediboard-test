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

global $locales;

$types = array();
foreach (CSejour::$types as $_type) {
  $types[$_type] = "str default|$_type";

  $tr = CAppUI::tr("CSejour.type.$_type");
  $locales["config-dPsante400-CIncrementer-type_sejour-$_type"]      = $tr;
  $locales["config-dPsante400-CIncrementer-type_sejour-$_type-desc"] = $tr;
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