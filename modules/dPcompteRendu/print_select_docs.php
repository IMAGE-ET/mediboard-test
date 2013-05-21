<?php

/**
 * Imprime les documents reliés à un objet
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
/** @var $object CMbObject */
$object->load($object_id);

$object->loadRefsDocs();
  
$smarty = new CSmartyDP();

$smarty->assign("documents", $object->_ref_documents);

$smarty->display("print_select_docs.tpl");
