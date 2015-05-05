<?php

/**
 * $Id$
 *
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$group_id = CValue::get("group_id");

CStoredObject::$useObjectCache = false;

$backrefs_tree = array(
  "CGroups" => array(
    "functions",
    "blocs",
    "services",
    "secteurs",
  ),
  "CFunctions" => array(
    "users",
  ),
  "CBlocOperatoire" => array(
    "salles",
  ),
  "CService" => array(
    "chambres",
  ),
  "CChambre" => array(
    "lits",
  ),
);

$fwdrefs_tree = array(
  "CMediusers" => array(
    "user_id",
  ),
);

$group = CGroups::get($group_id);

$export = new CMbObjectExport($group, $backrefs_tree);
$export->empty_values = false;
$export->setForwardRefsTree($fwdrefs_tree);
$export->streamXML();