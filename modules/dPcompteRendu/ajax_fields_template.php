<?php 

/**
 * field template selector modal
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkRead();

$object_class = CValue::get("class");

$object = new $object_class;

$template = new CTemplateManager();
$object->fillTemplate($template);

//smarty
$smarty = new CSmartyDP();
$smarty->assign("template", $template);
$smarty->assign("class",    $object_class);
$smarty->display("vw_fields_template_selector.tpl");

