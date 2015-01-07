<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$segment_name = CValue::get("segment_name");
$version      = CValue::get("version");
$extension    = CValue::get("extension");
$message      = CValue::get("message");
$profil       = CValue::get("profil");

$trans         = new CHL7v2Transformation($version, $extension, $message);
$tree_fields   = $trans->getFieldsTree($segment_name);
$tree_segments = $trans->getSegments();

$smarty = new CSmartyDP();

$smarty->assign("profil"       , $profil);
$smarty->assign("version"      , $version);
$smarty->assign("extension"    , $extension);
$smarty->assign("message"      , $message);
$smarty->assign("tree_fields"  , $tree_fields);
$smarty->assign("tree_segments", $tree_segments);

$smarty->display("inc_hl7v2_transformation_fields.tpl");