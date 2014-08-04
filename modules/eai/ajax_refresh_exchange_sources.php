<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$source_class = CValue::get('source_class');

$class = new $source_class;

$sources = $class->loadList();
foreach ($sources as $_source) {
  if ($_source instanceof CSourcePOP) {
    $_source->loadRefMetaObject();
  }
}

$smarty = new CSmartyDP();
$smarty->assign("_sources", $sources);
$smarty->assign("name"    , $source_class);
$smarty->display("inc_vw_sources.tpl");