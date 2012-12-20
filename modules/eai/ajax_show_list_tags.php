<?php /* $Id $ */

/**
 * Show list tags EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$object_class = CValue::get("object_class");

$ds = CSQLDataSource::get("std");

$where = array(
  "object_class" => "= '$object_class'"
);

// Liste des tags pour un object_class
$req = new CRequest;
$req->addTable("id_sante400");
$req->addColumn("tag");
$req->addWhere($where);
$req->addGroup("tag");

$tags = CMbArray::pluck($ds->loadList($req->getRequest()), "tag");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("tags", $tags);
$smarty->display("inc_show_list_tags.tpl");
