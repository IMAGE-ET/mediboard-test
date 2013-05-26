<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Chargement des fichiers
$object->loadRefsFiles();
foreach ($object->_ref_files as $_file) {
  $_file->loadRefCategory();
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("count_object", count($object->_ref_files));
$smarty->display("inc_vw_list_files.tpl");
